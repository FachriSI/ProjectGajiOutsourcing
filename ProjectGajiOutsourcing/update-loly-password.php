<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'loly@gmail.com';
$password = 'loly123';

// Find or create user
$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User();
    $user->name = 'Loly';
    $user->email = $email;
    echo "Creating new user: $email\n";
} else {
    echo "Updating existing user: $email\n";
}

$user->password = Hash::make($password);
$user->save();

echo "Success!\n\n";
echo "=== Login Credentials ===\n";
echo "Email: $email\n";
echo "Password: $password\n";
echo "=========================\n";
