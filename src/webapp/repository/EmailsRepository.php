<?php

namespace analysiswebapp\webapp\repository;

use PDO;
use analysiswebapp\webapp\models\Emails;
use analysiswebapp\webapp\models\EmailsCollection;

class EmailsRepository {

	const EMAILS_DB='grabemails.emails';

	/**
	 * @var PDO
	 */
	private $pdo;
	private $mongoDb;

	public function __construct(PDO $pdo, $mongoDb) {
		$this->pdo = $pdo;
		$this->mongoDb = $mongoDb;
	}

	public function makeEmailsFromRowMg($row) {

		$e = new Emails($row);
		return $e->getDbObject();
	}

	public function find($emailsID) {

		$id = new \MongoDB\BSON\ObjectId($emailsID);

		$filter = ['_id' => $id];

		$query = new \MongoDB\Driver\Query($filter);

		$rows = $this->mongoDb->executeQuery(self::EMAILS_DB, $query);
		foreach ($rows as $row) {
			return $this->makeEmailsFromRowMg($row);
		}
		return false;
	}

	public function searchEmails($emailsID, $outerdate, $outerFrom, $innerFrom, $innersubject) {

		$filter = [];
		if ($emailsID) {
			try{
				$filter['_id'] = new \MongoDB\BSON\ObjectId($emailsID);
			}catch(\MongoDB\Driver\Exception\InvalidArgumentException $e){
				return false;
			}catch(Exception $e){
			}
		}
		if($outerdate){
			$filter['outerdate'] = new \MongoDB\BSON\Regex("{$outerdate}",'');
		}
		if($outerFrom){
			$filter['outerFrom'] = new \MongoDB\BSON\Regex("{$outerFrom}",'');
		}
		if($innerFrom){
			$filter['innerFrom'] =  new \MongoDB\BSON\Regex("{$innerFrom}",'');
		}
		if($innersubject){
			$filter['innersubject'] = new \MongoDB\BSON\Regex("{$innersubject}",'');
		}
		$options = [];

        $query = new \MongoDB\Driver\Query($filter);

    $rows = $this->mongoDb->executeQuery(self::EMAILS_DB, $query);
	return new EmailsCollection(array_map([$this, 'makeEmailsFromRowMg'], $rows->toArray()));

	}

	public function searchEmailsusers($emailsID, $outerdate, $innerFrom, $innersubject) {

		$filter = [];
		if ($emailsID) {
			try{
				$filter['_id'] = new \MongoDB\BSON\ObjectId($emailsID);
			}catch(\MongoDB\Driver\Exception\InvalidArgumentException $e){
				return false;
			}catch(Exception $e){
			}
		}
		if($outerdate){
			$filter['outerdate'] = new \MongoDB\BSON\Regex("{$outerdate}",'');
		}
		if($innerFrom){
			$filter['innerFrom'] =  new \MongoDB\BSON\Regex("{$innerFrom}",'');
		}
		if($innersubject){
			$filter['innersubject'] = new \MongoDB\BSON\Regex("{$innersubject}",'');
		}
		$options = [];

        $query = new \MongoDB\Driver\Query($filter);

    $rows = $this->mongoDb->executeQuery(self::EMAILS_DB, $query);
	return new EmailsCollection(array_map([$this, 'makeEmailsFromRowMg'], $rows->toArray()));

	}

	public function all() {

		$query = new \MongoDB\Driver\Query([]);

		$rows = $this->mongoDb->executeQuery(self::EMAILS_DB, $query);
		$rows = $rows->toArray();
		return new EmailsCollection(
				  array_map([$this, 'makeEmailsFromRowMg'], $rows)
		);

	}

	public function deleteByEmailsid($emailsID) {

		$dbWrite = new \MongoDB\Driver\BulkWrite;
		 $id   = new \MongoDB\BSON\ObjectId($emailsID);

		 $dbWrite->delete(['_id' => $id],['limit' => 1]);

		 $this->mongoDb->executeBulkWrite(self::EMAILS_DB, $dbWrite);

		 return 1;
	}

	public function save(Emails $emails) {
		$dbWrite = new \MongoDB\Driver\BulkWrite;
		$emailData = $emails->getDbObject();
		if (!is_array($emailData)) {
			var_dump($emailData);
			return;
		}
		$dbWrite->insert($emails->getDbObject());
		$this->mongoDb->executeBulkWrite(self::EMAILS_DB, $dbWrite);
	}

