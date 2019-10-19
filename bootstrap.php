<?php

require_once 'vendor/autoload.php';

use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\EventManager as EventManager,
    Doctrine\ORM\Events,
    Doctrine\ORM\Configuration,
    Doctrine\Common\Cache\ArrayCache as Cache,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\ClassLoader;

$cache = new Doctrine\Common\Cache\ArrayCache;
$annotationReader = new Doctrine\Common\Annotations\AnnotationReader;

$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
  $annotationReader,
  $cache
);

$AnnotationsDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
  $cachedAnnotationReader,
  array(__DIR__ . DIRECTORY_SEPARATOR . 'src')
);

$driverChain = new Doctrine\ORM\Mapping\Driver\DriverChain();

$config = new Doctrine\ORM\Configuration;
$config->setProxyDir('/tmp');
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(true);
$config->setMetadataDriverImpl($driverChain);
$config->setMetadataCacheImpl($cache);

AnnotationRegistry::registerFile(__DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'doctrine'.DIRECTORY_SEPARATOR.'orm'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Doctrine'.DIRECTORY_SEPARATOR.'ORM'.DIRECTORY_SEPARATOR.'Mapping'.DIRECTORY_SEPARATOR.'Driver'.DIRECTORY_SEPARATOR.'DoctrineAnnotations.php');

$evm = new Doctrine\Common\EventManager();
$em = EntityManager::create(
  array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'port' => '3306',
    'user' => 'root',
    'password' => 'root',
    'dbname' => 'trilhando_doctrine',
  ),
  $config,
  $evm
);

$app = new \Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__ . '/views'
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
