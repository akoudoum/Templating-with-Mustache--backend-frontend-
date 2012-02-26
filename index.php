<?php
//Slim
require 'Slim/Slim.php';
require 'Slim/Utils.php'; /* custom defined by me */

// Paris and Idiorm
require 'Paris/idiorm.php';
require 'Paris/paris.php';

// Models
require 'models/Photo.php';

//View Engine
require 'Slim/MustacheView.php'; /* custom -> https://github.com/codeguy/Slim-Extras/tree/master/Views */


MustacheView::$mustacheDirectory = 'Slim';

$app = new Slim(array(
    'view' => 'MustacheView',
    'templates.path' => 'views'
));

$app->get('/', function() use($app){
	$app->render('index.html');	
});

$app->get('/photos', 'getPhotos');
$app->get('/photos/:id','getPhoto');
$app->post('/photos', 'addPhoto');
$app->delete('/photos/:id',	'deletePhoto');

$app->run();

function getPhotos() {
	$app = Slim::getInstance();
	$sql = "select * FROM photos ";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		if(isAjax($app->request()))
			echo '{"photos": ' . json_encode($photos) . '}';
		else
			return $app->render('list.html', array('photos' => $photos));	
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function getPhoto($name) {
	$app = Slim::getInstance();
	$sql = "select * FROM photos WHERE name=:name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $name);
		$stmt->execute();
		$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		if(isAjax($app->request()))
			echo '{"photos": ' . json_encode($photos) . '}';
		else
			return $app->render('list.html', array('photos' => $photos));	
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addPhoto() {
	error_log('addPhoto\n', 3, '/var/tmp/php.log');
	$request = Slim::getInstance()->request();
	$photo = json_decode($request->getBody());
	$sql = "INSERT INTO photos (name, profile_image_url,from_user,text) VALUES (:name, :profile_image_url,:from_user,:text)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $photo->name);
		$stmt->bindParam("profile_image_url", $photo->profile_image_url);
		$stmt->bindParam("from_user", $photo->from_user);
		$stmt->bindParam("text", $photo->text);
		$stmt->execute();
		$photo->id = $db->lastInsertId();
		$db = null;
		echo json_encode($photo); 
	} catch(PDOException $e) {
		error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deletePhoto($id) {
	$sql = "DELETE FROM photos WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}



function getConnection() {
	$dbhost="localhost";
	$dbuser="root";
	$dbpass="root";
	$dbname="photos";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>
