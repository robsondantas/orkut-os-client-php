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
require_once('auth/auth.php');

/**
Safety hacking, extending orkut class and overriding login function to work properly
*/
class OrkutDirect extends Orkut
{
    /* override login function */
    public function login() 
    {
		// do we have an active token in the session ?
		if($this->getAccessToken()!=null)  {
            echo "yup! already authenticated. You may close this window";
			$this->accessToken = $this->getAccessToken();
        }
		else 
        {
            
            // get request token, passing our callback as a return
            $token = $this->obtainRequestToken('http://'. $_SERVER['HTTP_HOST'].g_calback_direct_handler);
            
            // initial url
            $url = self::AUTHORIZE_URL . "?oauth_token={$token->key}";
                
            // authenticate behind the scenes. Read notes on lib/auth/auth.php first.
            //getKeys($url, '<username>', '<password>');
            
            // for testing, read from a post
            getKeys($url, $_POST['user'], $_POST['pass']);
		
            // upgrade request token
            $this->upgradeRequestToken($token->key, $token->secret);

            // ok, finally we are ready to go.
            $_SESSION['oauth_token'] = serialize($this->accessToken);
            
            // printout a message
            echo 'If you are not seeing any error messages, looks like authentication worked correctly. You may close this window';

		}
    }
}       

$orkutApi = new OrkutDirect(g_orkut_consumer_key, g_orkut_consumer_secret);

// start session, it it's not already done
if(!isset($_SESSION))
	session_start();

$_SERVER['SCRIPT_NAME']='auth_direct.php';
    
// gets the script name
$script = $_SERVER["SCRIPT_NAME"];

// check if we are asking for an authorization, or including a file with authorization
if(strstr($script,"auth_direct.php")=="") {
	if(!isset($_SESSION['oauth_token']) || $_SESSION['oauth_token']=='')
		GenericError::stop(1, 'Not authenticated');
}
else
{

	try {
		$orkutApi->login();
	}
	catch(Exception $e) {
		$_SESSION['oauth_token']='';
		GenericError::stop(1,'Cant authenticate on Orkut. Message: '.$e->getMessage());
	}
}
?>
