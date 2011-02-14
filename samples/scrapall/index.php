<?php

require_once("common.php");
require_once("services/friends.php");

// login
$orkutApi->login();

$friends = new Friends($orkutApi);
$friends->fetchMe();
$friends->fetchUsers();
$listFriends = $friends->execute();


if($listFriends[0]['message']!='ok') 
    // FIX IT: handle error message properly
    die('Error calling orkut');
    

$listFriends = $listFriends[1]['friends']['data'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
			body { width: 100%; padding: 0; margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #5E5E5E; }
			small a, h4 a, h1 a, h3 a { color: #d93b36; text-decoration: none; }
			small a:hover, h4 a:hover, h1 a:hover, h3 a:hover { text-decoration: underline; }
			h4 { font-size: 12px; color: #999; margin: 0px 0px 13px 0px; padding: 0px; font-weight: normal; }
			.form { width: 400px; margin: 15px auto; }
            .h3bt { margin: 15px 0; padding: 0px; font-size: 20px; font-weight: normal; padding: 4px; border:#CCCCCC 1px solid; width: 400px; color: #d93b36; background: #FFFFFF; }
			.amigo { float:left; padding: 5px 15px 5px 15px; width: 370px; border-bottom:1px dotted #CCCCCC; }
			.img { float:left; margin: 0 10px 0 10px; height: 70px; width: 70px; text-align: center; overflow: hidden}  
            .img img{overflow: hidden}
			.nome { overflow:hidden; width: 132px; text-align: left; float:left; padding-top: 10px; }
			.centro { width: 400px; text-align: left; }
			.textarea { width: 400px; height: 200px; margin-bottom: 15px}
		</style>


		<script type="text/javascript">
		function checkAll() {
		  var j = 0;
		    while(true) {
		    box = eval("document.form2['friends[]']");
		    if (box != null && box[j] != undefined)
		    {
		      box[j].checked = !box[j].checked;
		    } else { break; }
		    j++;
		   }
		}
		//-->
		</script>

	</head>
	<body>

		<div class="form">

			<form action="index_action.php" name="form2" method="post">

				<div class="centro">
				      <h4><strong class="strongb">Message</strong></h4>
				</div>
				
				<textarea class="textarea" name="msg"></textarea>
				
				<div class="centro">
				      <h4><strong class="strongb">Select your friends:</strong>&nbsp;&nbsp;<a href="javascript:checkAll();">check/uncheck all</a></h4>
				</div>
				
				<input name="submit" type="submit" class="h3bt" value="SEND" />
				<div style="clear:both">You have <?php echo $listFriends['totalResults'];?> friends!</div><br />
				<div style="clear:both"></div>

				<?php
				$i=0;
				foreach($listFriends['list'] as $friend)
				{
				?>	
					<div class="amigo">

						<div class="img"><img src="<?php echo $friend['thumbnailUrl']; ?>" /></div>
					  	<div class="nome">
							<?php echo ++$i; ?>.<?php echo $friend['displayName'];?><br /><br />
							<input type="checkbox" name="friends[]" value="<?php echo $friend['id'];?>" checked/> <strong>select this friend</strong>
						</div>
					</div>
					<br />

				<?php
				}

				?>
				<div style="clear:both"></div>
				<input name="submit" type="submit" class="h3bt" value="SEND" />
				<br />
			</form>
		</div>
		<p></p>
	</body>
</html>
