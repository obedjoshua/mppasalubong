<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\select;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Interactive terminal dashboard for managing the sitemap';

    public function handle()
    {
        $path = public_path('sitemap.xml');
        
        while (true) {
            // 1. Gather file status details
            $exists = File::exists($path);
            $lastModified = $exists ? date("Y-m-d H:i:s", File::lastModified($path)) : 'Never';
            $size = $exists ? round(File::size($path) / 1024, 2) . ' KB' : '0 KB';

            // 2. Render a clean visual dashboard status table
            $this->newLine();
            info('================ 🗺️  SITEMAP TERMINAL DASHBOARD ================');
            table(
                ['Metric', 'Current Status'],
                [
                    ['File Existence', $exists ? '🟢 Active (public/sitemap.xml)' : '🔴 Missing'],
                    ['Last Updated', $lastModified],
                    ['File Size', $size],
                ]
            );

            // 3. Prompt user with a graphical command menu
            $action = select(
                label: 'What would you like to do?',
                options: [
                    'generate' => '🔄 Regenerate Sitemap Now',
                    'view'     => '👀 View File Path info',
                    'exit'     => '❌ Close Dashboard',
                ],
                default: 'generate'
            );

            // 4. Handle menu choices
            if ($action === 'exit') {
                info('Goodbye!');
                break;
            }

            if ($action === 'view') {
                info("Sitemap path: " . realpath($path));
                continue;
            }

            if ($action === 'generate') {
                // Renders a smooth terminal spinner animation while crawling
                spin(
                    callback: function () use ($path) {
                        SitemapGenerator::create(config('app.url'))->writeToFile($path);
                    },
                    message: 'Crawling application routes and updating file...'
                );

                info('✅ Sitemap updated successfully!');
            }
        }
    }
}
