<?php
/*
 * Copyright 2010 - Robson Dantas <biu.dantas@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once("auth.php");

class OrkutSendMessage {

	private $uid;
	private $message;
	private $orkutApi;
	
	public function __construct($orkut, Array $uid, $message) {
	
			$this->orkutApi = $orkut;
			$this->uid = $uid;
			$this->message = $message;
	}

	public function setCaptchaRequest($captchaToken, $captchaValue) {
		$captcha = array('method' => 'captcha.answer',
						 'params' => array('userId' => array('@me'),
								   'groupId' => '@self',
								   'captchaAnswer' => $captchaValue,
								   'captchaToken' => $captchaToken));
						
		$this->orkutApi->addRequest($captcha,'captcha');
	}
	
	public function setOrkutApi($orkut) {
		$this->orkutApi = $orkut;
	}
	
	public function send() {
	
		if(count($this->uid)==0)
			GenericError::stop(1,'No uids specified!');
	
		// batch messages
		foreach($this->uid as $uid) {
			$message = array('method' => 'messages.create',
			 'params' => array('userId' => array($uid), 
					   'groupId' => '@self', 
					   'message' => array('recipients' => array(1), 
								  'body' => $this->message, 
								  'title' => 'sent at '. strftime('%X')), 
								  'messageType'=> 'public_message'));
					   
			$this->orkutApi->addRequest($message, $uid);
		}
		
		$ret = Array();

		$exec = $this->orkutApi->execute();
		$ret[] = $this->checkError($exec);

		// execute and return a json		
		return json_encode($ret);
		
	}

	// basically captcha and generic error handling	
	private function checkError($data) {

		$ret = Array();
		$error=false;
		foreach($data as $uidData) {
			
			// orkut specific
			if(isset($uidData['error']))
			{
				$error=true;

				// append captcha
				if(isset($uidData['error']['data']) && isset($uidData['error']['data']['captchaToken'])) {
					$captchaToken = $uidData['error']['data']['captchaToken'];
					$captchaUrl = g_captcha_handler.'?captchaUrl='. $uidData['error']['data']['captchaUrl'];
					$ret = array('id'=>'2', 'message'=>'captcha!', 'captchaToken'=>$captchaToken,'captchaUrl'=>$captchaUrl);
					break;
				}
				else {
					$ret = array('id'=>'1','message'=>'Error sending message');					
					break;
				}
			}
		}	

		if(!$error)
			$ret = array('id'=>'0','message'=>'ok');

		return $ret;
	}
}

// just a simple check, we need uid and message to proceed
if(!isset($_POST['uids']))
	GenericError::stop(1, 'At least 1 uid must be specified');
else if(!isset($_POST['message']))
	GenericError::stop(1, 'Message missing');

// list of uids separated by comma
$uids = explode(',',$_POST['uids']);

// message
$message = $_POST['message'];

//log in
$orkutApi->login();

// instantiate this class
$s = new OrkutSendMessage($orkutApi, $uids, $message);

// we might have a captcha sent through post. We need to pass it to our class in order to have
// our scrap sent.
if(isset($_POST['captchaToken']) && isset($_POST['captchaValue'])) {
	$s->setCaptchaRequest($_POST['captchaToken'], $_POST['captchaValue']);
}


echo $s->send();

?>
