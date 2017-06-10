<?php

namespace analysiswebapp\webapp\controllers;

use analysiswebapp\webapp\models\Emails;
use analysiswebapp\webapp\validation\EmailsValidation;
use analysiswebapp\webapp\controllers\UserController;
use analysiswebapp\webapp\controllers\AdminController;

class EmailsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
      if ($this->auth->guest()) {
          $this->app->flash('info', 'You must be logged in to see emails');
          $this->app->redirect("/login");
      }

      $emails = $this->emailRepository->sortEmailsByDate($this->emailRepository->all());
      if ($emails != null)
      {
        if (!$this->auth->isAdmin()) {
            #$emails = $this->emailRepository->all();
            $this->render('emails/indexuser.twig', ['emails' => $emails]);

          }

        if ($this->auth->isAdmin()) {
            #$emails = $this->emailRepository->all();
            $this->render('emails/index.twig', ['emails' => $emails]);
        }
    }else {
      $this->app->flash('info', 'No emails available');
      $this->app->redirect("/updateemails");

      }
  }

  public function mailtrends()
  {
    if ($this->auth->guest()) {
        $this->app->flash('info', 'You must be logged in to see Mail Trends');
        $this->app->redirect("/login");
    }

    $emails = $this->emailRepository->sortEmailsByDate($this->emailRepository->all());
    if ($emails != null)
    {
      if (!$this->auth->isAdmin()) {
          #$emails = $this->emailRepository->all();
          $this->render('mailtrends.twig', ['emails' => $emails]);

        }

      if ($this->auth->isAdmin()) {
          #$emails = $this->emailRepository->all();
          $this->render('mailtrends.twig', ['emails' => $emails]);
      }
  }else {
    $this->app->flash('info', 'No emails available');
    $this->app->redirect("/updateemails");

    }
}

    public function search()
    {
      if ($this->auth->isAdmin()) {
      $request = $this->app->request;
      $emailsID = trim($request->post('emailsID'));
      $outerdate = trim($request->post('outerdate'));
      $outerFrom = trim($request->post('outerFrom'));
      $innerFrom = trim($request->post('innerFrom'));
      $innersubject = trim($request->post('innersubject'));

      $emails = $this->emailRepository->sortEmailsByDate($this->emailRepository->all());

      if ($emailsID ||$outerdate ||$outerFrom || $innerFrom || $innersubject)
      {

        $searchQuery = $this->emailRepository->sortEmailsByDate($this->emailRepository->searchEmails(
					$emailsID, $outerdate, $outerFrom, $innerFrom, $innersubject));
      }else{
        $searchQuery = "";
      }

      $this->render('emails/index.twig', [
          'emails' => $emails,
          'search' => $searchQuery
      ]);
    }

        else if (!$this->auth->isAdmin()) {
        $request = $this->app->request;
        $emailsID = trim($request->post('emailsID'));
        $outerdate = trim($request->post('outerdate'));
        $innerFrom = trim($request->post('innerFrom'));
        $innersubject = trim($request->post('innersubject'));

        $emails = $this->emailRepository->sortEmailsByDate($this->emailRepository->all());

        if ($emailsID ||$outerdate || $innerFrom || $innersubject)
        {

          $searchQuery = $this->emailRepository->sortEmailsByDate($this->emailRepository->searchEmailsusers(
  					$emailsID, $outerdate, $innerFrom, $innersubject));
        }else{
          $searchQuery = "";
        }

        $this->render('emails/indexuser.twig', [
            'emails' => $emails,
            'search' => $searchQuery
        ]);
      }
    }

    public function searchAdmin()
    {

      $request = $this->app->request;
      $emailsID = trim($request->post('emailsID'));
      $outerdate = trim($request->post('outerdate'));
      $outerFrom = trim($request->post('outerFrom'));
      $innerFrom = trim($request->post('innerFrom'));
      $innersubject = trim($request->post('innersubject'));

      $emails = $this->emailRepository->sortEmailsByDate($this->emailRepository->all());
      $users = $this->userRepository->all();

      if ($emailsID ||$outerdate ||$outerFrom || $innerFrom || $innersubject)
      {

        $searchQuery = $this->emailRepository->sortEmailsByDate($this->emailRepository->searchEmails(
					$emailsID, $outerdate, $outerFrom, $innerFrom, $innersubject));
      }else{
        $searchQuery = "";
      }

      $this->render('admin.twig', [
          'emails' => $emails,
          #'users' => $users,
          'search' => $searchQuery
      ]);
    }

    public function show($emailsID)
    {
        if ($this->auth->isAdmin()) {
        $emails = $this->emailRepository->find($emailsID);

        $username = $_SESSION['user'];
        #$user = $this->userRepository->findByUser($username);

        $request = $this->app->request;
        $message = $request->get('msg');

        $variables = [];

        if($message) {
            $variables['msg'] = $message;

        }

      $html = [];
      $skipped = array('_id','outerFrom','outerdate','innerFrom','innermessageid','received_email','innerdate','body','metadata','received_domain','received_ip','received_foremail','outermessageid', 'outersubject','innersubject','outerTo','innerTo','emailsID');
      $fieldsToEncode = ['to','from','replyto','reply-to'];
      #'innmsg',
        foreach($emails as $field=>$value){
          $value = trim($value);
          if(!$value) continue;
          if($field=='full_headers') continue;
          if(in_array($field, $skipped)) {
            continue;
          }
          if(in_array($field,$fieldsToEncode)){
        		$value=  htmlentities($value);
        	}
          $field = str_replace(" ","-",ucwords(str_replace("-"," ",$field)));
        $html[] = "<tr><td>{$field}</td><td>$value</td></tr>";
        }
        $emails->all_fields = join("\n",$html);

          $this->render('emails/show.twig', [
              'emails' => $emails,
              #'user' => $user,
              'flash' => $variables
            ]);
        }

        else if (!$this->auth->isAdmin()) {
          $emails = $this->emailRepository->find($emailsID);

          $username = $_SESSION['user'];
          #$user = $this->userRepository->findByUser($username);

          $request = $this->app->request;
          $message = $request->get('msg');

          $variables = [];

          if($message) {
              $variables['msg'] = $message;

          }

        $html = [];
        $skipped = array('_id','outerFrom','outerdate','innerFrom','innermessageid','received_email','innerdate','body','metadata','received_domain','received_ip','received_foremail','outermessageid', 'outersubject','innersubject','outerTo','innerTo','emailsID');
        $fieldsToEncode = ['to','from','replyto','reply-to'];
        #'innmsg',
          foreach($emails as $field=>$value){
            $value = trim($value);
            if(!$value) continue;
            if($field=='full_headers') continue;
            if(in_array($field, $skipped)) {
              continue;
            }
            if(in_array($field,$fieldsToEncode)){
          		$value=  htmlentities($value);
          	}
            $field = str_replace(" ","-",ucwords(str_replace("-"," ",$field)));
          $html[] = "<tr><td>{$field}</td><td>$value</td></tr>";
          }
          $emails->all_fields = join("\n",$html);

            $this->render('emails/showusers.twig', [
                'emails' => $emails,
                #'user' => $user,
                'flash' => $variables
              ]);
          }
        }
	 function _generateHtmlFromArray($header,$data){
		 $html=["<tr>"];
		 if(is_array($data)){

			 $html[] = "<td>";
			 $html[] = "<table>";
			 foreach($data as $key=>$value){
				 if(is_numeric($key)){
					 $key = "";
				 }
				 $html[]="<tr><td>$key";

				 $html[] = $this->_generateHtmlFromArray($key,$value);
				 $html[]  ="</tr>";
			 }
			 $html[] = "</table>";
		 }else{
			 $html[] = "<td>$data</td>";
		 }
		 $html[] = "</tr>";

		 return join("\n",$html);

	 }

    /*
     * This is called from ajax in javascript
     */
    public function updateEmail()
    {
      if ($this->auth->isAdmin()) {

        // get and read json file from python script
        $result = exec('/usr/bin/python2.7 /home/skolepc/analysiswebapp/src/webapp/scripts/grabemails.py /tmp');

        $data = [
            'msg' => 'Emails have been retrieved from emailserver and updated in the database',
        ];

        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->write(json_encode($data));

      }
  }

  /*
   * This is called from ajax in javascript
   */
  public function analysisEngine()
  {
    if ($this->auth->isAdmin()) {
      // get data from database to make statistics
      $result = exec('/usr/bin/python2.7 /home/skolepc/analysiswebapp/src/webapp/templates/mailTrends/main.py --server=e36.ehosts.com --use_ssl --username=inbox@automaticanalysis.eu --skip_labels');

      $data = [
          'msg' => 'Analysis has been completed, and email trends generated',
      ];

      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->write(json_encode($data));
    }
  }

}
