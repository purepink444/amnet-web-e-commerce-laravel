<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductNotification;
use App\Services\Logging\ApiLogger;

class SendProductNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60]; // Retry delays in seconds
    public int $timeout = 30; // Job timeout in seconds

    public function __construct(
        public Product $product,
        public string $type = 'created'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ApiLogger $logger): void
    {
        try {
            $logger->logBusinessEvent('product_notification_job_started', [
                'product_id' => $this->product->product_id,
                'type' => $this->type,
                'attempt' => $this->attempts()
            ]);

            // Get admin users
            $admins = User::whereHas('role', function ($query) {
                $query->where('role_name', 'admin');
            })->get();

            if ($admins->isEmpty()) {
                $logger->logBusinessEvent('product_notification_no_admins', [
                    'product_id' => $this->product->product_id
                ]);
                return;
            }

            // Send notifications to all admins
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(
                        new ProductNotification($this->product, $this->type, $admin)
                    );

                    $logger->logBusinessEvent('product_notification_sent', [
                        'product_id' => $this->product->product_id,
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'type' => $this->type
                    ]);

                } catch (\Exception $e) {
                    $logger->logError($e, [
                        'operation' => 'send_product_notification',
                        'product_id' => $this->product->product_id,
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'type' => $this->type
                    ]);

                    // Continue with other admins even if one fails
                    continue;
                }
            }

            $logger->logBusinessEvent('product_notification_job_completed', [
                'product_id' => $this->product->product_id,
                'type' => $this->type,
                'admins_notified' => $admins->count()
            ]);

        } catch (\Exception $e) {
            $logger->logError($e, [
                'operation' => 'product_notification_job',
                'product_id' => $this->product->product_id,
                'type' => $this->type,
                'attempt' => $this->attempts()
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $logger = app(ApiLogger::class);
        $logger->logError($exception, [
            'operation' => 'product_notification_job_failed',
            'product_id' => $this->product->product_id,
            'type' => $this->type,
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries
        ]);
    }
}