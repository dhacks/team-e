<?php
require_once 'init.php';

if(!isSignin()) {
	$signin_url = 'signin.php';
	header("Location: {$signin_url}");
	exit;
}

$user_id = $_SESSION['user_id'];
//error_log("user id debug output" . $user_id, 4);

$db = connectDb();

$place = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['place'])) {
		$place = $_POST['place'];
	}
}

$user_data = getUserData($db, $user_id);
$user_name = $user_data["user_name"];
$user_id = $user_data["id"];

$event_data = getEventData($db, $user_id);
$event_name = $event_data["event_name"];
$event_content = $event_data["content"];

$lastEvent = getLastEvent($db, $user_id);
$leName = "イベントがありません";
$last = "イベントがありません";
$lastId = $lastEvent["user_id"];
$lastEId = $lastEvent["id"];
if($lastId == $user_id){
	$last = $lastEvent["content"];
	$leName = $lastEvent["event_name"];
}
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function getEventName($pdo, $place){	
	$sql = "SELECT event_name FROM event WHERE id = :place";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':place', $place, PDO::PARAM_INT);
	$statement->execute();

	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows[0]['event_name'];
	}else{
		return false;
	}

}

function getEventContent($pdo, $place){
	$sql = "SELECT content FROM event WHERE id = :place";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':place', $place, PDO::PARAM_INT);
	$statement->execute();

	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows[0]['content'];
	}else{
		return false;
	}
}
$event_name = getEventName($db, $place);
//print $event_name;
$event_content = getEventContent($db, $place);
//print $event_content;
$rows = $db->query("SELECT id,event_id,name,type,thumb_data,date FROM image WHERE event_id = $place")->fetchAll();
$rows = $rows[0];
var_dump($rows['name']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>index</title>
</head>
<body>
<div>
	EventName :<?php print $event_name; ?><br>
	Detail :<?php print $event_content; ?><br>

   <fieldset>
     <legend>イベント画像</legend>
		<?php foreach ($rows as $i => $row): ?>
		<?php if ($row['event_id'] == $lastEId && $lastId == $user_id): ?>
		<?php if ($i): ?>
		     <hr />
		<?php endif; ?>
     <p>
       <?=sprintf(
           '<a href="?id=%d"><img src="data:%s;base64,%s" alt="%s" /></a>',
           $row['id'],
           image_type_to_mime_type($row['type']),
           base64_encode($row['thumb_data']),
           h($row['name'])
       )?><br />
    </p>
		<?php endif; ?>
		<?php endforeach; ?>
   </fieldset>
   <br>
   <div>
   		<a href="eventup.php">イベント追加</a>
   </div>
   <br>
   <br>
   <div>
   		<a href="signout.php">サインアウト</a>
   </div>
</div>
</body>
</html>