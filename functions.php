<?php
// functions.php
function connectDb() {
	try {
		return new PDO(DSN, DB_USER, DB_PASSWORD);
	}catch (PDOException $e) {
		print $e->getMessage();
		exit;
	}
}

function getUserId($email, $password, $db) {
	$sql = "SELECT id, password FROM users WHERE email = :email";
	$statement = $db->prepare($sql);
	$statement->bindValue(':email', $email, PDO::PARAM_STR);
	$statement->execute();
	$row = $statement->fetch();
	if (password_verify($password, $row['password'])) {
		return $row['id'];
	}else{
		return false;
	}
}


function getLastEvent($pdo, $id){
	$sql = 'SELECT * FROM event WHERE user_id = :id ORDER BY `id` DESC LIMIT 1';
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':id', $id, PDO::PARAM_INT);
	$statement->execute();

	if($row = $statement->fetch()){
		return $row;
	}else{
		//throw new Exception('ユーザデータを取得できません');
	}
}

function escape($s){
	return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function isSignin(){
	if (!isset($_SESSION['user_id'])) {
		return false;
	}else{
		return true;
	}
}



function getUserData($pdo, $id){
	$sql = 'SELECT * FROM users WHERE id = :id';
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':id', $id, PDO::PARAM_INT);
	$statement->execute();

	if($row = $statement->fetch()){
		return $row;
	}else{
		throw new Exeption('ユーザデータを取得できません');
	}
}

function getEventData($pdo, $id){
	$sql = 'SELECT * FROM event WHERE user_id = :id';
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':id', $id, PDO::PARAM_INT);
	$statement->execute();

	if($row = $statement->fetch()){
		return $row;
	}else{
		//throw new Exception('ユーザデータを取得できません');
	}
}

function addEvent(PDO $pdo, $id, $text) {
	$replyUserId = getReplyId($pdo, $text);
	$sql = 'INSERT INTO posts (user_id,in_reply_to_user_id,text) VALUES (:user_id, :reply_user_id, :text)';
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':user_id', $id, PDO::PARAM_INT);
	$statement->bindValue(':text', $text, PDO::PARAM_STR);
	$statement->execute();
}

function emailExists($email, PDO $pdo){
	$sql = 'SELECT * FROM users WHERE email = :email';
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':email', $email, PDO::PARAM_STR);
	$statement->execute();
	$row = $statement->fetch();
	return $row ? true : false;
}

function setToken(){
	$token = sha1(uniqid(mt_rand(), true));
	$_SESSION['token'] = $token;
}

function checkToken(){
	if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
		print "不正なPOSTが行われました";
		header('HTTP', true, 400);
	}
}