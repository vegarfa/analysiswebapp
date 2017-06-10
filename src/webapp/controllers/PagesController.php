<?php

namespace analysiswebapp\webapp\controllers;

use analysiswebapp\webapp\models\User;

class PagesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function frontpage()
    {
        $this->render('frontpage.twig', [
            'users' => $this->userRepository->all()
        ]);
    }

}
