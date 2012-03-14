<?php


if (!class_exists('Composer\\Autoload\\ClassLoader', false)) {
    require __DIR__.'/ClassLoader.php';
}

return call_user_func(function() {
    $loader = new \Composer\Autoload\ClassLoader();

    $map = require __DIR__.'/autoload_namespaces.php';

    foreach ($map as $namespace => $path) {
        $loader->add($namespace, $path);
    }

    $loader->register();

    return $loader;
});
