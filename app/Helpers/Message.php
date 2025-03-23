<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Http;

class IncompleteMessageException extends Exception {}
class RequestMessageProblemException extends Exception {}
class NoPermissionMessageException extends Exception {}

class Message
{
    private string|int $orgId;
    private string $message;
    private string $fullPhoneNumber;

    public static function make(): self
    {
        $msg = new Message();
        $msg->setOrgId('PhilSMS');

        return $msg;
    }

    public function setOrgId($orgId)
    {
        $this->orgId = $orgId;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setFullPhoneNumber($fullPhoneNumber)
    {
        $this->fullPhoneNumber = $fullPhoneNumber;
    }

    public function send()
    {
        if (empty($this->orgId) || empty($this->message) || empty($this->fullPhoneNumber)) {
            throw new IncompleteMessageException();
        }

        $request = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('SMS_API_KEY'),
            'Accept' => 'application/json'
        ])->withBody(json_encode([
            'sender_id' => $this->orgId,
            'type' => 'unicode',
            'message' => $this->message,
            'recipient' => $this->fullPhoneNumber
        ]));

        $response = $request->post('https://app.philsms.com/api/v3/sms/send');

        $status = $response->status();
        if ($status !== 200) {
            throw new RequestMessageProblemException($response);
        }

        return $response->json();
    }
}
