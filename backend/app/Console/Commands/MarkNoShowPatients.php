<?php

namespace App\Console\Commands;

use App\Events\QueueUpdated;
use App\Models\Queue;
use Illuminate\Console\Command;

class MarkNoShowPatients extends Command
{
    protected $signature   = 'mark:noshow-patients';
    protected $description = 'Mark waiting patients as no-show after 15 minutes of inactivity';

    public function handle(): void
    {
        $timeoutMinutes = 15;

        $entries = Queue::where('status', 'waiting')
            ->whereRaw('queue_date = ?', [now()->toDateString()])
            ->get();

        $count = 0;

        foreach ($entries as $entry) {
            if ($entry->created_at->diffInMinutes(now()) > $timeoutMinutes) {
                $entry->update(['status' => 'no_show']);
                event(new QueueUpdated($entry));
                $count++;
            }
        }

        $this->info("Marked {$count} patient(s) as no-show.");
    }
}
