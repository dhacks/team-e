<?php
require_once 'init.php';

if(!isSignin()) {
	$signin_url = 'signin.php';
	header("Location: {$signin_url}");
	exit;
}
//変数の初期化
$error = [];
$event_name = '';
$fac_id = '';
$content = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$user_id = $_SESSION['user_id'];
	$fac_id = $_POST['fac_id'];
	$event_name = $_POST['event_name'];
	$content = $_POST['content'];
	$url = $_POST['url'];

	$db = connectDb();

	//イベント名文字数チェック
	if (mb_strlen($event_name) < 3 || mb_strlen($event_name) > 20) {
		$error['event_name'] = '3文字以上15文字以下にしてください';
	}

	//イベント内容の文字数
	if (mb_strlen($content) > 150) {
		$error['content'] = '150文字以下にしてください';
	}

	if (empty($error)) {

		$sql = 'INSERT INTO event (user_id,fac_id,event_name, content,url) VALUES (:user_id, :fac_id, :event_name, :content, :url)';

		$statement = $db->prepare($sql);
		$statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$statement->bindValue(':fac_id', $fac_id, PDO::PARAM_INT);
		$statement->bindValue(':event_name', $event_name, PDO::PARAM_STR);
		$statement->bindValue(':content', $content, PDO::PARAM_STR);
		$statement->bindValue(':url', $url, PDO::PARAM_STR);

		if($statement->execute()) {
			$imageup_url = 'imageup.php';
			header("Location: {$imageup_url}");
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
  <title>イベント登録</title>
  </head>
<body>
	<div>
<img src="add.png">
		</div>
	<form action="./eventup.php" method="POST">
	<CENTER>
		<div>
			<label for="userName">イベント名</label>
			<input type="text" id="eventName" name="event_name" placeholder="3文字以上15文字以下"/>
			<p><?php if (array_key_exists('event_name', $error)) { print escape($error['event_name']);}  ?></p>
		</div>
		<div>
			<div style="margin-left:-110px">
			<label for="content">内容</label>
				</div>
			<div style="margin-left:100px;margin-top:-20px"><textarea type="comment" id="content" name="content" placeholder="140文字以内" rows="3"></textarea>
			<p><?php if (array_key_exists('content', $error)) { print escape($error['content']);}  ?></p>
		</div>
		<div>
			<select name="fac_id" id="fac_id" size="1">
				<option value="1">知真館１号館</option>
				<option value="2">知真館2号館</option>
				<option value="3">知真館3号館</option>
				<option value="4">ローム記念館</option>
				<option value="5">寒梅館</option>
			</select>
		</div>
		<br>
		<div>
			<label for="url">youtubeのURL</label>
			<input type="text" id="url" name="url">
		</div>
		<br>
		<button type="submit">新規登録</button>
	</form>
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</body>
</html>