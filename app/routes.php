<?php

use Symfony\Component\HttpFoundation\Request;
use WebLinks\Domain\Link;
use WebLinks\Form\Type\LinkType;

// Home page
$app->get('/', function () use ($app) {
    $links = $app['dao.link']->findAll();
    return $app['twig']->render('index.html.twig', array('links' => $links));
})->bind('home');

// Login form
$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

// Submit link
$app->match('/link/submit', function (Request $request) use ($app) {
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
        $return = $app->redirect('/');
    }
    return $return;
})->bind('link_submit');
