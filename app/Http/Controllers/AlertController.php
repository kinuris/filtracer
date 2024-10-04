<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function genAlerts() {
        return view('alerts.snippets.user-alert');
    }

    public function seenAll() {
        User::query()->find(Auth::user()->id)->alerts()->update(['is_read' => true]);

        return back();
    }
}
