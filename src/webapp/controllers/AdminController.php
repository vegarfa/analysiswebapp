<?php

namespace analysiswebapp\webapp\controllers;

use analysiswebapp\webapp\Auth;
use analysiswebapp\webapp\models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    private function checkifAdmin()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
            return;
        }
	if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
            return;
        }
    }

    public function index()
    {
        $this->checkifAdmin();
        $variables = [
            'users' => $this->userRepository->all(),
            'emails' => $emails = $this->emailRepository->sortEmailsByDate($this->emailRepository->all())
        ];
        $this->render('admin.twig', $variables);
    }

//    public function test()
//    {
  //      $this->checkifAdmin();
    //    $this->render('test.twig');
    //}

    public function extendedsearchindex()
    {
        $this->checkifAdmin();
        $variables = [
            'users' => $this->userRepository->all(),
            'emails' => $emails = $this->emailRepository->sortEmailsByDate($this->emailRepository->all())
        ];
        $this->render('extendedsearch.twig', $variables);
    }

    public function indexupdateEmail()
    {
        $this->checkifAdmin();
        $this->render('emails/updateemails.twig');
    }

    public function indexemailTrends()
    {
      $filename = '/home/skolepc/analysiswebapp/src/webapp/templates/mailTrends/out/emailTrends.twig';

      if ($this->auth->guest()) {
          $this->app->flash('info', 'You must be logged in to see the Email Trends');
          $this->app->redirect("/login");
        }

      if (file_exists($filename)) {

        if (!$this->auth->isAdmin()) {
            $this->render('mailTrends/out/emailTrends.twig');
          }

        if ($this->auth->isAdmin()) {
            $this->render('mailTrends/out/emailTrends.twig');
          }

      } else {
        $this->app->flash('info', 'No Email Trends are available');
        $this->app->redirect("/analysisengine");
      }

    }

    public function indexanalysisEngine()
    {
        $this->checkifAdmin();
        $this->render('emails/analysisengine.twig');
    }

    public function indexmenupage()
    {
      if ($this->auth->guest()) {
          $this->app->flash('info', 'You must be logged in for access');
          $this->app->redirect("/login");
      }

      if (!$this->auth->isAdmin()) {
          $this->render('/menupage.twig');
      }

      if ($this->auth->isAdmin()) {
          $this->render('/menupage.twig');
      }
    }

    public function indexinfopage()
    {
      if ($this->auth->guest()) {
          $this->render('/infopage.twig');
        }

      elseif (!$this->auth->isAdmin()) {
          $this->render('/infopage.twig');
      }

      elseif ($this->auth->isAdmin()) {
          $this->render('/infopage.twig');
      }
    }

    public function destroyemails($emailsID)
    {
        $this->checkifAdmin();

        if ($this->emailRepository->deleteByEmailsid($emailsID) === 1) {
            $this->app->flash('info', "Successfully deleted email with ID: $emailsID");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error occurred. Unable to delete email with ID: $emailsID");
        $this->app->redirect('/admin');
    }

    public function destroyuser($username)
    {
      $user = $this->userRepository->findByUser($username);

        if ($this->auth->isAdmin() and $user->getUsername() !== $this->auth->getUsername()) {
            $this->userRepository->deleteByUsername($username) === 1;
            $this->app->flash('info', "Successfully deleted User with username: $username");
            $this->app->redirect('/admin');

            return;
        }
        $this->app->flash('info', "An error occurred. Unable to delete User with username: $username");
        $this->app->redirect('/');
    }
}
