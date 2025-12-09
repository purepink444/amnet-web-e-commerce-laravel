<?php

namespace App\Services\Batch;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class BatchProcessor
{
    /**
     * Process large datasets in optimized chunks
     *
     * @param Collection $items Items to process
     * @param int $chunkSize Size of each chunk
     * @param callable $processor Function to process each chunk
     * @param bool $continueOnError Whether to continue processing if a chunk fails
     * @return array Processing results
     */
    public function processInChunks(Collection $items, int $chunkSize = 1000, callable $processor, bool $continueOnError = false): array
    {
        $totalItems = $items->count();
        $totalChunks = (int) ceil($totalItems / $chunkSize);
        $processed = 0;
        $failed = 0;
        $errors = [];

        Log::info('Starting batch processing', [
            'total_items' => $totalItems,
            'chunk_size' => $chunkSize,
            'total_chunks' => $totalChunks,
        ]);

        $startTime = microtime(true);

        $items->chunk($chunkSize)->each(function ($chunk, $chunkIndex) use ($processor, &$processed, &$failed, &$errors, $continueOnError, $totalChunks) {
            $chunkNumber = $chunkIndex + 1;

            try {
                DB::beginTransaction();

                $processor($chunk);
                $processed += $chunk->count();

                DB::commit();

                Log::debug('Batch chunk processed successfully', [
                    'chunk' => $chunkNumber,
                    'total_chunks' => $totalChunks,
                    'chunk_size' => $chunk->count(),
                    'processed_so_far' => $processed,
                ]);

            } catch (Throwable $e) {
                DB::rollBack();

                $failed += $chunk->count();
                $error = [
                    'chunk' => $chunkNumber,
                    'error' => $e->getMessage(),
                    'items_in_chunk' => $chunk->count(),
                    'trace' => $e->getTraceAsString(),
                ];

                $errors[] = $error;

                Log::error('Batch chunk processing failed', $error);

                if (!$continueOnError) {
                    throw $e;
                }
            }
        });

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $result = [
            'total_items' => $totalItems,
            'processed' => $processed,
            'failed' => $failed,
            'success_rate' => $totalItems > 0 ? round(($processed / $totalItems) * 100, 2) : 0,
            'execution_time_ms' => $executionTime,
            'chunks_processed' => $totalChunks,
            'errors' => $errors,
        ];

        Log::info('Batch processing completed', $result);

        return $result;
    }

    /**
     * Bulk insert with conflict resolution (PostgreSQL specific)
     *
     * @param string $table Table name
     * @param array $data Array of associative arrays
     * @param array $conflictColumns Columns to check for conflicts
     * @param array $updateColumns Columns to update on conflict (optional)
     * @return int Number of affected rows
     */
    public function bulkInsert(string $table, array $data, array $conflictColumns = [], array $updateColumns = []): int
    {
        if (empty($data)) {
            return 0;
        }

        $firstRow = $data[0];
        $columns = array_keys($firstRow);

        // Build placeholders for INSERT
        $placeholders = '(' . str_repeat('?,', count($columns) - 1) . '?)';
        $allPlaceholders = str_repeat($placeholders . ',', count($data) - 1) . $placeholders;

        $sql = "INSERT INTO {$table} (" . implode(',', $columns) . ") VALUES {$allPlaceholders}";

        // Add ON CONFLICT clause if conflict columns specified
        if (!empty($conflictColumns)) {
            $sql .= " ON CONFLICT (" . implode(',', $conflictColumns) . ")";

            if (!empty($updateColumns)) {
                $updates = [];
                foreach ($updateColumns as $column) {
                    if (in_array($column, $columns)) {
                        $updates[] = "{$column} = EXCLUDED.{$column}";
                    }
                }
                if (!empty($updates)) {
                    $sql .= " DO UPDATE SET " . implode(',', $updates);
                } else {
                    $sql .= " DO NOTHING";
                }
            } else {
                $sql .= " DO NOTHING";
            }
        }

        // Flatten data for binding
        $flattenedData = [];
        foreach ($data as $row) {
            $flattenedData = array_merge($flattenedData, array_values($row));
        }

        try {
            $affected = DB::affectingStatement($sql, $flattenedData);

            Log::debug('Bulk insert completed', [
                'table' => $table,
                'rows_inserted' => count($data),
                'affected_rows' => $affected,
                'conflict_columns' => $conflictColumns,
            ]);

            return $affected;
        } catch (Throwable $e) {
            Log::error('Bulk insert failed', [
                'table' => $table,
                'error' => $e->getMessage(),
                'data_count' => count($data),
            ]);
            throw $e;
        }
    }

