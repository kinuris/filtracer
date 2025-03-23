<?php

namespace App\Jobs;

use App\Models\ProfessionalRecord;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DailyProfRecordChecking implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('LOG: DailyProfRecordChecking job is running at ' . now());

        $sixMonthsAgo = now()->subMonths(6);
        $records = ProfessionalRecord::where('updated_at', '<=', $sixMonthsAgo)->get();
        
        Log::info('Found ' . $records->count() . ' professional records not updated in 6 months or more.');
        
        // Process records as needed
        foreach ($records as $record) {
            // Add your processing logic here
            // For example: $record->markForReview();
        }

        self::dispatch()->delay(now()->addMinutes(1));
    }
}
