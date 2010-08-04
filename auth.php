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

require_once('globals.php');

$orkutApi = new Orkut(g_orkut_consumer_key, g_orkut_consumer_secret);

// start session, it it's not already done
if(!isset($_SESSION))
	session_start();

// gets the script name
$script = $_SERVER["SCRIPT_NAME"];

// check if we are asking for an authorization, or including a file with authorization
if(strstr($script,"auth.php")=="") {
	if(!isset($_SESSION['oauth_token']) || $_SESSION['oauth_token']=='')
		GenericError::stop(1, 'Not authenticated');
}
else{

	try {
		$orkutApi->login();
	}
	catch(Exception $e) {
		$_SESSION['oauth_token']='';
		GenericError::stop(1,'Cant authenticate on Orkut');
	}
}
?>
