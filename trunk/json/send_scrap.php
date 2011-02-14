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

require_once("../auth.php");
require_once("../services/scrap.php");

class OrkutSendMessage extends Scrap {

	public function __construct($api) {
        parent::__construct($api);
    }
    
    public function setCaptcha($captchaToken, $captchaValue) {
        parent::setCaptchaRequest($captchaToken, $captchaValue);
    }
    
    public function send($uids, $message) {
        echo parent::send($uids, $message);
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
$s = new OrkutSendMessage($orkutApi);

// we might have a captcha sent through post. We need to pass it to our class in order to have
// our scrap sent.
if(isset($_POST['captchaToken']) && isset($_POST['captchaValue'])) {
	$s->setCaptcha($_POST['captchaToken'], $_POST['captchaValue']);
}


$s->send($uids, $message);

?>
