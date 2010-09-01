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

class GetOrkutScraps {

	private $orkutApi;
	
	public function __construct($orkut) {
	
		$this->orkutApi = $orkut;
		$this->fetchScraps();
		$this->execute();
	}
	  
	private function fetchScraps() {

		$msg = array('method' => 'messages.get',
			     'params' => array('userId' => array('@me'),'groupId' => '@friends', 'pageType' =>'first', 'messageType'=>'public_message'));
		
		// add current user to the batch
		$this->orkutApi->addRequest($msg,'scraps');
		
	}
		
	private function execute() {
		
		// try to execute the request, and stop sending an error (if we get one)
		$exec = $this->orkutApi->execute();

		if(isset($exec['scraps']['error']))
			GenericError::stop(1,$result['scraps']['error']['message']);
		
		$result[] = array('id'=>'0','message'=>'ok');
		$result[] = $exec;

		echo json_encode($result);
	
	}
	
}

//log in
$orkutApi->login();

// instantiate the class
$k = new GetOrkutScraps($orkutApi);

?>
