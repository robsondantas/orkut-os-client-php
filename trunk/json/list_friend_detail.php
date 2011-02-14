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

/**
* This class allows you to get information of an specific user, passing uid as a query-string.
* Returns a json-encoded message. See profileFields below to add/remove fields. Available info on:
* http://code.google.com/apis/orkut/docs/orkutdevguide/orkutdevguide-0.8.html - goto list of profile fields
*/
class GetUserDetail {

	private $profileFields = array(
					'displayName',
					'currentLocation',
					'thumbnailUrl',
					'gender',
					'name',
                    'profileUrl'
					);
                    
	private $orkutApi;
    private $uid;
	
	public function __construct($orkut, $uid) {
	
		$this->orkutApi = $orkut;
        $this->uid = $uid;
        
		$this->fetchUser();
		$this->execute();
	}
	  
	private function fetchUser() {
	
		// myself call
		$me = array('method' => 'people.get', 
			'params' => array('userId' => array($this->uid), 'groupId' => '@self', 'fields' => $this->profileFields),
			);
		
		// add current user to the batch
		$this->orkutApi->addRequest($me,'self');
		
	}
	
	private function execute() {
		
		// try to execute the request, and stop sending an error (if we get one)
		$exec = $this->orkutApi->execute();

		if(isset($exec['self']['error']))
			GenericError::stop(1,$exec['self']['error']['message']);
		
		$result = array();
		$result[] = array('id'=>'0','message'=>'ok');
		$result[] = $exec;

		echo json_encode($result);
	
	}
	
}

//log in
$orkutApi->login();

// instantiate the class
$k = new GetUserDetail($orkutApi, $_GET['uid']);

?>
