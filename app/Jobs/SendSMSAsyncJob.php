<?php

namespace App\Jobs;

use App\Helpers\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $message = Message::make();

        $message->setMessage($this->message);
        $message->setFullPhoneNumber($this->number);

        $message->send();
    }
}
