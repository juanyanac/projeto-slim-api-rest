<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App;

$app->get('/productos','getProducts'); // Using Get HTTP Method and process getProduct function
$app->get('/productos/[{id}]','getProduct'); // Using Get HTTP Method and process getProduct function
$app->get('/productos/search/[{name}]','findProductByName'); // Using Get HTTP Method and process findProductByName function
$app->post('/productos','addProduct'); // Using Post HTTP Method and process addProduct function
$app->put('/productos/[{id}]', 'updateProduct'); // Using Put HTTP Method and process updateProduct function
$app->delete('/productos/[{id}]',    'deleteProduct'); // Using Delete HTTP Method and process deleteProduct function

$app->run();

function getConnection() {
    try {
        $db_username = "root";
        $db_password = "JN_javac12:_";
        $conn = new PDO('mysql:host=localhost;dbname=pruebas', $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    return $conn;
}

//listar todos os produtos
function getProducts() {
    $sql_query = "select * from productos";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $products  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        //echo '{"products": '.json_encode($products).'}';
        header("Content-Type: application/json; charset=UTF-8");
        //echo json_encode($products, JSON_FORCE_OBJECT);
        echo '{"products": '.'{"prod": '.json_encode($products).'}'.'}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}

//lista o producto indicada pelo ID
function getProduct($request, $response, $args) {
	//$req = $request->getParsedBody();// Getting parameter with names
    $sql = "SELECT * FROM productos WHERE id=:id";
    //$paramId = $req['id']; // Getting parameter with name
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id",$args['id']);
        $stmt->execute();
        $product = $stmt->fetchObject();  
        $dbCon = null;
        echo '{"product": '.json_encode($product).'}';
        //header('Content-type: application/json; charset=utf-8');
        //echo json_encode($product, JSON_FORCE_OBJECT);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

//procurar producto pelo nome
function findProductByName($request, $response, $args) {
	//$req = $request->getParsedBody();// Getting parameter with names
	$sql = "SELECT * FROM productos WHERE UPPER(name) LIKE :query ORDER BY name";
    //$sql = "SELECT * FROM productos WHERE id=:id";
    //$paramId = $req['id']; // Getting parameter with name
    $paramName = "%".$args['name']."%"; // Getting parameter 
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("query",$paramName);
        $stmt->execute();
        $product = $stmt->fetchAll(PDO::FETCH_OBJ);  
        $dbCon = null;
        echo '{"product": '.json_encode($product).'}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

//adicionar um produto
function addProduct($request, $response){
	//pruebas con variables pasados por post
	//$my_name = $_POST['my_name'];
 	//echo "hola".$my_name;
	//$my_name = $request->getParsedBody()['my_name']; 
	//echo "hello again ".$my_name;

 	//leer entradas post
 	/*foreach ($_POST as $key => $value) {
 		# code...
 		echo "key was poste a value of $value<br>";
 	}*/
 	$req = $request->getParsedBody();// Getting parameter with names
    $sql = "INSERT INTO productos (name,description,price) VALUES (:name, :description, :price);";

	$paramName = $req['name']; // Getting parameter with name
    $paramDescription = $req['description']; // Getting parameter with 
    $paramPrice = $req['price']; // Getting parameter with names

    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("name", $paramName);
        $stmt->bindParam("description", $paramDescription);
        $stmt->bindParam("price", $paramPrice);
        $stmt->execute();
        $req['id'] = $dbCon->lastInsertId();
        $dbCon = null;
        //echo json_encode($req);  -- solo para pruebas
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

//atualizar um produto atraves do ID
function updateProduct($request, $response, $args) {
    $req = $request->getParsedBody();// Getting parameter 
    $sql = "UPDATE productos SET name=:name WHERE id=:id";
    //$paramId = $req['id']; // Getting parameter with id
    $paramName = $req['name']; // Getting parameter with name
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id",$args['id']);
        $stmt->bindParam("name",$paramName);
        $stmt->execute();
        //$product = $stmt->fetchObject();  
        $dbCon = null;
        echo '{"product": '.json_encode($req).'}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

//atualizar um produto atraves do ID
function deleteProduct($request, $response, $args) {
    $sql = "DELETE FROM productos WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id",$args['id']);
        $status = $stmt->execute();
        $dbCon = null;
        echo json_encode($status);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

