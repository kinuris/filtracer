<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function dashboardView() {
        return view('alumni.dashboard');
    }    
}
