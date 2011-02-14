<?php
/*
 * Copyright 2010 - Robson Dantas <biu.dantas@gmail.com>
 * 
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
 
 /*
  * This page is ridiculous simple, and of course was made on purpose. Just do some cool hacks in order
  * to handle post, or whatever you want to send here properly.
  *
  * Basically, you need to send two post variables here: friends - comma delimited list of uids and message - the message you want to send.
  * If you want to test sending messages without having to choose all your friends, configure $ID_TEST below and uncomment makeFakeArray .
  * Note: Take care: flooding yourself may block your account for few hours :)
  */



function makePhrase(){

	$m='';
	for($i=0; $i<300; $i++){
		$m.= chr(rand(32,122));
	}

	return $m;
}


// in order to test, let´s fill an array with my id
function makeFakeArray() {

    $ID_TEST='xxxxxxxxxxxxx';

	$g=Array();
	for($a=0; $a<250; $a++)
		$g[]=$ID_TEST;
        
    $_POST['friends'] = $g;
    $_POST['msg'] = makePhrase();
}

/* uncomment the line below if you want to test sending multiple messages
* to an specific id. Had to do this in order to test, since dont wanted to send hundreds of testing messages.
*/
//makeFakeArray();


// small checked, needs to be improved.
if(!isset($_POST['friends']) || !isset($_POST['msg'])){
    die('Post parameters where not sent. Stop!');
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
			body {width: 100%; padding: 0; margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #5E5E5E; }
			.h3bt { margin: 15px 0; padding: 0px; font-size: 20px; font-weight: normal; padding: 4px; border:#CCCCCC 1px solid; width: 400px; color: #d93b36; background: #FFFFFF; }
			h3 { margin: 15px 0; padding: 0px; font-size: 20px; font-weight: normal; padding: 4px; border:#CCCCCC 1px solid; text-align: right; }
			small a,
			h4 a,
			h1 a,
			h3 a { color: #d93b36; text-decoration: none; }
			small a:hover,
			h4 a:hover,
			h1 a:hover,
			h3 a:hover { text-decoration: underline; }
			h4 { font-size: 12px; color: #999; margin: 0px 0px 13px 0px; padding: 0px; font-weight: normal; }
			.form { width: 400px; margin: 15px auto; }
			.captcha { width: 200px; height: 70px; background: #d93b36; float:right; }
			small { text-align: right; display: block; }
			.center {text-align:center; }
			.nome { overflow:hidden; width: 132px; text-align: left; float:left; padding-top: 10px; }
			.centro { width: 400px; text-align: left; }
			.textarea { width: 400px; height: 200px; margin-bottom: 15px}
		</style>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/orkut.js"></script>
		<script type="text/javascript">

		//globals
		var orkut;
		var message="<?php echo urlencode(stripslashes($_POST['msg']));?>";

        /**
         * When dom is ready, this function will be called and all the login handled
         * inside orkut.js.
        */
		function sendScrap() {
			
			var friends = Array();
			orkut = new Orkut();
			
			orkut.load('<?php echo implode(',',$_POST['friends']); ?>', message);
			
		}


		$(document).ready(function(){ sendScrap(); });

		</script>
	</head>
	<body>

		<div class="form">

			<h1>Wait while messages are sent</h1>
			<div id="info">
				<div id="de" style="float: left">0</div>
				<div id="total">&nbsp;of <?php echo count($_POST['friends']);?></div>
			</div>
			<div id="captcha"></div>
		</div>
	</body>
</html>