    /**
     * Bulk update using temporary table strategy
     *
     * @param string $table Target table
     * @param array $updates Array of update data
     * @param string $keyColumn Primary key column
     * @return int Number of affected rows
     */
    public function bulkUpdate(string $table, array $updates, string $keyColumn = 'id'): int
    {
        if (empty($updates)) {
            return 0;
        }

        $tempTable = 'temp_bulk_update_' . uniqid();
        $columns = array_keys($updates[0]);

        // Create temporary table
        $columnDefinitions = [];
        foreach ($columns as $column) {
            $columnDefinitions[] = "\"{$column}\" TEXT";
        }

        DB::statement("CREATE TEMP TABLE {$tempTable} (" . implode(',', $columnDefinitions) . ")");

        try {
            // Insert data into temp table
            $this->bulkInsert($tempTable, $updates);

            // Perform bulk update
            $updateColumns = array_filter($columns, fn($col) => $col !== $keyColumn);
            $setClause = implode(',', array_map(fn($col) => "\"{$table}\".\"{$col}\" = {$tempTable}.\"{$col}\"", $updateColumns));

            $affected = DB::affectingStatement("
                UPDATE \"{$table}\"
                SET {$setClause}
                FROM {$tempTable}
                WHERE \"{$table}\".\"{$keyColumn}\" = {$tempTable}.\"{$keyColumn}\"
            ");

            Log::debug('Bulk update completed', [
                'table' => $table,
                'rows_updated' => count($updates),
                'affected_rows' => $affected,
                'key_column' => $keyColumn,
            ]);

            return $affected;

        } finally {
            // Clean up temporary table
            DB::statement("DROP TABLE IF EXISTS {$tempTable}");
        }
    }

    /**
     * Bulk delete with chunking for large datasets
     *
     * @param string $table Table name
     * @param array $ids Array of IDs to delete
     * @param string $keyColumn Primary key column
     * @param int $chunkSize Chunk size for processing
     * @return int Total deleted rows
     */
    public function bulkDelete(string $table, array $ids, string $keyColumn = 'id', int $chunkSize = 1000): int
    {
        if (empty($ids)) {
            return 0;
        }

        $totalDeleted = 0;

        collect($ids)->chunk($chunkSize)->each(function ($chunk) use ($table, $keyColumn, &$totalDeleted) {
            $deleted = DB::table($table)
                        ->whereIn($keyColumn, $chunk)
                        ->delete();

            $totalDeleted += $deleted;
        });

        Log::debug('Bulk delete completed', [
            'table' => $table,
            'total_ids' => count($ids),
            'deleted_rows' => $totalDeleted,
            'key_column' => $keyColumn,
        ]);

        return $totalDeleted;
    }

    /**
     * Process items with progress tracking and resumability
     *
     * @param string $jobId Unique job identifier
     * @param Collection $items Items to process
     * @param callable $processor Processing function
     * @param int $chunkSize Processing chunk size
     * @return array Processing results
     */
    public function processWithProgress(string $jobId, Collection $items, callable $processor, int $chunkSize = 100): array
    {
        $progressKey = "batch_progress:{$jobId}";
        $totalItems = $items->count();

        // Check if job was already started
        $lastProcessedIndex = (int) Redis::get($progressKey) ?? 0;

        if ($lastProcessedIndex > 0) {
            Log::info('Resuming batch job', [
                'job_id' => $jobId,
                'last_processed_index' => $lastProcessedIndex,
                'total_items' => $totalItems,
            ]);
        }

        $processed = $lastProcessedIndex;
        $failed = 0;
        $errors = [];

        // Skip already processed items
        $remainingItems = $items->slice($lastProcessedIndex);

        $result = $this->processInChunks($remainingItems, $chunkSize, function ($chunk) use ($processor, &$processed, $progressKey, $chunkSize) {
            $processor($chunk);

            // Update progress
            $processed += $chunk->count();
            Redis::setex($progressKey, 3600, $processed); // Expire in 1 hour
        }, true); // Continue on error

        // Clean up progress tracking
        Redis::del($progressKey);

        return array_merge($result, [
            'job_id' => $jobId,
            'resumed_from' => $lastProcessedIndex,
            'progress_key' => $progressKey,
        ]);
    }

    /**
     * Get batch processing statistics
     */
    public function getBatchStats(string $jobId = null): array
    {
        $stats = [
            'active_jobs' => 0,
            'completed_jobs' => 0,
            'failed_jobs' => 0,
        ];

        try {
            $progressKeys = Redis::keys('batch_progress:*');
            $stats['active_jobs'] = count($progressKeys);

            // You could extend this to track completed/failed jobs
            // by storing them in Redis sets or database table

        } catch (Throwable $e) {
            Log::error('Failed to get batch stats', ['error' => $e->getMessage()]);
        }

        if ($jobId) {
            $progressKey = "batch_progress:{$jobId}";
            $stats['current_job'] = [
                'job_id' => $jobId,
                'progress' => (int) Redis::get($progressKey) ?? 0,
                'is_active' => Redis::exists($progressKey),
            ];
        }

        return $stats;
    }

    /**
     * Validate batch data before processing
     *
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array Validation results
     */
    public function validateBatchData(array $data, array $rules): array
    {
        $validator = validator($data, $rules);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->toArray(),
                'failed_count' => count($validator->errors()),
            ];
        }

        return [
            'valid' => true,
            'errors' => [],
            'failed_count' => 0,
        ];
    }
}