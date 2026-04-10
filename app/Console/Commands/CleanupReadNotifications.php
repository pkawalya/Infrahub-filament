<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupReadNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-read-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete read notifications older than 90 days to keep the notifications table clean';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays(90))
            ->delete();

        $this->info("Deleted {$count} read notifications older than 90 days.");
    }
}
