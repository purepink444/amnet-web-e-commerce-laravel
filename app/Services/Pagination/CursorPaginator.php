<?php

namespace App\Services\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CursorPaginator
{
    /**
     * Paginate results using cursor-based pagination
     *
     * Advantages over offset-based pagination:
     * - Consistent performance regardless of page number
     * - No duplicate/missing results when data changes
     * - Better for real-time data
     * - More efficient for large datasets
     *
     * @param Builder $query The Eloquent query builder
     * @param int $perPage Number of items per page
     * @param string|null $cursor Encoded cursor for next page
     * @return array
     */
    public function paginate(Builder $query, int $perPage = 20, ?string $cursor = null): array
    {
        $startTime = microtime(true);

        // Decode cursor to get pagination parameters
        $decodedCursor = $cursor ? $this->decodeCursor($cursor) : null;

        // Clone query for counting (if needed)
        $countQuery = clone $query;

        // Apply cursor condition for pagination
        if ($decodedCursor) {
            $query->where(function ($q) use ($decodedCursor) {
                // Order by created_at DESC, then by ID DESC for stable sorting
                $q->where('created_at', '<', $decodedCursor['created_at'])
                  ->orWhere(function ($sq) use ($decodedCursor) {
                      $sq->where('created_at', '=', $decodedCursor['created_at'])
                         ->where('id', '<', $decodedCursor['id']);
                  });
            });
        }

        // Get results with one extra item to check if there are more results
        $results = $query->orderBy('created_at', 'desc')
                         ->orderBy('id', 'desc')
                         ->limit($perPage + 1)
                         ->get();

        $hasNextPage = $results->count() > $perPage;
        $data = $results->take($perPage);

        // Generate next cursor if there are more results
        $nextCursor = null;
        if ($hasNextPage && $data->isNotEmpty()) {
            $lastItem = $data->last();
            $nextCursor = $this->encodeCursor([
                'id' => $lastItem->id,
                'created_at' => $lastItem->created_at->toISOString(),
            ]);
        }

        // Get total count (expensive operation, use with caution)
        $totalCount = $this->shouldIncludeCount() ? $countQuery->count() : null;

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        Log::debug('Cursor pagination executed', [
            'per_page' => $perPage,
            'has_next_page' => $hasNextPage,
            'result_count' => $data->count(),
            'execution_time_ms' => $executionTime,
            'cursor_provided' => !is_null($cursor),
        ]);

        return [
            'data' => $data,
            'pagination' => [
                'has_next_page' => $hasNextPage,
                'next_cursor' => $nextCursor,
                'per_page' => $perPage,
                'count' => $totalCount,
                'execution_time_ms' => $executionTime,
            ],
        ];
    }

    /**
     * Paginate with previous page support (bidirectional)
     */
    public function paginateBidirectional(Builder $query, int $perPage = 20, ?string $beforeCursor = null, ?string $afterCursor = null): array
    {
        // Decode cursors
        $beforeDecoded = $beforeCursor ? $this->decodeCursor($beforeCursor) : null;
        $afterDecoded = $afterCursor ? $this->decodeCursor($afterCursor) : null;

        // Apply cursor conditions
        if ($beforeDecoded) {
            // Get items before the cursor (previous page)
            $query->where(function ($q) use ($beforeDecoded) {
                $q->where('created_at', '>', $beforeDecoded['created_at'])
                  ->orWhere(function ($sq) use ($beforeDecoded) {
                      $sq->where('created_at', '=', $beforeDecoded['created_at'])
                         ->where('id', '>', $beforeDecoded['id']);
                  });
            });
        }

        if ($afterDecoded) {
            // Get items after the cursor (next page)
            $query->where(function ($q) use ($afterDecoded) {
                $q->where('created_at', '<', $afterDecoded['created_at'])
                  ->orWhere(function ($sq) use ($afterDecoded) {
                      $sq->where('created_at', '=', $afterDecoded['created_at'])
                         ->where('id', '<', $afterDecoded['id']);
                  });
            });
        }

        // Get results
        $results = $query->orderBy('created_at', 'desc')
                         ->orderBy('id', 'desc')
                         ->limit($perPage + 2) // +2 to check boundaries
                         ->get();

        $data = $results->take($perPage);

        // Generate cursors
        $hasNextPage = $results->count() > $perPage;
        $hasPreviousPage = !is_null($afterCursor) || (!is_null($beforeCursor) && $results->count() > $perPage);

        $startCursor = null;
        $endCursor = null;

        if ($data->isNotEmpty()) {
            $startCursor = $this->encodeCursor([
                'id' => $data->first()->id,
                'created_at' => $data->first()->created_at->toISOString(),
            ]);

            $endCursor = $this->encodeCursor([
                'id' => $data->last()->id,
                'created_at' => $data->last()->created_at->toISOString(),
            ]);
        }

        return [
            'data' => $data,
            'pagination' => [
                'has_next_page' => $hasNextPage,
                'has_previous_page' => $hasPreviousPage,
                'start_cursor' => $startCursor,
                'end_cursor' => $endCursor,
                'per_page' => $perPage,
            ],
        ];
    }

    /**
     * Encode cursor data to base64 string
     */
    private function encodeCursor(array $data): string
    {
        return base64_encode(json_encode($data));
    }

    /**
     * Decode cursor string to array
     */
    private function decodeCursor(string $cursor): array
    {
        $decoded = json_decode(base64_decode($cursor), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid cursor format');
        }

        // Validate required fields
        if (!isset($decoded['id']) || !isset($decoded['created_at'])) {
            throw new \InvalidArgumentException('Cursor missing required fields');
        }

        return $decoded;
    }

    /**
     * Determine if count should be included (based on request parameter)
     */
    private function shouldIncludeCount(): bool
    {
        // Check if client explicitly requested count
        return request()->has('include_count') && request()->boolean('include_count');
    }

    /**
     * Get cursor-based pagination metadata
     */
    public function getPaginationMeta(array $pagination, ?string $baseUrl = null): array
    {
        $meta = [
            'per_page' => $pagination['per_page'],
            'has_next_page' => $pagination['has_next_page'],
        ];

        if (isset($pagination['count'])) {
            $meta['total_count'] = $pagination['count'];
        }

        if (isset($pagination['execution_time_ms'])) {
            $meta['execution_time_ms'] = $pagination['execution_time_ms'];
        }

        // Generate links if base URL provided
        if ($baseUrl && $pagination['has_next_page'] && $pagination['next_cursor']) {
            $meta['links'] = [
                'next' => $baseUrl . '?cursor=' . $pagination['next_cursor'],
            ];
        }

        return $meta;
    }

    /**
     * Validate cursor format
     */
    public function validateCursor(?string $cursor): bool
    {
        if (empty($cursor)) {
            return true; // Null cursor is valid
        }

        try {
            $this->decodeCursor($cursor);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get estimated page number from cursor (approximate)
     * This is useful for UI that wants to show "Page X of Y"
     */
    public function estimatePageFromCursor(string $cursor, Builder $query, int $perPage): int
    {
        try {
            $decodedCursor = $this->decodeCursor($cursor);

            // Count items newer than cursor
            $newerCount = $query->where(function ($q) use ($decodedCursor) {
                $q->where('created_at', '>', $decodedCursor['created_at'])
                  ->orWhere(function ($sq) use ($decodedCursor) {
                      $sq->where('created_at', '=', $decodedCursor['created_at'])
                         ->where('id', '>', $decodedCursor['id']);
                  });
            })->count();

            return (int) ceil(($newerCount + 1) / $perPage);
        } catch (\Exception $e) {
            return 1; // Default to first page on error
        }
    }
}