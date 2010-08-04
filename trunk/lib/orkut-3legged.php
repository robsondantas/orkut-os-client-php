<?php
/*
 * Copyright 2010 - Robson Dantas <biu.dantas@gmail.com>
 * 
 * This file is based on opensocial-client-libraries 3legged implementation, create by
 * Google. More info can be found on: http://code.google.com/p/opensocial-php-client/
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

require_once("external/OAuth.php");

// see TODO.txt for a list of missing features.

class CurlRequest {

	public static function send($url, $method, $postBody = false, $headers = false, $ua = 'os-php-3legged 0.1') {

		$ch = curl_init();

		$request = array(
			'url' => $url,
			'method' => $method,
			'body' => $postBody,
			'headers' => $headers
		);

		// log here		

		curl_setopt($ch, CURLOPT_URL, $url);

		if ($postBody) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
		}

		// We need to set method even when we don't have a $postBody 'DELETE'
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, true);

		if ($headers && is_array($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		$data = @curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno = @curl_errno($ch);
		$error = @curl_error($ch);
		@curl_close($ch);

		if ($errno != CURLE_OK) {
			throw new Exception("HTTP Error: " . $error);
		}

		list($raw_response_headers, $response_body) = explode("\r\n\r\n", $data, 2);
		$response_header_lines = explode("\r\n", $raw_response_headers);
		array_shift($response_header_lines);
		$response_headers = array();
		foreach($response_header_lines as $header_line) {
			list($header, $value) = explode(': ', $header_line, 2);
			if (isset($response_header_array[$header])) {
				$response_header_array[$header] .= "\n" . $value;
			} else $response_header_array[$header] = $value;
		}

		$response = array('http_code' => $http_code, 'data' => $response_body, 'headers' => $headers);

		
		// log info here?

		return $response;
	}
}

class OrkutAuth {

	const REQUEST_TOKEN_URL = 'https://www.google.com/accounts/OAuthGetRequestToken';
	const AUTHORIZE_URL = 'https://www.google.com/accounts/OAuthAuthorizeToken';
	const ACCESS_TOKEN_URL = 'https://www.google.com/accounts/OAuthGetAccessToken';
	const REST_ENDPOINT = 'http://sandbox.orkut.com/social/rest/';
	const RPC_ENDPOINT = 'http://www.orkut.com/social/rpc';

	protected $oauthRequestTokenParams = array('scope' => 'http://orkut.gmodules.com/social');

	protected $consumerToken;
	protected $signature;
	protected $accessToken;

	public function __construct($consumerKey, $consumerSecret) {
		
		$this->consumerToken = new OAuthConsumer($consumerKey, $consumerSecret, NULL);
		$this->signature = new OAuthSignatureMethod_HMAC_SHA1();
	}
	
	/**
	 * How to describe 3-legged Oauth in a simple way ? Well, will try here, supposing we are not logged in
	 * 1- request a token from REQUEST_TOKEN url
	 * 2- redirect to AUTHORIZE_URL in order to get token and key
	 * 3- some parameters are passed on url, which we use to get an access token. This access token will allow us to call RPC methods.
	 * 4- after getting access token, redirect back to the url which originally sent the auth process.
	 */
	public function login() {

		// do we have an active token in the session ?
		if($this->getAccessToken()!=null) {	
			$this->accessToken = $this->getAccessToken();
		}
		else {
			// first step, nothing set, start dancing
			if(!isset($_GET['oauth_continue'])) {

				//setup the callback url, so we can go back further
				$callbackUrl = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

				// get request token
        			$token = $this->obtainRequestToken($callbackUrl);
			        //$callbackUrl .= (strpos($_SERVER['REQUEST_URI'], '?') !== false ? '&' : '?') . 'oauth_continue=1&token=' . $token->key . '&key=' . urldecode($token->secret);
			        $callbackUrl .= (strpos($_SERVER['REQUEST_URI'], '?') !== false ? '&' : '?') . 'oauth_continue=1';

				// change, instead of passing through url
				$_SESSION['orkut_key'] = $token->key;
				$_SESSION['orkut_secret'] = $token->secret;

				// now we can redirect
				$authorizeRedirect = self::AUTHORIZE_URL . "?oauth_token={$token->key}&oauth_callback=" . urlencode($callbackUrl);
				header("Location: $authorizeRedirect");

				
			}
			// ok, now we are almost done, just upgrade request token and redirect
			else {
				$this->upgradeRequestToken($_SESSION['orkut_key'], $_SESSION['orkut_secret']);
				$_SESSION['oauth_token'] = serialize($this->accessToken);
				$originalUrl = $_SESSION['oauth_callback'];

				// unset session stuff
				$_SESSION['orkut_key']=NULL;
				$_SESSION['orkut_secret']=NULL;

				unset($_SESSION['orkut_key']);
				unset($_SESSION['orkut_secret']);


				header("Location: $originalUrl");
			}
		}

	}

	protected function upgradeRequestToken($requestToken, $requestTokenSecret) {
		$ret = $this->requestAccessToken($requestToken, $requestTokenSecret);

		if ($ret['http_code'] == '200') {

			$matches = array();
			@parse_str($ret['data'], $matches);

			if (!isset($matches['oauth_token']) || !isset($matches['oauth_token_secret'])) {
				throw new Exception("Error authorizing access key (result was: {$ret['data']})");
			}
			// The token was upgraded to an access token, we can now continue to use it.
			$this->accessToken = new OAuthConsumer(urldecode($matches['oauth_token']), urldecode($matches['oauth_token_secret']));
			return $this->accessToken;
		} 
		else {
			throw new Exception("Error requesting oauth access token, code " . $ret['http_code'] . ", message: " . $ret['data']);
		}
	}

	protected function requestAccessToken($requestToken, $requestTokenSecret) {
		$accessToken = new OAuthConsumer($requestToken, $requestTokenSecret);
		$accessRequest = OAuthRequest::from_consumer_and_token($this->consumerToken, $accessToken, "GET", self::ACCESS_TOKEN_URL, array());
		$accessRequest->sign_request($this->signature, $this->consumerToken, $accessToken);
		return CurlRequest::send($accessRequest, 'GET', false, false);
	}
	


	protected function getAccessToken() {
		if(isset($_SESSION["oauth_token"]))
			return unserialize($_SESSION["oauth_token"]);
		else
			return null;
	}

	protected function obtainRequestToken($callbackUrl) {

		$_SESSION['oauth_callback'] = $callbackUrl;
		$ret = $this->requestRequestToken();
//		die("");

		if ($ret['http_code'] == '200') {

			$matches = array();
			preg_match('/oauth_token=(.*)&oauth_token_secret=(.*)/', $ret['data'], $matches);
			if (!is_array($matches) || count($matches) != 3) {
				throw new Exception("Error retrieving request key ({$ret['data']})");	
			}

			return new OAuthToken(urldecode($matches[1]), urldecode($matches[2]));

		} 
		else {
			throw new Exception("Error requesting oauth request token, code " . $ret['http_code'] . ", message: " . $ret['data']);	
		}
	}

	protected function requestRequestToken() {
		$requestTokenRequest = OAuthRequest::from_consumer_and_token($this->consumerToken, NULL, "GET", self::REQUEST_TOKEN_URL, $this->oauthRequestTokenParams);
		foreach($this->oauthRequestTokenParams as $key => $value) {	
			$requestTokenRequest->set_parameter($key, $value);
		}		

		$requestTokenRequest->sign_request($this->signature, $this->consumerToken, NULL);
		return CurlRequest::send($requestTokenRequest, 'GET', false, false);
	}
	
	protected function sign($postBody, $method='POST', $url='', $params=array()) {
		if($url=='')		
			$url = self::RPC_ENDPOINT;

		$headers = array("Content-Type: application/json");
		
		// add some parameters used to sign the request
		$params['oauth_nonce'] = md5(microtime() . mt_rand());
		$params['oauth_version'] = OAuthRequest::$version;
		$params['oauth_timestamp'] = time();
		$params['oauth_consumer_key'] = $this->consumerToken->key;

		if ($this->accessToken != null) {
			$params['oauth_token'] = $this->accessToken->key;
		}
		
		if($method=='POST') {		
			// compute our body hash, base64 + sha1
			$bodyHash = base64_encode(sha1($postBody, true));
			$params['oauth_body_hash'] = $bodyHash;
		}
		
		// create the oauth request
		$oauthRequest = OAuthRequest::from_request($method, $url, $params);
		$oauthRequest->sign_request($this->signature, $this->consumerToken, $this->accessToken);
		
		// return the signed url
		return $oauthRequest->to_url();
	}
}

