<?php

use Symfony\Component\HttpFoundation\Request;
use WebLinks\Domain\Link;
use WebLinks\Domain\User;
use WebLinks\Form\Type\LinkType;
use WebLinks\Form\Type\UserType;

// Home page
$app->get('/', function () use ($app)
{
    $links = $app['dao.link']->findAll();
    return $app['twig']->render('index.html.twig', array('links' => $links));
})->bind('home');

// Login form
$app->get('/login', function (Request $request) use ($app)
{
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

// Submit link
$app->match('/link/submit', function (Request $request) use ($app)
{
    $return = '';
    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
        // A user is fully authenticated : he can add links
        $link = new Link();
        $user = $app['user'];
        $link->setAuthor($user);
        $linkForm = $app['form.factory']->create(LinkType::class, $link);
        $linkForm->handleRequest($request);
        if ($linkForm->isSubmitted() && $linkForm->isValid()) {
            $app['dao.link']->save($link);
            $app['session']->getFlashBag()->add('success', 'The link was successfully created.');
        }
        $return = $app['twig']->render('link_form.html.twig', array(
            'title' => 'New link',
            'linkForm' => $linkForm->createView(),
        ));
    } else {
        $app['session']->getFlashBag()->add('danger', 'You must be logged in.');
        $return = $app->redirect('/login');
    }
    return $return;
})->bind('link_submit');

// Admin zone
$app->get('/admin', function () use ($app){
    $links = $app['dao.link']->findAll();
    $users = $app['dao.user']->findAll();
    return $app['twig']->render('admin.html.twig', ['links' => $links, 'users' => $users]);
})->bind('admin');

// Edit an existing link
$app->match('/admin/link/{id}/edit', function ($id, Request $request) use ($app) {
    $link = $app['dao.link']->find($id);
    $linkForm = $app['form.factory']->create(LinkType::class, $link);
    $linkForm->handleRequest($request);
    if ($linkForm->isSubmitted() && $linkForm->isValid()) {
        $app['dao.link']->save($link);
        $app['session']->getFlashBag()->add('success', 'The link was successfully updated.');
    }

    return $app['twig']->render('link_form.html.twig', ['title' => 'Edit link', 'linkForm' => $linkForm->createView()]);
})->bind('admin_link_edit');
// Remove an link
$app->get('/admin/link/{id}/delete', function ($id, Request $request) use ($app) {
    $app['dao.link']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The link was successfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_link_delete');

// Add a user
$app->match('/admin/user/add', function (Request $request) use ($app) {
    $user = new User();
    $userForm = $app['form.factory']->create(UserType::class, $user);
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        // generate a random salt value
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();
        // find the default encoder
        $encoder = $app['security.encoder.bcrypt'];
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password); 
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
    }
    return $app['twig']->render('user_form.html.twig', [
        'title' => 'New user',
        'userForm' => $userForm->createView(),
    ]);
})->bind('admin_user_add');
// Edit an existing user
$app->match('/admin/user/{id}/edit', function ($id, Request $request) use ($app) {
    $user = $app['dao.user']->find($id);
    $userForm = $app['form.factory']->create(UserType::class, $user);
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was successfully updated.');
    }
    return $app['twig']->render('user_form.html.twig', ['title' => 'Edit user', 'userForm' => $userForm->createView()]);
})->bind('admin_user_edit');
// Remove a user
$app->get('/admin/user/{id}/delete', function ($id, Request $request) use ($app) {
    // Delete all associated comments
    $app['dao.link']->deleteAllByUser($id);
    // Delete the user
    $app['dao.user']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The user was successfully removed.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_user_delete');
