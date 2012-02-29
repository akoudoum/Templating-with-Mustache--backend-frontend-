<?php
//Slim
require 'Slim/Slim.php';
require 'Slim/Utils.php'; /* custom defined by me */

// Paris and Idiorm
require 'Paris/idiorm.php';
require 'Paris/paris.php';

// Models
require 'models/photo.php';

//View Engine
require 'Slim/MustacheView.php'; /* custom -> https://github.com/codeguy/Slim-Extras/tree/master/Views */

//Configure DB and View
ORM::configure('mysql:host=localhost;dbname=photos');
ORM::configure('username', 'root');
ORM::configure('password', 'root');
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

$app->run();

function getPhotos() {
	$app = Slim::getInstance();
	$photos = Model::factory('Photo')->find_many();
	$photos_json = Array();
	foreach($photos as $photo) {
		array_push($photos_json,$photo->as_array());
	}
	
	if($photos)	{
		if(isAjax($app->request()))
			echo '{"photos": ' . json_encode($photos_json) . '}';
		else
			return $app->render('list.html', array('photos' => $photos));
	}
	else {	
		$app->response()->status(404);
		$app->render('404.html'); 
	}
}
function getPhoto($name) {
	$app = Slim::getInstance();
	$photo = Model::factory('Photo')->where_equal('name', $name)->find_one();
	if($photo)	{
		if(isAjax($app->request()))
			echo '{"photos": ' . json_encode($photo->as_array()) . '}';
		else
			return $app->render('list.html', array('photos' => $photo));
	}
	else {		
		$app->response()->status(404);
		$app->render('404.html');
	}
}

function addPhoto() {
	error_log('addPhoto\n', 3, '/var/tmp/php.log');
	$request = Slim::getInstance()->request();
	$photo 	 = Model::factory('Photo')->create();
	
	$photo->name = $app->request()->post('name');
	$photo->profile_image_url = $app->request()->post('profile_image_url');
	$photo->from_user = $app->request()->post('from_user');
	$photo->text = $app->request()->post('text');
	
	$photo->save();
	
	return "Success";
}


?>
