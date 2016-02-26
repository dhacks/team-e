<?php
require_once 'init.php';

//変数の初期化
$error = [];
$user_name = '';
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user_name = $_POST['user_name'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$db = connectDb();

	//ユーザの文字数チェック
	if (mb_strlen($user_name) < 3 || mb_strlen($user_name) > 15) {
		$error['user_name'] = '3文字以上15文字以下にしてください';
	}

	//メールアドレスが入力されているかのチェック
	if ($email === '') {
		$error['email'] = 'メールアドレスを入力してください';
	//メールアドレスの形式が正しいかどうかチェック
	}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error['email'] = 'メールアドレスの形式が正しくないです';
	//メールアドレスが既に登録されているかどうかチェック
	}elseif (emailExists($email, $db)) {
		$error['email'] = 'このメールアドレスは既に登録されています';
	}

	//パスワードが英数字であることかつ文字数チェック
	if (!preg_match('/^[a-zA-Z0-9]{4,8}$/', $password)) {
		$error['password'] = '4文字以上8文字以下の英数字にしてください';
	}

	if (empty($error)) {
		# code...
	


		$hash = password_hash($password, PASSWORD_DEFAULT);

		$sql = 'INSERT INTO users (user_name, email, password) VALUES (:user_name, :email, :password)';

		$statement = $db->prepare($sql);

		$statement->bindValue(':user_name', $user_name, PDO::PARAM_STR);
		$statement->bindValue(':email', $email, PDO::PARAM_STR);
		$statement->bindValue(':password', $hash, PDO::PARAM_STR);

		if($statement->execute()) {
			$signin_url = "signin.php";
			header("Location: {$signin_url}");
			exit;
		}else{
			print("データベースの挿入に失敗しました");
		}
	}
}
  
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>signup</title>
  </head>
<body>
		<div style="margin-top:200px;margin-left:200px;">
<img src="new_ac.png">
		</div>
	<form action="./signup.php" method="POST">
		<CENTER>
		
		<div style="margin-top:-100px;">
			
			<label for="userName">ユーザ名（必須）</label>
			<input type="text" id="userName" name="user_name" placeholder="3文字以上15文字以下"/>
			<p><?php if (array_key_exists('user_name', $error)) { print escape($error['user_name']);}  ?></p>
		</div>
		<div>
			<label for="email">メールアドレス（必須）</label>
			<input type="email" id="email" name="email"/>
			<p><?php if (array_key_exists('email', $error)) { print escape($error['email']);}  ?></p>
		</div>
		<div>
			<label for="password">パスワード（必須）</label>
			<input type="password" id="password" name="password" placeholder="4文字以上8文字以下"/ >
			<p><?php if (array_key_exists('password', $error)) { print escape($error['password']);}  ?></p>
		</div>
		<button type="submit">新規登録</button>
	</form>
	<p>ログインは<a href="signin.php">こちら</a></p>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		</CENTER>
		<div style="margin-top:-570px;margin-left:500px;">
<img src="new_bg.png">
		</div>
</body>
</html>
