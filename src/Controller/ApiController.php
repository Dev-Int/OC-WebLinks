<?php

namespace WebLinks\Controller;

use Silex\Application;

/**
 * Description of ApiController
 *
 * @author dev-int
 */
class ApiController
{
    /**
     * API links controller.
     *
     * @param Application $app Silex application
     */
    public function getLinksAction(Application $app)
    {
        $links = $app['dao.link']->findAll();
        // Convert an array of objects ($link) into an array of associative arrays ($responseData)
        $responseData = array();
        foreach ($links as $link) {
            $responseData[] = array(
                'id'    => $link->getId(),
                'title' => $link->getTitle(),
                'url'   => $link->getUrl(),
                'user'  => $link->getAuthor()->getUsername(),
            );
        }
        // Create and return a JSON response
        return $app->json($responseData);
    }

    /**
     * API link controller.
     *
     * @param integer $id Link id
     * @param Application $app Silex application
     */
    public function getLinkAction($id, Application $app)
    {
        $link = $app['dao.link']->find($id);
        // Convert an object ($article) into an associative array ($responseData)
        $responseData = array(
            'id' => $link->getId(),
            'title' => $link->getTitle(),
            'url' => $link->getUrl(),
            'user' => $link->getAuthor()->getUserName(),
        );
        // Create and return a JSON response
        return $app->json($responseData);
    }
}
