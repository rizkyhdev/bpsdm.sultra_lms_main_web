<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/student/dashboard', 'GET');
$user = App\Models\User::where('email', 'rizky2.student@gmail.com')->first();
$request->setUserResolver(function() use ($user) {
    return $user;
});
Auth::login($user);

$response = $kernel->handle($request);
echo "Dashboard status: " . $response->getStatusCode() . "\n";
file_put_contents('dashboard_output.html', $response->getContent());
echo "Saved to dashboard_output.html\n";
