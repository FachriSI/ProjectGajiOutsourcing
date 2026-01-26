<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'admin@admin.com';
$password = 'password';

$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User();
    $user->name = 'Admin';
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->save();
    echo "User created successfully.\n";
} else {
    $user->password = Hash::make($password);
    $user->save();
    echo "User already exists. Password reset to 'password'.\n";
}

echo "Email: $email\n";
echo "Password: $password\n";
