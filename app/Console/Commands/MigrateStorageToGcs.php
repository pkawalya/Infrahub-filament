<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateStorageToGcs extends Command
{
    protected $signature = 'storage:migrate-to-gcs {--dry-run : Show what would be copied without actually copying}';
    protected $description = 'Migrate all local storage files to Google Cloud Storage';

    public function handle(): int
    {
        $localPublic = Storage::disk('public');
        $localPrivate = Storage::disk('local');
        $gcs = Storage::disk('gcs');

        $this->info('🔍 Scanning local files...');

        // Migrate public files
        $publicFiles = collect($localPublic->allFiles())
            ->reject(fn($f) => str_contains($f, '.gitignore') || str_contains($f, 'livewire-tmp'));

        // Migrate private files
        $privateFiles = collect($localPrivate->allFiles())
            ->reject(fn($f) => str_contains($f, '.gitignore') || str_contains($f, 'livewire-tmp'));

        $totalFiles = $publicFiles->count() + $privateFiles->count();
        $this->info("📦 Found {$publicFiles->count()} public + {$privateFiles->count()} private = {$totalFiles} files");

        if ($totalFiles === 0) {
            $this->info('✅ No files to migrate.');
            return self::SUCCESS;
        }

        $isDryRun = $this->option('dry-run');
        $migrated = 0;
        $skipped = 0;
        $failed = 0;

        // Copy public files
        if ($publicFiles->isNotEmpty()) {
            $this->info('');
            $this->info('── Public files ──');
            foreach ($publicFiles as $file) {
                $size = number_format($localPublic->size($file));
                if ($isDryRun) {
                    $this->line("  [DRY] {$file} ({$size} bytes)");
                    $migrated++;
                    continue;
                }

                try {
                    if ($gcs->exists($file)) {
                        $this->warn("  ⏭  {$file} (already exists in GCS, skipping)");
                        $skipped++;
                        continue;
                    }
                    $gcs->put($file, $localPublic->get($file));
                    $this->line("  ✅ {$file} ({$size} bytes)");
                    $migrated++;
                } catch (\Throwable $e) {
                    $this->error("  ❌ {$file}: {$e->getMessage()}");
                    $failed++;
                }
            }
        }

        // Copy private files
        if ($privateFiles->isNotEmpty()) {
            $this->info('');
            $this->info('── Private files ──');
            foreach ($privateFiles as $file) {
                $gcsPath = 'private/' . $file;
                $size = number_format($localPrivate->size($file));
                if ($isDryRun) {
                    $this->line("  [DRY] {$file} → {$gcsPath} ({$size} bytes)");
                    $migrated++;
                    continue;
                }

                try {
                    if ($gcs->exists($gcsPath)) {
                        $this->warn("  ⏭  {$gcsPath} (already exists in GCS, skipping)");
                        $skipped++;
                        continue;
                    }
                    $gcs->put($gcsPath, $localPrivate->get($file));
                    $this->line("  ✅ {$file} → {$gcsPath} ({$size} bytes)");
                    $migrated++;
                } catch (\Throwable $e) {
                    $this->error("  ❌ {$file}: {$e->getMessage()}");
                    $failed++;
                }
            }
        }

        $this->info('');
        $this->info("📊 Results: {$migrated} migrated, {$skipped} skipped, {$failed} failed");

        if ($isDryRun) {
            $this->warn('⚠️  This was a dry run. Run without --dry-run to actually migrate.');
        } elseif ($failed === 0) {
            $this->info('🎉 Migration complete! You can now set FILESYSTEM_DISK=gcs in your .env');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
