<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorDatabasePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:monitor-performance
                            {--slow-threshold=1000 : Slow query threshold in milliseconds}
                            {--report : Generate detailed performance report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor database performance and identify slow queries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Monitoring Database Performance...');

        $slowThreshold = $this->option('slow-threshold');
        $generateReport = $this->option('report');

        // Check if pg_stat_statements is available
        $hasPgStat = $this->checkPgStatStatements();

        if (!$hasPgStat) {
            $this->warn('⚠️  pg_stat_statements extension not available. Limited monitoring capabilities.');
            $this->warn('To enable full monitoring, run: CREATE EXTENSION pg_stat_statements;');
        }

        if ($generateReport) {
            $this->generateDetailedReport($slowThreshold);
        } else {
            $this->showQuickStats($slowThreshold);
        }

        return Command::SUCCESS;
    }

    /**
     * Check if pg_stat_statements extension is available
     */
    private function checkPgStatStatements(): bool
    {
        try {
            $result = DB::select("SELECT 1 FROM pg_extension WHERE extname = 'pg_stat_statements'");
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Show quick performance statistics
     */
    private function showQuickStats(int $slowThreshold): void
    {
        $this->info('📊 Quick Performance Stats:');

        // Database size
        $dbSize = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size")[0]->size ?? 'Unknown';
        $this->line("Database Size: <comment>{$dbSize}</comment>");

        // Connection count
        $connections = DB::select("SELECT count(*) as connections FROM pg_stat_activity WHERE datname = current_database()")[0]->connections ?? 0;
        $this->line("Active Connections: <comment>{$connections}</comment>");

        // Table bloat analysis
        $this->showTableBloat();

        // Index usage
        $this->showIndexUsage();

        // Slow queries (if pg_stat_statements available)
        if ($this->checkPgStatStatements()) {
            $this->showSlowQueries($slowThreshold);
        }
    }

    /**
     * Generate detailed performance report
     */
    private function generateDetailedReport(int $slowThreshold): void
    {
        $this->info('📋 Generating Detailed Performance Report...');

        $report = [
            'timestamp' => now()->toISOString(),
            'database' => config('database.connections.pgsql.database'),
            'stats' => []
        ];

        // Database statistics
        $report['stats']['database'] = $this->getDatabaseStats();

        // Table statistics
        $report['stats']['tables'] = $this->getTableStats();

        // Index statistics
        $report['stats']['indexes'] = $this->getIndexStats();

        // Query performance
        if ($this->checkPgStatStatements()) {
            $report['stats']['queries'] = $this->getQueryStats($slowThreshold);
        }

        // Recommendations
        $report['recommendations'] = $this->generateRecommendations($report);

        // Save report
        $filename = 'database_performance_report_' . date('Y-m-d_H-i-s') . '.json';
        $path = storage_path('reports/' . $filename);

        // Ensure directory exists
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ Report saved to: <comment>{$path}</comment>");

        // Display summary
        $this->displayReportSummary($report);
    }

    /**
     * Get database statistics
     */
    private function getDatabaseStats(): array
    {
        $stats = [];

        try {
            $stats['size'] = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size")[0]->size ?? 'Unknown';
            $stats['connections'] = DB::select("SELECT count(*) as connections FROM pg_stat_activity WHERE datname = current_database()")[0]->connections ?? 0;
            $stats['cache_hit_ratio'] = DB::select("SELECT round(sum(blks_hit)*100/sum(blks_hit+blks_read), 2) as ratio FROM pg_stat_database WHERE datname = current_database()")[0]->ratio ?? 0;
        } catch (\Exception $e) {
            Log::error('Failed to get database stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get table statistics
     */
    private function getTableStats(): array
    {
        try {
            return DB::select("
                SELECT
                    schemaname,
                    tablename,
                    n_tup_ins as inserts,
                    n_tup_upd as updates,
                    n_tup_del as deletes,
                    n_live_tup as live_rows,
                    n_dead_tup as dead_rows,
                    ROUND(n_dead_tup::numeric / NULLIF(n_live_tup + n_dead_tup, 0) * 100, 2) as bloat_ratio
                FROM pg_stat_user_tables
                WHERE schemaname = 'public'
                ORDER BY n_live_tup DESC
                LIMIT 20
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get index statistics
     */
    private function getIndexStats(): array
    {
        try {
            return DB::select("
                SELECT
                    schemaname,
                    tablename,
                    indexname,
                    idx_scan as scans,
                    idx_tup_read as tuples_read,
                    idx_tup_fetch as tuples_fetched,
                    pg_size_pretty(pg_relation_size(indexrelid)) as size
                FROM pg_stat_user_indexes
                WHERE schemaname = 'public'
                ORDER BY idx_scan DESC
                LIMIT 20
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get query performance statistics
     */
    private function getQueryStats(int $slowThreshold): array
    {
        try {
            return DB::select("
                SELECT
                    query,
                    calls,
                    total_time,
                    mean_time,
                    rows,
                    CASE WHEN mean_time > ? THEN 'SLOW' ELSE 'NORMAL' END as performance
                FROM pg_stat_statements
                WHERE query NOT LIKE '%pg_stat_statements%'
                ORDER BY mean_time DESC
                LIMIT 20
            ", [$slowThreshold]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Show table bloat information
     */
    private function showTableBloat(): void
    {
        $this->info('📏 Table Bloat Analysis:');

        try {
            $bloatData = DB::select("
                SELECT
                    schemaname || '.' || tablename as table_name,
                    n_dead_tup as dead_tuples,
                    n_live_tup as live_tuples,
                    ROUND(n_dead_tup::numeric / NULLIF(n_live_tup + n_dead_tup, 0) * 100, 2) as bloat_percent
                FROM pg_stat_user_tables
                WHERE schemaname = 'public'
                    AND n_live_tup + n_dead_tup > 0
                    AND n_dead_tup > 1000
                ORDER BY bloat_percent DESC
                LIMIT 5
            ");

            if (empty($bloatData)) {
                $this->line('  ✅ No significant table bloat detected');
            } else {
                foreach ($bloatData as $table) {
                    $status = $table->bloat_percent > 50 ? '🔴' : ($table->bloat_percent > 20 ? '🟡' : '🟢');
                    $this->line("  {$status} {$table->table_name}: {$table->bloat_percent}% bloat ({$table->dead_tuples} dead tuples)");
                }
            }
        } catch (\Exception $e) {
            $this->line('  ⚠️  Unable to analyze table bloat');
        }
    }

    /**
     * Show index usage information
     */
    private function showIndexUsage(): void
    {
        $this->info('🔍 Index Usage Analysis:');

        try {
            $unusedIndexes = DB::select("
                SELECT
                    schemaname || '.' || tablename || '.' || indexname as index_name,
                    idx_scan as scans
                FROM pg_stat_user_indexes
                WHERE schemaname = 'public'
                    AND idx_scan = 0
                    AND indexname NOT LIKE 'pk_%'
                ORDER BY tablename, indexname
                LIMIT 10
            ");

            if (empty($unusedIndexes)) {
                $this->line('  ✅ All indexes are being used');
            } else {
                foreach ($unusedIndexes as $index) {
                    $this->line("  ⚠️  Unused index: <comment>{$index->index_name}</comment>");
                }
                if (count($unusedIndexes) >= 10) {
                    $this->line('  ... and more (showing first 10)');
                }
            }
        } catch (\Exception $e) {
            $this->line('  ⚠️  Unable to analyze index usage');
        }
    }

    /**
     * Show slow queries
     */
    private function showSlowQueries(int $slowThreshold): void
    {
        $this->info("🐌 Slow Queries (>{$slowThreshold}ms):");

        try {
            $slowQueries = DB::select("
                SELECT
                    LEFT(query, 100) as query_preview,
                    calls,
                    ROUND(mean_time::numeric, 2) as avg_time,
                    ROUND(total_time::numeric, 2) as total_time
                FROM pg_stat_statements
                WHERE mean_time > ?
                    AND query NOT LIKE '%pg_stat_statements%'
                ORDER BY mean_time DESC
                LIMIT 5
            ", [$slowThreshold]);

            if (empty($slowQueries)) {
                $this->line('  ✅ No slow queries detected');
            } else {
                foreach ($slowQueries as $query) {
                    $this->line("  🔴 {$query->avg_time}ms avg: <comment>{$query->query_preview}...</comment>");
                }
            }
        } catch (\Exception $e) {
            $this->line('  ⚠️  Unable to analyze slow queries');
        }
    }

    /**
     * Generate recommendations based on analysis
     */
    private function generateRecommendations(array $report): array
    {
        $recommendations = [];

        // Check database size
        if (isset($report['stats']['database']['size'])) {
            $size = $report['stats']['database']['size'];
            if (str_contains($size, 'GB') && (float) str_replace(['GB', ' '], '', $size) > 10) {
                $recommendations[] = 'Consider database partitioning for large tables';
            }
        }

        // Check table bloat
        if (!empty($report['stats']['tables'])) {
            foreach ($report['stats']['tables'] as $table) {
                if (($table->bloat_ratio ?? 0) > 30) {
                    $recommendations[] = "High bloat detected in {$table->schemaname}.{$table->tablename} ({$table->bloat_ratio}%). Consider VACUUM FULL.";
                }
            }
        }

        // Check slow queries
        if (!empty($report['stats']['queries'])) {
            $slowCount = count(array_filter($report['stats']['queries'], fn($q) => ($q->performance ?? '') === 'SLOW'));
            if ($slowCount > 0) {
                $recommendations[] = "{$slowCount} slow queries detected. Review query optimization and add appropriate indexes.";
            }
        }

        return $recommendations;
    }

    /**
     * Display report summary
     */
    private function displayReportSummary(array $report): void
    {
        $this->info('📊 Report Summary:');

        if (isset($report['stats']['database'])) {
            $db = $report['stats']['database'];
            $this->line("Database Size: <comment>" . (isset($db['size']) ? $db['size'] : 'Unknown') . "</comment>");
            $this->line("Active Connections: <comment>" . (isset($db['connections']) ? $db['connections'] : 0) . "</comment>");
            if (isset($db['cache_hit_ratio'])) {
                $ratio = $db['cache_hit_ratio'];
                $color = $ratio > 95 ? 'green' : ($ratio > 85 ? 'yellow' : 'red');
                $this->line("Cache Hit Ratio: <{$color}>{$ratio}%</{$color}>");
            }
        }

        if (!empty($report['recommendations'])) {
            $this->info('💡 Recommendations:');
            foreach ($report['recommendations'] as $rec) {
                $this->line("  • {$rec}");
            }
        } else {
            $this->line('✅ No critical issues detected');
        }
    }
}