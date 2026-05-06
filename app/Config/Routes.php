<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/etudiants', 'EtudiantController::index');
$routes->get('/etudiants/(:num)', 'EtudiantController::show/$1');
$routes->get('/etudiants/(:num)/notes', 'NoteController::studentNotes/$1');
$routes->post('/etudiants/(:num)/notes/store', 'NoteController::storeForStudent/$1');
$routes->post('/etudiants/(:num)/notes/update/(:num)', 'NoteController::updateForStudent/$1/$2');
$routes->post('/etudiants/(:num)/notes/delete/(:num)', 'NoteController::deleteForStudent/$1/$2');

$routes->get('/notes/create', 'NoteController::create');
$routes->post('/notes/store', 'NoteController::store');
