<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'Admin') {
            $logs = \App\Models\ActivityLog::with('user')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        } else {
            $logs = \App\Models\ActivityLog::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        }

        return view('profile.index', compact('user', 'logs'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // Log Activity manually if needed, or rely on Observer (which handles 'updated' event)
        // Since we have an observer, it should log automatically.

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui!');
    }
}
