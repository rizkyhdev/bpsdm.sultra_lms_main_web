<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get admin credentials from environment variables with defaults
$email = getenv('ADMIN_EMAIL') ?: 'admin@bpsdmsultra.go.id';
$password = getenv('ADMIN_PASSWORD') ?: 'passwordbpsdmsultra';
$name = getenv('ADMIN_NAME') ?: 'Administrator BPSDM';
$nip = getenv('ADMIN_NIP') ?: '197801012005011001';
$jabatan = getenv('ADMIN_JABATAN') ?: 'Administrator';
$unitKerja = getenv('ADMIN_UNIT_KERJA') ?: 'BPSDM Sultra';

// Check if admin user already exists
$user = User::where('email', $email)->first();

if (!$user) {
    // Create admin user
    User::create([
        'nip' => $nip,
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'jabatan' => $jabatan,
        'unit_kerja' => $unitKerja,
        'role' => 'admin',
        'is_validated' => true,
    ]);
    
    echo "Admin user created successfully.\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";
} else {
    echo "Admin user already exists.\n";
}

