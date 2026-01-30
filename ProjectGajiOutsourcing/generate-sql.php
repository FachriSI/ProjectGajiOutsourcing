<?php
$password = 'loly123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "-- SQL untuk update password loly@gmail.com\n";
echo "UPDATE users SET password = '$hash' WHERE email = 'loly@gmail.com';\n\n";
echo "-- Kredensial login:\n";
echo "-- Email: loly@gmail.com\n";
echo "-- Password: loly123\n";
