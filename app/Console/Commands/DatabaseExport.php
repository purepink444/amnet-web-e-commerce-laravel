<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export {--path= : Custom export path} {--compress : Compress the export file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export database to SQL file with proper structure and data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting database export...');

        $timestamp = Carbon::now()->format('Y-m-d-H-i-s');
        $filename = "dump-amnet-ecommerce-{$timestamp}.sql";
        $path = $this->option('path') ?: "../SQLScripts/{$filename}";

        $this->info("📁 Export path: {$path}");

        try {
            // Get all tables
            $tables = $this->getAllTables();
            $this->info("📊 Found " . count($tables) . " tables to export");

            $sql = $this->generateSQLDump($tables);

            // Save to file
            $fullPath = base_path('SQLScripts/' . basename($path));

            if ($this->option('compress')) {
                $this->compressAndSave($sql, basename($path));
            } else {
                file_put_contents($fullPath, $sql);
            }

            $this->info("✅ Database export completed successfully!");
            $this->info("📄 File saved to: SQLScripts/" . basename($path));

            // Show file size
            $size = filesize($fullPath);
            $this->info("📏 File size: " . $this->formatBytes($size));

        } catch (\Exception $e) {
            $this->error("❌ Export failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Get all tables in the database
     */
    private function getAllTables(): array
    {
        $database = config('database.connections.pgsql.database');
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");

        return array_column($tables, 'tablename');
    }

    /**
     * Generate SQL dump for all tables
     */
    private function generateSQLDump(array $tables): string
    {
        $sql = "-- Amnet E-commerce Database Export\n";
        $sql .= "-- Generated on: " . Carbon::now()->toDateTimeString() . "\n";
        $sql .= "-- Database: " . config('database.connections.pgsql.database') . "\n\n";

        $sql .= "SET statement_timeout = 0;\n";
        $sql .= "SET lock_timeout = 0;\n";
        $sql .= "SET idle_in_transaction_session_timeout = 0;\n";
        $sql .= "SET client_encoding = 'UTF8';\n";
        $sql .= "SET standard_conforming_strings = on;\n";
        $sql .= "SELECT pg_catalog.set_config('search_path', '', false);\n";
        $sql .= "SET check_function_bodies = false;\n";
        $sql .= "SET xmloption = content;\n";
        $sql .= "SET client_min_messages = warning;\n";
        $sql .= "SET row_security = off;\n\n";

        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();

        foreach ($tables as $table) {
            $sql .= $this->exportTable($table);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $sql;
    }

    /**
     * Export a single table with structure and data
     */
    private function exportTable(string $table): string
    {
        $sql = "--\n-- Table: {$table}\n--\n\n";

        // Get table structure
        $columns = DB::select("SELECT column_name, data_type, is_nullable, column_default
                              FROM information_schema.columns
                              WHERE table_name = ? AND table_schema = 'public'
                              ORDER BY ordinal_position", [$table]);

        // Create table SQL
        $sql .= "DROP TABLE IF EXISTS \"{$table}\" CASCADE;\n\n";
        $sql .= "CREATE TABLE \"{$table}\" (\n";

        $columnDefs = [];
        foreach ($columns as $column) {
            $def = "    \"{$column->column_name}\" {$column->data_type}";
            if ($column->is_nullable === 'NO') {
                $def .= ' NOT NULL';
            }
            if ($column->column_default) {
                $def .= ' DEFAULT ' . $column->column_default;
            }
            $columnDefs[] = $def;
        }

        $sql .= implode(",\n", $columnDefs) . "\n);\n\n";

        // Get data
        $data = DB::table($table)->get();

        if ($data->count() > 0) {
            $sql .= "INSERT INTO \"{$table}\" (";
            $columnNames = array_keys((array) $data->first());
            $sql .= '"' . implode('", "', $columnNames) . '"';
            $sql .= ") VALUES\n";

            $values = [];
            foreach ($data as $row) {
                $rowValues = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $rowValues[] = 'NULL';
                    } else {
                        $rowValues[] = "'" . addslashes($value) . "'";
                    }
                }
                $values[] = '(' . implode(', ', $rowValues) . ')';
            }

            $sql .= implode(",\n", $values) . ";\n\n";
        }

        return $sql;
    }

    /**
     * Compress and save the SQL file
     */
    private function compressAndSave(string $sql, string $filename): void
    {
        $compressedPath = base_path('SQLScripts/' . str_replace('.sql', '.sql.gz', $filename));
        $gz = gzopen($compressedPath, 'w9');
        gzwrite($gz, $sql);
        gzclose($gz);

        $this->info("🗜️ File compressed to: SQLScripts/" . str_replace('.sql', '.sql.gz', $filename));
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
