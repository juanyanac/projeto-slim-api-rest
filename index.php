<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App;

//uma rota
$app->get('/hola/{name}', function (Request $request, Response $response) use ($app){
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hola, $name");
    //mostrar tudos os parametros na tela
    var_dump($request->getQueryParams(''));

    return $response;
});


function pruebaMiddle(){
    echo "prueba middleware1"."<br/>";
}

function pruebaMiddle2(){
    echo "prueba middleware2"."<br/>";
}

//dois rotas
//rotas com só numero e só texto
$app->get('/prueba[/{ruta1:[0-9]+}[/{ruta2:[a-z]+}]]', function (Request $request, Response $response, $args = []) {
    //if (!empty($args)){
        pruebaMiddle2();
        pruebaMiddle();
        echo "Hello, " . $args['ruta1']."<br/>";
        echo "Hello, " . $args['ruta2']."<br/>";
    //}
    //else{
    //    echo "Sem rota ";
    //}
});
//$uri="/index.php/api/exemplo";
//$router=$app->router;
$app->group("/api",function() use($app){
    $app->group("/exemplo",function() use($app){
        //retorna nome
        $app->get('/nome/{nome}', function (Request $request, Response $response) use ($app){
        $nome = $request->getAttribute('nome');
        $response->getBody()->write("Nombre, $nome");
        return $response;
        })->setName('rotanome');
        //retorna sobrenome
        $app->get('/sobrenome/{sobrenome}', function (Request $request, Response $response) use ($app) {
        $sobrenome = $request->getAttribute('sobrenome');
        $response->getBody()->write("Sobrenome, $sobrenome");
        return $response;
        });
        //redireccionar pasando parametro
        $app->get('/salto', function (Request $request, Response $response) use ($app) {
        return $response->withRedirect("/index.php/api/exemplo/nome/juan");
        }); 
        //redireccionar con URL
        $app->get('/salto2', function (Request $request, Response $response) use ($app){
        //echo $app->getContainer()->get('router')->pathFor('rotanome', ['nome' => 'juan']);
        return $response->withRedirect($app->getContainer()->get('router')->pathFor('rotanome', ['nome' => 'juan']));
        }); 
    });
});

$app->run();