class Orkut extends OrkutAuth {

	private $message;
	private $messageKeys;
	
	public function __construct($consumerKey, $consumerSecret) {
		parent::__construct($consumerKey, $consumerSecret);
	}
	
	public function addRequest(Array $request, $id) {
		if(isset($this->messageKeys[$id]))
			throw new Exception('A key with name '.$id.' already exists');
		else {
			$this->messageKeys[$id]=1;
			$request['id'] = $id;

			// multiple slashes bug when json-encoding further
			// characters like ' and " gets \\\ when encoding. Kill it now.
			if(get_magic_quotes_gpc())
				@$this->dropSlashes($request);

			$this->message[] = $request;
	
		}
	}
	
	private function dropSlashes(Array &$process) {
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} 
				else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}

	}
	
	public function execute() {
	

		$request = json_encode($this->message);
		$headers = array("Content-Type: application/json");
		$signedUrl = $this->sign($request);
		$ret = CurlRequest::send($signedUrl, 'POST', $request, $headers);
		
		//fix return bug
		$data = explode('}]{"',$ret['data']);
		if(count($data)==2)
			$data[0]=$data[0].'}]';

		
		//print_r($ret);
		return $this->mapData( json_decode($data[0],true) );
	}
	
	private function mapData(Array $ret) {
		
		$response = Array();
		
		foreach($ret as $r) {
			$response[ $r['id'] ] = $r;
		}
		
		return $response;
		
	}

	public function executeCaptcha($captchaPage, $token) {

		$signedUrl = $this->sign('','GET','http://www.orkut.com'.$captchaPage);
		
		$ret = CurlRequest::send($signedUrl, 'GET', false, false);
		return $ret;
	}
}

?>
