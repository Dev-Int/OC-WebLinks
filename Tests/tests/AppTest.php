<?php

namespace WebLinks\Tests;

require_once __DIR__.'/../../vendor/autoload.php';

use Silex\WebTestCase;

/**
 * Description of AppTest
 *
 * @author dev-int
 */
class AppTest extends WebTestCase
{
    /**
     * Basic, application-wide functional test inspired by Symfony best practices.
     * Simply checks that all application URLs load successfully.
     * Duringtest execution, this method is called for each URL returned by the provideUrls method.
     *
     * @dataProvider successUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * {@inheritDoc}
     */
    public function createApplication()
    {
        $app = new \Silex\Application();

        require __DIR__.'/../../app/config/dev.php';
        require __DIR__.'/../../app/app.php';
        require __DIR__.'/../../app/routes.php';

       
        // Generate raw exceptions instead of HTLM pages if errors occur
        unset($app['exception_handler']);
        // Simulate sessions for testing
        $app['session.test'] = true;
        // Enable anonymous access to admin zone
        $app['security.access_rules'] = array();

        return $app;
    }

    public function successUrls()
    {
        return array(
            ['/'],
            ['/login'],
            ['/admin'],
            ['/admin/link/1/edit'],
            ['/admin/user/add'],
            ['/admin/user/1/edit'],
            array('/api/links'),
            array('/api/link/1'),
        );
    }
}
