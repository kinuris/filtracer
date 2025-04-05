<?php

namespace App\Jobs;

use App\Helpers\Message;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSMSAsyncJob implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $number;

    /**
     * Create a new job instance.
     */
    public function __construct($number, $message)
    {
        $this->message = $message;
        $this->number = $number;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!preg_match('/^639\d{9}$/', $this->number)) {
            Log::warning('Invalid phone number format: ' . $this->number);

            return;
        }

        $message = Message::make();

        $message->setMessage($this->message);
        $message->setFullPhoneNumber($this->number);

        Log::info('Sending SMS to ' . $this->number . ': ' . $this->message);

        try {
            $response = $message->send();
            Log::info('SMS response: ' . json_encode($response));
        } catch (Exception $e) {
            Log::info('Failed to send SMS to ' . $this->number . ': ' . $e->getMessage());
        }
    }
}
