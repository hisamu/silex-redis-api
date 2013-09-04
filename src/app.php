<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$params = require __DIR__.'/../config/params.php';

$app['debug'] = true;

// services
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => $params['mysql_host'], 
        'dbname' => $params['mysql_db'], 
        'user' => $params['mysql_user'], 
        'password' => $params['mysql_pass'],
    ),
));
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Predis\Silex\PredisServiceProvider(), array(
    'predis.parameters' => $params['redis'],
));
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'api' => array('pattern' => '^/api'),
        'default' => array(
            'pattern' => '^.*$',
            'anonymous' => true,
            'form' => array('login_path' => '/', 'check_path' => 'login_check'),
            'logout' => array('logout_path' => '/logout'),
            'users' => $app->share(function() use ($app) {
                return new Acme\Security\UserProvider($app['db']);
            }),
        ),
    ),
    'security.access_rules' => array(
        array('^/.+$', 'ROLE_USER'),
        array('^/api$', ''),
    )
));

// manager + cache
$app['em'] = new Acme\Manager\Manager($app['db']);
$app['mcache'] = new Acme\Cache\Redis($app['predis']);

// flashs
$app->before(function() use ($app) {
    $app['session']->start();

    $flash = $app['session']->get('flash');
    $app['session']->set('flash', null);

    if (!empty($flash))
    {
        $app['twig']->addGlobal('flash', $flash);
    }
});

// controllers
$front = require __DIR__.'/controllers/front.php';
$api = require __DIR__.'/controllers/api.php';

$app->mount('/', $front);
$app->mount('/api', $api);

return $app;