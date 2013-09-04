<?php 

use Symfony\Component\HttpFoundation\Request;

$hitCache = function(Request $request, Silex\Application $app) {
    if ($app['mcache']) {
        $cached = $app['mcache']->get($request->getPathInfo());

        if ($cached) {
            return $app->json(json_decode($cached), 200, array('ETag' => md5($cached)));
        }
    }
};

$api = $app['controllers_factory'];

$api->get('/price/{products}', function(Request $request, $products) use ($app){
    if (!preg_match("#/#", $products)) {
        // single product
        $result = $app['em']->product->one($products);
    } else {
        // multiple product, by keys
        $result = array();
        $prods = $app['em']->product->many(explode('/', $products));

        if ($prods) {
            foreach ($prods as $prod) {
                $result[$prod['name']] = $prod;
            }
        }
    }

    if (!$result) {
        // not found
        $result = array('error' => true);
        return $app->json($result, 404);
    }

    if ($app['mcache']) {
        $app['mcache']->add($request->getPathInfo(), json_encode($result));
    }

    return $app->json($result, 200, array('ETag' => md5(json_encode($result))));
})
->assert('products', '[\w\-\._/]+')
->before($hitCache);

return $api;