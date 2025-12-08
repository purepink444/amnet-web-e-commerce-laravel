<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--cleanup-days=30 : Number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$timestamp}.sql";
        $path = "backups/{$filename}";

        try {
            // Get database connection details
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');

            // Create backup using mysqldump
            $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > /tmp/{$filename}";

            $returnVar = null;
            $output = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('mysqldump command failed');
            }

            // Store backup file
            $backupContent = file_get_contents("/tmp/{$filename}");
            Storage::put($path, $backupContent);

            // Clean up temp file
            unlink("/tmp/{$filename}");

            $this->info("Database backup created successfully: {$path}");

            // Clean up old backups
            $this->cleanupOldBackups();

        } catch (\Exception $e) {
            $this->error('Database backup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Clean up old backup files
     */
    private function cleanupOldBackups()
    {
        $cleanupDays = $this->option('cleanup-days');
        $cutoffDate = Carbon::now()->subDays($cleanupDays);

        $backups = Storage::files('backups');
        $deletedCount = 0;

        foreach ($backups as $backup) {
            // Extract date from filename (backup_2025-12-08_14-30-00.sql)
            if (preg_match('/backup_(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2}-\d{2})\.sql/', $backup, $matches)) {
                $backupDate = Carbon::createFromFormat('Y-m-d H-i-s', $matches[1] . ' ' . str_replace('-', ':', $matches[2]));

                if ($backupDate->lessThan($cutoffDate)) {
                    Storage::delete($backup);
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Cleaned up {$deletedCount} old backup files");
        }
    }
}