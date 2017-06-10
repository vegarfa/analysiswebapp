<?php

function pr($data){
	echo '<pre>'.print_r($data,true).'</pre>';
}

use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use analysiswebapp\webapp\Auth;
use analysiswebapp\webapp\Token;
use analysiswebapp\webapp\Hash;
use analysiswebapp\webapp\models\Emails;
use analysiswebapp\webapp\repository\UserRepository;
use analysiswebapp\webapp\repository\EmailsRepository;

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__ . '/../');
#chmod(__DIR__ . '/../web/uploads', 0700);

$app = new Slim([
    'templates.path' => __DIR__.'/webapp/templates/',
		'images.path' => __DIR__.'/webapp/images',
    'debug' => false,
    'view' => new Twig()

]);

$view = $app->view();
$view->parserExtensions = array(
    new TwigExtension(),
);

try {
    // Create (connect to) SQLite database
    $app->db = new PDO('sqlite:app.db');
    $app->mongoDb = new MongoDB\Driver\Manager("mongodb://localhost:27017/grabemails");

    // Set errormode to exceptions
    $app->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

// Wire together dependencies

date_default_timezone_set("Europe/Oslo");

$app->hash = new Hash();
$app->userRepository = new UserRepository($app->db,$app->mongoDb);
$app->emailRepository = new EmailsRepository($app->db,$app->mongoDb);
$app->auth = new Auth($app->userRepository, $app->hash,$app->mongoDb);
$app->token = new Token();

$ns ='analysiswebapp\\webapp\\controllers\\';

// Static pages
$app->get('/', $ns . 'PagesController:frontpage');
$app->get('/infopage', $ns . 'AdminController:indexinfopage')->name('infopage');
$app->get('/menupage', $ns . 'AdminController:indexmenupage')->name('menupage');

// Authentication
$app->get('/login', $ns . 'SessionsController:newSession');
$app->post('/login', $ns . 'SessionsController:create');

// Logout
$app->get('/logout', $ns . 'SessionsController:destroy')->name('logout');

// User management
$app->get('/users/new', $ns . 'UsersController:newuser')->name('newuser');
$app->post('/users/new', $ns . 'UsersController:create');

//Show user
$app->get('/users/:username', $ns . 'UsersController:show')->name('showuser');

// Administer own profile
$app->get('/profile/edit', $ns . 'UsersController:edit')->name('editprofile');
$app->post('/profile/edit', $ns . 'UsersController:update');

// Emails
$app->get('/emails', $ns . 'EmailsController:index')->name('showemails');
$app->post('/emails', $ns . 'EmailsController:search');
$app->post('/emailsforusers', $ns . 'EmailsController:searchforusers');

$app->get('/emails/:emailsID', $ns . 'EmailsController:show');
$app->get('/mailtrends', $ns . 'EmailsController:mailtrends')->name('mailtrends');
$app->get('/mailtrends/topsize', $ns . 'EmailsController:topsize')->name('topsize');
$app->get('/mailtrends/topsenders', $ns . 'EmailsController:topsenders')->name('topsenders');
$app->get('/mailtrends/topnotifiers', $ns . 'EmailsController:topnotifiers')->name('topnotifiers');
$app->get('/mailtrends/topemails', $ns . 'EmailsController:topemails')->name('topemails');
$app->get('/mailtrends/timeofrecemails', $ns . 'EmailsController:timeofrecemails')->name('timeofrecemails');
$app->get('/mailtrends/timeofrepemails', $ns . 'EmailsController:timeofrepemails')->name('timeofrepemails');

// Admin restricted area
$app->post('/admin', $ns . 'EmailsController:searchAdmin');
$app->get('/admin', $ns . 'AdminController:index')->name('admin');
$app->get('/updateemails', $ns . 'AdminController:indexupdateEmail')->name('updateEmails');
$app->post('/updateemails', $ns . 'EmailsController:updateEmail');
$app->get('/analysisengine', $ns . 'AdminController:indexanalysisEngine')->name('analysisEngine');
$app->post('/analysisengine', $ns . 'EmailsController:analysisEngine');
$app->get('/emailtrends', $ns . 'AdminController:indexemailTrends')->name('emailtrends');
$app->get('/users/:username/delete/', $ns . 'AdminController:destroyuser');
$app->get('/emails/:emailsID/delete/', $ns . 'AdminController:destroyemails');

return $app;
