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
require_once("../services/friends.php");

class GetOrkutUserInfo extends Friends {

	function __construct($api) {
        parent::__construct($api);
    }
    
    function execute() {
    
        $this->fetchMe();
		$this->fetchUsers();
		
        echo json_encode(parent::execute());
    }
	
}

// login
$orkutApi->login();

// create the instance and print the json output
$jsonFriends = new GetOrkutUserInfo($orkutApi);
$jsonFriends->execute();

?>