	public function sortEmailsByDate($emailDataObjects) {
		$sortArray = array();

		if(!$emailDataObjects){

			return $emailDataObjects;
		}
	$emailData = [];
		foreach ($emailDataObjects as $index=>$email) {
			$emailArr = json_decode(json_encode($email),true);
			$emailData[$index] = $emailArr;
			$emailData[$index]['_id']=$emailData[$index]['emailsID'] = (string) $email->_id;
			$emailData[$index]["email_timestamp"] = $emailArr['email_timestamp'] = strtotime($emailArr['innerdate']);

			foreach ($emailArr as $key => $value) {
				if (!isset($sortArray[$key])) {
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}

		$orderby = 'email_timestamp'; //change this to whatever key you want from the array
//pr($sortArray);exit;

		try{
			if(!isset($sortArray[$orderby]) || !is_array($sortArray[$orderby])){
				return [];
			}
		array_multisort($sortArray[$orderby], SORT_DESC, $emailData);
		}catch(Exception $e){

	}
		return $emailData;
	}

	public function sorttopnotifiers($emailDataObjects) {
		$sortArray = array();
		//$sortCount = array();

		if(!$emailDataObjects){

			return $emailDataObjects;
		}
	$emailData = [];
		foreach ($emailDataObjects as $index=>$email) {
			$emailArr = json_decode(json_encode($email),true);
			$emailData[$index] = $emailArr;
			$emailData[$index]['_id']=$emailData[$index]['emailsID'] = (string) $email->_id;
			$emailData[$index]["outerFrom"] = $emailArr['outerFrom'];

			foreach ($emailArr as $key => $value) {
				if (!isset($sortArray[$key])) {
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}

		$orderby = 'outerFrom'; //change this to whatever key you want from the array
//pr($sortArray);exit;

		try{
			if(!isset($sortArray[$orderby]) || !is_array($sortArray[$orderby])){
				return [];
			}
		$count = array_count_values($sortArray[$orderby]);
		arsort($count);
		}catch(Exception $e){

	}

		return $count;
	}

	public function sorttopsenders($emailDataObjects) {
		$sortArray = array();
		//$sortCount = array();

		if(!$emailDataObjects){

			return $emailDataObjects;
		}
	$emailData = [];
		foreach ($emailDataObjects as $index=>$email) {
			$emailArr = json_decode(json_encode($email),true);
			$emailData[$index] = $emailArr;
			$emailData[$index]['_id']=$emailData[$index]['emailsID'] = (string) $email->_id;
			$emailData[$index]["innerFrom"] = $emailArr['innerFrom'];

			foreach ($emailArr as $key => $value) {
				if (!isset($sortArray[$key])) {
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}

		$orderby = 'innerFrom'; //change this to whatever key you want from the array
	//pr($sortArray);exit;

		try{
			if(!isset($sortArray[$orderby]) || !is_array($sortArray[$orderby])){
				return [];
			}
		$count = array_count_values($sortArray[$orderby]);
		arsort($count);
		}catch(Exception $e){

	}
		return $count;
	}

	public function sorttopdomains($emailDataObjects) {
		$sortArray = array();
		//$sortCount = array();

		if(!$emailDataObjects){

			return $emailDataObjects;
		}
	$emailData = [];
		foreach ($emailDataObjects as $index=>$email) {
			$emailArr = json_decode(json_encode($email),true);
			$emailData[$index] = $emailArr;
			$emailData[$index]['_id']=$emailData[$index]['emailsID'] = (string) $email->_id;
			$emailData[$index]["innerFrom"] = $emailArr['innerFrom'];

			foreach ($emailArr as $key => $value) {
				if (!isset($sortArray[$key])) {
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}

		$orderby = 'innerFrom'; //change this to whatever key you want from the array
		$topdomains = preg_replace('/^.*@\s*/', '', $sortArray[$orderby]);
	//pr($sortArray);exit;

		try{
			if(!isset($topdomains) || !is_array($topdomains)){
				return [];
			}
		$count = array_count_values($topdomains);
		arsort($count);
		}catch(Exception $e){

	}
		return $count;
	}

	public function sorttopemails($emailDataObjects) {
		$sortArray = array();
		//$sortCount = array();

		if(!$emailDataObjects){

			return $emailDataObjects;
		}
	$emailData = [];
		foreach ($emailDataObjects as $index=>$email) {
			$emailArr = json_decode(json_encode($email),true);
			$emailData[$index] = $emailArr;
		}
			foreach ($emailData as $key => $value) {
				$sortArray[$key] = $value["innerFrom"] . ": \x20\x20\x20 " . $value["innersubject"];
			}

		try{
			if(!isset($sortArray) || !is_array($sortArray)){
				return [];
			}
		$count = array_count_values($sortArray);
		arsort($count);
		}catch(Exception $e){

	}
		return $count;
	}

	public function sortrecemails($emailDataObjects) {
		$sortArray = array();

		if(!$emailDataObjects){

			return $emailDataObjects;
		}
	$emailData = [];
		foreach ($emailDataObjects as $index=>$email) {
			$emailArr = json_decode(json_encode($email),true);
			$emailData[$index] = $emailArr;
			$emailData[$index]['_id']=$emailData[$index]['emailsID'] = (string) $email->_id;
			$emailData[$index]["email_timestamp"] = $emailArr['email_timestamp'] = strtotime($emailArr['innerdate']);

			foreach ($emailArr as $key => $value) {
				if (!isset($sortArray[$key])) {
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}

		$orderby = 'email_timestamp'; //change this to whatever key you want from the array
//pr($sortArray);exit;

		try{
			if(!isset($sortArray[$orderby]) || !is_array($sortArray[$orderby])){
				return [];
			}
		array_multisort($sortArray[$orderby], SORT_DESC, $emailData);
		}catch(Exception $e){

	}
		return $emailData;
	}

	public function sortrepemails($emailDataObjects) {
		$sortArray = array();

		if(!$emailDataObjects){

			return $emailDataObjects;
		}
	$emailData = [];
		foreach ($emailDataObjects as $index=>$email) {
			$emailArr = json_decode(json_encode($email),true);
			$emailData[$index] = $emailArr;
			$emailData[$index]['_id']=$emailData[$index]['emailsID'] = (string) $email->_id;
			$emailData[$index]["email_timestamp"] = $emailArr['email_timestamp'] = strtotime($emailArr['outerdate']);

			foreach ($emailArr as $key => $value) {
				if (!isset($sortArray[$key])) {
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}

		$orderby = 'email_timestamp'; //change this to whatever key you want from the array
//pr($sortArray);exit;

		try{
			if(!isset($sortArray[$orderby]) || !is_array($sortArray[$orderby])){
				return [];
			}
		array_multisort($sortArray[$orderby], SORT_DESC, $emailData);
		}catch(Exception $e){

	}
		return $emailData;
	}

}
