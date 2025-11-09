<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Checking user passwords...\n\n";

// Get email from command line argument or prompt
$email = $argv[1] ?? null;

if (!$email) {
    echo "Usage: php check-user-password.php <email>\n";
    echo "Example: php check-user-password.php user@example.com\n";
    exit(1);
}

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found with email: {$email}\n";
    exit(1);
}

echo "User found:\n";
echo "  ID: {$user->id}\n";
echo "  Name: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Role: {$user->role}\n";
echo "  Validated: " . ($user->is_validated ? 'Yes' : 'No') . "\n";
echo "  Password hash: " . substr($user->password, 0, 30) . "...\n";
echo "  Password length: " . strlen($user->password) . "\n";

// Check if password is valid Bcrypt format
$isValidBcrypt = preg_match('/^\$2[ay]\$\d{2}\$[.\/A-Za-z0-9]{53}$/', $user->password);
echo "  Is valid Bcrypt format: " . ($isValidBcrypt ? 'Yes' : 'No') . "\n";

// Test with common passwords
$testPasswords = ['password', '12345678', 'password123'];
$found = false;

foreach ($testPasswords as $testPassword) {
    try {
        $result = Hash::check($testPassword, $user->password);
        if ($result) {
            echo "  Password matches: '{$testPassword}'\n";
            $found = true;
            break;
        }
    } catch (\Exception $e) {
        echo "  Error checking password '{$testPassword}': " . $e->getMessage() . "\n";
    }
}

if (!$found) {
    echo "  Password does not match common test passwords.\n";
    echo "  The password hash may be corrupted.\n";
    echo "\nTo reset the password, run:\n";
    echo "  php artisan tinker\n";
    echo "  Then in tinker:\n";
    echo "    \$user = App\\Models\\User::where('email', '{$email}')->first();\n";
    echo "    \$user->password = Hash::make('your_new_password');\n";
    echo "    \$user->save();\n";
}

