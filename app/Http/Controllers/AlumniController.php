<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniController extends Controller
{
    public function dashboardView()
    {
        return view('alumni.dashboard');
    }

    public function alumniProfileView()
    {
        $user = Auth::user();

        return view('alumni.profile')
            ->with('user', $user);
    }

    public function updateProfileView() {
        $user = Auth::user();

        return view('alumni.update-profile')
            ->with('user', $user);
    }
}
