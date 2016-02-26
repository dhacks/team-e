<?php

require_once 'init.php';


if (isSignin()) {
	$index_url = 'select.php';
	header("Location: {$index_url}");
	exit;
}

$error = [];
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	setToken();
}else{
	checkToken();
	$email = $_POST['email'];
	$password = $_POST['password'];
	$db = connectDb();

	if ($email === '') {
		$error['email'] = 'メールアドレスを入力してください';
	}
	if ($password === '') {
		$error['password'] = 'パスワードを入力してください';
	}else if (!$user_id = getUserId($email, $password, $db)) {
		$error['password'] = 'パスワードとメールアドレスが正しくありません';
	}else if (empty($error)) {
		session_regenerate_id(true);
		$_SESSION['user_id'] = $user_id;
		$index_url = "select.php";
		header("Location: {$index_url}");
		exit;
	}
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>signin</title>
</head>
<body style="
			background-image : url(back_in.png);
			background-repeat : no-repeat;
			background-size : cover">
	
	<form action="signin.php" method="POST">
		<div style ="margin-top:330px;margin-left:430px; font-size:15pt">
			<label for="InputEmal">メールアドレス</label>
			<input type="email" id="inputEmail" name="email" value="<?php print escape($email); ?>">
			<p><?php if (array_key_exists('email', $error)) { print escape($error['email']); }?></p>
		</div>
		<div style ="margin-top:20px;margin-left:470px; font-size:15pt">
			<label for="InputPassword">パスワード</label>
			<input type="password" id="inputPassword" name="password">
			<p><?php if (array_key_exists('password', $error)) { print escape($error['password']); }?></p>
		</div>
		<div style ="margin-top:-120px;margin-left:740px">
		<input type="hidden" name="token" value="<?php print escape($_SESSION['token']); ?>">
		<p><input type="image" src="icon.png"></p>
		</div>
	</form>
	<div style ="margin-top:-35px;margin-left:630px">
	<p>新規登録は<a href="./signup.php">こちら</a></p>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</div>
</body>
</html>