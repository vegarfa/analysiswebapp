<?php

namespace analysiswebapp\webapp\controllers;

class Controller
{
    protected $app;
    protected $auth;
    protected $token;
    protected $userRepository;
    protected $emailRepository;

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
        $this->userRepository = $this->app->userRepository;
        $this->emailRepository = $this->app->emailRepository;
        $this->auth = $this->app->auth;
        $this->hash = $this->app->hash;
        $this->token = $this->app->token;
    }

    protected function render($template, $variables = [])
    {

        $variables['token'] = $this->token->getToken();

        if ($this->auth->check()) {
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = $this->auth->isAdmin();
            $variables['loggedInUsername'] = $_SESSION['user'];
        }

        print $this->app->render($template, $variables);
    }
}
