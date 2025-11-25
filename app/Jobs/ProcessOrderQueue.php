<?php

namespace App\Jobs;

use App\Services\DataStructureService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessOrderQueue implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job with optimized algorithms.
     * Time Complexity: O(n) where n is number of pending orders
     */
    public function handle(DataStructureService $dataStructureService): void
    {
        Log::info('Starting order queue processing job');

        try {
            // Process orders using efficient queue algorithm
            $result = $dataStructureService->processOrderQueue();

            // Log results
            Log::info('Order queue processing completed', [
                'processed' => $result['total_processed'],
                'failed' => $result['total_failed'],
                'success_rate' => $result['total_processed'] + $result['total_failed'] > 0
                    ? ($result['total_processed'] / ($result['total_processed'] + $result['total_failed'])) * 100
                    : 0
            ]);

            // Log failed orders for manual review
            if ($result['total_failed'] > 0) {
                foreach ($result['failed'] as $failed) {
                    Log::warning('Order processing failed', [
                        'order_id' => $failed['order']->order_id,
                        'reason' => $failed['reason']
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Order queue processing job failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to mark job as failed
        }
    }

    /**
     * Handle job failure with exponential backoff
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Order queue processing job failed permanently', [
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Could send admin notification here
    }
}
