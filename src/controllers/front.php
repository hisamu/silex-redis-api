<?php 

use Symfony\Component\HttpFoundation\Request;

$front = $app['controllers_factory'];

$front->get('/', function(Request $request) use ($app){
    return $app['twig']->render('index.html.twig', array(
         'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})
->bind('home');

$front->get('/expire', function(Request $request) use ($app){
    if ($request->get('path')) {
        $app['mcache']->expire($request->get('path'));
    }

    return 'Ok';
});

return $front;