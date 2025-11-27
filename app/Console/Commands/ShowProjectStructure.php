<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ShowProjectStructure extends Command
{
    protected $signature = 'project:structure 
                            {--full : แสดงทุกไฟล์รวม vendor}
                            {--save : บันทึกเป็นไฟล์}';
    
    protected $description = 'แสดงโครงสร้างไฟล์ของโปรเจค';

    protected $output_lines = [];

    public function handle()
    {
        $this->info('📂 โครงสร้างโปรเจค Laravel');
        $this->newLine();

        $full = $this->option('full');
        
        // โฟลเดอร์หลักที่ต้องการแสดง
        $directories = [
            'app' => 'Application Logic',
            'bootstrap' => 'Bootstrap',
            'config' => 'Configuration',
            'database' => 'Database',
            'public' => 'Public Assets',
            'resources' => 'Views & Assets',
            'routes' => 'Routes',
            'storage' => 'Storage',
            'tests' => 'Tests',
        ];

        if ($full) {
            $directories['vendor'] = 'Vendor Packages';
        }

        foreach ($directories as $dir => $description) {
            if (File::exists(base_path($dir))) {
                $this->showDirectory($dir, $description);
            }
        }

        // แสดงไฟล์สำคัญในโฟลเดอร์ root
        $this->showRootFiles();

        // สถิติโปรเจค
        $this->newLine();
        $this->showStatistics();

        // บันทึกเป็นไฟล์
        if ($this->option('save')) {
            $this->saveToFile();
        }
    }

    protected function showDirectory($path, $description, $level = 0)
    {
        $fullPath = base_path($path);
        $indent = str_repeat('  ', $level);
        
        if ($level === 0) {
            $this->line("📁 <fg=cyan>{$path}/</> <fg=gray>({$description})</>");
            $this->output_lines[] = "📁 {$path}/ ({$description})";
        }

        // กรองไฟล์/โฟลเดอร์ที่ไม่ต้องการแสดง
        $ignore = ['.git', 'node_modules', 'vendor', '.idea', '.vscode', 'storage/framework', 'storage/logs'];
        
        if (!$this->option('full')) {
            $ignore = array_merge($ignore, ['vendor']);
        }

        $items = File::directories($fullPath);
        
        // แสดงโฟลเดอร์
        foreach ($items as $item) {
            $name = basename($item);
            
            if (in_array($name, $ignore)) continue;
            if ($level > 2) continue; // จำกัดความลึก
            
            $fileCount = count(File::files($item));
            $subDirCount = count(File::directories($item));
            
            $info = [];
            if ($fileCount > 0) $info[] = "{$fileCount} files";
            if ($subDirCount > 0) $info[] = "{$subDirCount} dirs";
            $infoStr = !empty($info) ? ' (' . implode(', ', $info) . ')' : '';
            
            $this->line("{$indent}  ├─ 📁 <fg=yellow>{$name}/</>$infoStr");
            $this->output_lines[] = "{$indent}  ├─ 📁 {$name}/$infoStr";
            
            // แสดงไฟล์สำคัญในโฟลเดอร์นี้
            if ($level < 2) {
                $this->showImportantFiles($item, $level + 1);
            }
        }
    }

    protected function showImportantFiles($directory, $level)
    {
        $indent = str_repeat('  ', $level);
        $files = File::files($directory);
        
        // แสดงเฉพาะไฟล์ .php, .blade.php, .js, .css
        $importantExtensions = ['php', 'blade.php', 'js', 'css', 'vue'];
        
        $filteredFiles = array_filter($files, function($file) use ($importantExtensions) {
            foreach ($importantExtensions as $ext) {
                if (str_ends_with($file->getFilename(), $ext)) {
                    return true;
                }
            }
            return false;
        });

        // จำกัดจำนวนไฟล์ที่แสดง
        $maxFiles = 10;
        $count = 0;
        
        foreach ($filteredFiles as $file) {
            if ($count >= $maxFiles) {
                $remaining = count($filteredFiles) - $maxFiles;
                $this->line("{$indent}    └─ <fg=gray>... และอีก {$remaining} ไฟล์</>");
                $this->output_lines[] = "{$indent}    └─ ... และอีก {$remaining} ไฟล์";
                break;
            }
            
            $icon = $this->getFileIcon($file->getExtension());
            $size = $this->formatBytes($file->getSize());
            
            $this->line("{$indent}    ├─ {$icon} <fg=green>{$file->getFilename()}</> <fg=gray>({$size})</>");
            $this->output_lines[] = "{$indent}    ├─ {$icon} {$file->getFilename()} ({$size})";
            $count++;
        }
    }

    protected function showRootFiles()
    {
        $this->newLine();
        $this->line('📄 <fg=cyan>Root Files</>');
        $this->output_lines[] = '📄 Root Files';
        
        $importantFiles = [
            '.env.example',
            'artisan',
            'composer.json',
            'package.json',
            'phpunit.xml',
            'README.md',
        ];

        foreach ($importantFiles as $file) {
            $path = base_path($file);
            if (File::exists($path)) {
                $size = $this->formatBytes(File::size($path));
                $this->line("  ├─ 📄 <fg=green>{$file}</> <fg=gray>({$size})</>");
                $this->output_lines[] = "  ├─ 📄 {$file} ({$size})";
            }
        }
    }

    protected function showStatistics()
    {
        $this->info('📊 สถิติโปรเจค');
        
        $stats = [
            'Controllers' => $this->countFiles('app/Http/Controllers', '*.php'),
            'Models' => $this->countFiles('app/Models', '*.php'),
            'Migrations' => $this->countFiles('database/migrations', '*.php'),
            'Views' => $this->countFiles('resources/views', '*.blade.php'),
            'Routes' => $this->countRoutes(),
        ];

        foreach ($stats as $label => $count) {
            $this->line("  • {$label}: <fg=yellow>{$count}</>");
            $this->output_lines[] = "  • {$label}: {$count}";
        }

        // ขนาดโปรเจค
        $this->newLine();
        $projectSize = $this->getDirectorySize(base_path());
        $this->line("  • ขนาดโปรเจค: <fg=yellow>{$this->formatBytes($projectSize)}</>");
        $this->output_lines[] = "  • ขนาดโปรเจค: {$this->formatBytes($projectSize)}";
    }

    protected function countFiles($path, $pattern = '*')
    {
        $fullPath = base_path($path);
        if (!File::exists($fullPath)) return 0;
        
        return count(File::glob("{$fullPath}/{$pattern}"));
    }

    protected function countRoutes()
    {
        try {
            $routes = \Route::getRoutes();
            return count($routes);
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getDirectorySize($path)
    {
        $size = 0;
        $ignore = ['vendor', 'node_modules', '.git', 'storage/logs'];
        
        foreach (File::allFiles($path) as $file) {
            $skip = false;
            foreach ($ignore as $ignoreDir) {
                if (str_contains($file->getPathname(), $ignoreDir)) {
                    $skip = true;
                    break;
                }
            }
            if (!$skip) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    protected function getFileIcon($extension)
    {
        return match($extension) {
            'php' => '🐘',
            'js' => '📜',
            'vue' => '💚',
            'css' => '🎨',
            'blade.php' => '🔪',
            default => '📄'
        };
    }

    protected function saveToFile()
    {
        $filename = 'project-structure-' . date('Y-m-d-His') . '.txt';
        $path = base_path($filename);
        
        File::put($path, implode("\n", $this->output_lines));
        
        $this->newLine();
        $this->info("✅ บันทึกเป็นไฟล์: {$filename}");
    }
}