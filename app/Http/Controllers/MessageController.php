<?php

namespace App\Http\Controllers;

use App\Helpers\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage()
    {
        $msg = Message::make();

        $msg->setMessage("✅ Account Verified\nCongratulations! Your FilTracer account has been verified. Log in now to connect with other users and explore opportunities. Visit: [Your Login URL]");
        $msg->setFullPhoneNumber("639668706736");

        return $msg->send();
    }
}
