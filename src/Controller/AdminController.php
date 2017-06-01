<?php

namespace WebLinks\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use WebLinks\Domain\Link;
use WebLinks\Domain\User;
use WebLinks\Form\Type\LinkType;
use WebLinks\Form\Type\UserType;

/**
 * Description of AdminController
 *
 * @author dev-int
 */
class AdminController
{
    /**
     * Admin home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app)
    {
        $links = $app['dao.link']->findAll();
        $users = $app['dao.user']->findAll();
        return $app['twig']->render('admin.html.twig', ['links' => $links, 'users' => $users]);
    }

    /**
     * Edit link controller.
     *
     * @param integer $id Link id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editLinkAction($id, Request $request, Application $app)
    {
        $link = $app['dao.link']->find($id);
        $linkForm = $app['form.factory']->create(LinkType::class, $link);
        $linkForm->handleRequest($request);
        if ($linkForm->isSubmitted() && $linkForm->isValid()) {
            $app['dao.link']->save($link);
            $app['session']->getFlashBag()->add('success', 'The link was successfully updated.');
        }

        return $app['twig']->render('link_form.html.twig', ['title' => 'Edit link', 'linkForm' => $linkForm->createView()]);
    }

    /**
     * Delete link controller.
     *
     * @param type $id Link id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function deleteLinkAction($id, Request $request, Application $app)
    {
        $app['dao.link']->delete($id);
        $app['session']->getFlashBag()->add('success', 'The link was successfully removed.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * Add user controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addUserAction(Request $request, Application $app)
    {
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
    }

    /**
     * Edit user controller.
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editUserAction($id, Request $request, Application $app)
    {
        $user = $app['dao.user']->find($id);
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user was successfully updated.');
        }
        return $app['twig']->render('user_form.html.twig', ['title' => 'Edit user', 'userForm' => $userForm->createView()]);
    }

    /**
     * Delete user controller.
     *
     * @param type $id User id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function deleteUserAction($id, Application $app)
    {
        // Delete all associated comments
        $app['dao.link']->deleteAllByUser($id);
        // Delete the user
        $app['dao.user']->delete($id);
        $app['session']->getFlashBag()->add('success', 'The user was successfully removed.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }
}
