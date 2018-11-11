<?php

namespace MSD\Controllers;

use MSD\Controllers\BaseController;

class InstallController extends BaseController
{
    public $viewData = [];

    public function getIndex()
    {
        // retrieve flashed errors
        $errors = $this->session->getFlashBag()->all();

        if (! empty($errors['install_errors'])) {
            $this->viewData['errors'] = $errors['install_errors'];
        }

        // get the previously submitted info
        $this->viewData['host'] = $this->session->get('mysql_host', '');
        $this->viewData['port'] = $this->session->get('mysql_port', '');
        $this->viewData['user'] = $this->session->get('mysql_username', '');
        $this->viewData['pass'] = $this->session->get('mysql_password', '');

        return $this->template->display('pages/install/index.twig', $this->viewData);
    }

    public function postIndex()
    {
        // get the parameters
        $params = $this->request->all();

        // store the previously submitted config in the session
        $this->session->set('mysql_host', $params['mysql_host']);
        $this->session->set('mysql_port', $params['mysql_port']);
        $this->session->set('mysql_username', $params['mysql_username']);
        $this->session->set('mysql_password', $params['mysql_password']);

        // test the parameters
        try {
            $pdo = new \PDO('mysql:host=' . $params['mysql_host'] . ';port=' . $params['mysql_port'] . ';charset=utf8;', $params['mysql_username'], $params['mysql_password']);

            // create the config
            $path = __DIR__ . '/../config.php';

            $data = file_get_contents(__DIR__ . '/../config.new.php');

            // do some replacements
            $data = preg_replace('/<!--HOST-->/', $params['mysql_host'], $data);
            $data = preg_replace('/<!--PORT-->/', $params['mysql_port'], $data);
            $data = preg_replace('/<!--USER-->/', $params['mysql_username'], $data);
            $data = preg_replace('/<!--PASS-->/', $params['mysql_password'], $data);

            file_put_contents($path, $data);

            $this->router->redirect('dashboard');
        } catch (\PDOException $e) {
            // add the error to the flash bag
            $this->session->getFlashBag()->add('install_errors', 'There was a problem with the MySQL configuration. Please ensure you are entering the correct details.');

            $this->router->redirect('install');
        }
    }
}
