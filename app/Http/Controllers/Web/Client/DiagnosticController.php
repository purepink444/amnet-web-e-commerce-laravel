<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DiagnosticController extends Controller
{
    /**
     * Display the main diagnostic page
     */
    public function index(): View
    {
        return view('pages.diagnostic');
    }

    /**
     * Perform system health checks
     */
    public function systemCheck(Request $request)
    {
        $checks = [];

        // CPU Usage (simplified)
        $checks['cpu'] = $this->getCpuUsage();

        // Memory Usage
        $checks['memory'] = $this->getMemoryUsage();

        // Disk Usage
        $checks['disk'] = $this->getDiskUsage();

        // Database Connection
        $checks['database'] = $this->checkDatabaseConnection();

        // Cache Status
        $checks['cache'] = $this->checkCacheStatus();

        return response()->json($checks);
    }

    /**
     * Perform network diagnostics
     */
    public function networkCheck(Request $request)
    {
        $target = $request->input('target', 'google.com');
        $checks = [];

        // Ping check
        $checks['ping'] = $this->pingHost($target);

        // DNS lookup
        $checks['dns'] = $this->dnsLookup($target);

        // Port check (HTTP)
        $checks['http'] = $this->checkPort($target, 80);

        // HTTPS check
        $checks['https'] = $this->checkPort($target, 443);

        return response()->json($checks);
    }

    /**
     * Perform product diagnostics
     */
    public function productCheck(Request $request)
    {
        $checks = [];

        // Total products
        $checks['total_products'] = Product::count();

        // Active products
        $checks['active_products'] = Product::where('status', 'active')->count();

        // Out of stock products
        $checks['out_of_stock'] = Product::where('stock_quantity', 0)->count();

        // Database query performance
        $startTime = microtime(true);
        Product::where('status', 'active')->limit(10)->get();
        $checks['query_time'] = round((microtime(true) - $startTime) * 1000, 2) . 'ms';

        // Image availability
        $checks['images_check'] = $this->checkProductImages();

        return response()->json($checks);
    }

    private function getCpuUsage()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                'load_average' => $load[0],
                'status' => $load[0] < 1 ? 'good' : ($load[0] < 2 ? 'warning' : 'critical')
            ];
        }
        return ['status' => 'not_available'];
    }

    private function getMemoryUsage()
    {
        $memory_limit = ini_get('memory_limit');
        $memory_used = memory_get_peak_usage(true);
        $memory_used_mb = round($memory_used / 1024 / 1024, 2);

        return [
            'used' => $memory_used_mb . 'MB',
            'limit' => $memory_limit,
            'status' => 'good'
        ];
    }

    private function getDiskUsage()
    {
        $disk_free = disk_free_space('/');
        $disk_total = disk_total_space('/');
        $disk_used = $disk_total - $disk_free;

        $free_gb = round($disk_free / 1024 / 1024 / 1024, 2);
        $total_gb = round($disk_total / 1024 / 1024 / 1024, 2);
        $used_percent = round(($disk_used / $disk_total) * 100, 2);

        return [
            'free' => $free_gb . 'GB',
            'total' => $total_gb . 'GB',
            'used_percent' => $used_percent . '%',
            'status' => $used_percent < 80 ? 'good' : ($used_percent < 95 ? 'warning' : 'critical')
        ];
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'connected'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function checkCacheStatus()
    {
        try {
            Cache::store()->getStore()->connection();
            return ['status' => 'working'];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function pingHost($host)
    {
        $ping = exec("ping -n 1 -w 1000 $host", $output, $return_var);
        return [
            'host' => $host,
            'status' => $return_var === 0 ? 'reachable' : 'unreachable',
            'output' => implode("\n", $output)
        ];
    }

    private function dnsLookup($host)
    {
        $ip = gethostbyname($host);
        return [
            'host' => $host,
            'ip' => $ip,
            'status' => $ip !== $host ? 'resolved' : 'failed'
        ];
    }

    private function checkPort($host, $port)
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, 5);
        if ($connection) {
            fclose($connection);
            return ['host' => $host, 'port' => $port, 'status' => 'open'];
        } else {
            return ['host' => $host, 'port' => $port, 'status' => 'closed', 'error' => $errstr];
        }
    }

    private function checkProductImages()
    {
        $products = Product::whereNotNull('photo_path')->limit(5)->get();
        $checked = 0;
        $accessible = 0;

        foreach ($products as $product) {
            $checked++;
            $path = storage_path('app/public/' . $product->photo_path);
            if (file_exists($path)) {
                $accessible++;
            }
        }

        return [
            'checked' => $checked,
            'accessible' => $accessible,
            'status' => $accessible === $checked ? 'good' : 'issues'
        ];
    }
}