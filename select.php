<?php

require_once 'init.php';
if(!isSignin()) {
	$signin_url = 'signin.php';
	header("Location: {$signin_url}");
	exit;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body style="background-image : url(index.png);
			background-repeat : no-repeat;
			background-size : cover;
			background-position:400px 300px;" charset="utf-8">

<img src="se.png">
<div style = "margin-top:-50px;margin-left:150px;">
<h1>イベントを見る</h1></div>
<CENTER>
<p>
<a href="/map-k.php"><img src="tanabe.png" alt="TAG index" border="0"></a>
<a href="/map-i.php"><img src="ima.png" alt="TAG index" style ="margin-left:20px"border="0"></a>
</p>
<br>
<br>
<br>
<h3>イベント作成はこちら</h3>
<a href="./eventup.php"><img src="event.png" alt="TAG index" style ="margin-left:20px"border="0"></a>
</CENTER>
<br>
<br>
<div align="right">
   		<a href="signout.php">サインアウト</a>
   </div>

</body>
</html>