<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function genAlerts() {
        return view('alerts.snippets.user-alert');
    }
}
