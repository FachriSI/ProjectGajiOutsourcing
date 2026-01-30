<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $logs = \App\Models\ActivityLog::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        return view('profile.index', compact('user', 'logs'));
    }
}
