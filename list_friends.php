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

class GetOrkutUserInfo {

	private $profileFields = array(
					'displayName',
					'currentLocation',
					'thumbnailUrl',
					'gender',
					'name'
					);
	private $friendCount = 300;
	private $orkutApi;
	
	public function __construct($orkut) {
	
		$this->orkutApi = $orkut;
		$this->fetchMe();
		$this->fetchUsers();
		$this->execute();
	}
	  
	private function fetchMe() {
	
		// myself call
		$me = array('method' => 'people.get', 
			'params' => array('userId' => array('@me'), 'groupId' => '@self', 'fields' => $this->profileFields),
			);
		
		// add current user to the batch
		$this->orkutApi->addRequest($me,'self');
		
	}
	
	private function fetchUsers() {
		
		$friends = array('method' => 'people.get', 
						 'params' => array('userId' => array('@me'), 'groupId' => '@friends', 
						 'fields' => $this->profileFields,
						 'count' => $this->friendCount),
			);
		
		// add friends request to the batch
		$this->orkutApi->addRequest($friends, 'friends');
	}
	
	private function execute() {
		
		// try to execute the request, and stop sending an error (if we get one)
		$exec = $this->orkutApi->execute();

		if(isset($exec['self']['error']))
			GenericError::stop(1,$result['self']['error']['message']);
		
		$result[] = array('id'=>'0','message'=>'ok');
		$result[] = $exec;

		echo json_encode($result);
	
	}
	
}

//log in
$orkutApi->login();

// instantiate the class
$k = new GetOrkutUserInfo($orkutApi);

?>
