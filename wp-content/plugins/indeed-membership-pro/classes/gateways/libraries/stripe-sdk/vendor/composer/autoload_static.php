<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfa858e8c96c4ea84fe46c860f6580969
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfa858e8c96c4ea84fe46c860f6580969::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfa858e8c96c4ea84fe46c860f6580969::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfa858e8c96c4ea84fe46c860f6580969::$classMap;

        }, null, ClassLoader::class);
    }
}
