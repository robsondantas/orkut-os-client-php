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

// Note this is a really simple example about handling the session. Shouldnt be used on a production system

require("../lib/orkut-3legged.php");
session_start();

$orkut = (isset($_SESSION['oauth_token']) && $_SESSION['oauth_token']!='')? 1:0;

// debuggin purpose, you can disable this.
if(isset($_SESSION['oauth_token']))
	print_r(unserialize($_SESSION['oauth_token']));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Session</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript">
			
			var orkutSession=<?php echo $orkut;?>;
			
			$(document).ready(function(){checkSession();});
			
			function checkSession() {
			
				if(orkutSession) {
					$("#orkut").html("Orkut: logged in");
				}
				else {
					$("#orkut").html("Orkut: <a href='../auth.php' target='blank'>login</a>");
				}
						
			}
			
		</script>
	</head>
	<body>
		<div id="orkut"></div>
	</body>
</html>
