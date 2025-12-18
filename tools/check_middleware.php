<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Kernel class: " . get_class($kernel) . PHP_EOL;

// Some implementations (and static analysis tools) may not expose getRouteMiddleware()
if (method_exists($kernel, 'getRouteMiddleware')) {
    print_r($kernel->getRouteMiddleware());
    exit(0);
}

// Fallback: try the application Http Kernel concrete class (App\Http\Kernel)
try {
    $appKernel = $app->make(\App\Http\Kernel::class);
    if (property_exists($appKernel, 'routeMiddleware')) {
        // Use reflection to safely access protected/private property
        $ref2 = new \ReflectionObject($appKernel);
        if ($ref2->hasProperty('routeMiddleware')) {
            $prop2 = $ref2->getProperty('routeMiddleware');
            $prop2->setAccessible(true);
            print_r($prop2->getValue($appKernel));
            exit(0);
        }
    }
} catch (Throwable $e) {
    echo "Fallback inspection failed: " . $e->getMessage() . PHP_EOL;
}

echo "No route middleware information available." . PHP_EOL;
