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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['postText'])) {
		$postText = $_POST['postText'];
		//error_log("post id debug output" . $user_id, 4);
		writePost($db, $user_id, $postText);

		header('Location: index.php');
		exit;
	}
}

if (isset($_GET['delete_post_id'])) {
	$delete_post_id = $_GET['delete_post_id'];
	deletePost($db, $delete_post_id);
	header('Location: '.$_SERVER['SCRIPT_NAME']);
	exit;
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
$lastCont = $lastEvent["content"];
$lastId = $lastEvent["user_id"];
$lastEId = $lastEvent["id"];
if($lastId == $user_id){
	$last = $lastEvent["content"];
	$leName = $lastEvent["event_name"];
}
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$rows = $db->query('SELECT id,event_id,name,type,thumb_data,date FROM image ORDER BY date DESC')->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>index</title>
</head>
<body>
<div>
	<h2>UserName :<?php print $user_name; ?><br>
	新規追加したイベント<br>
	EventName :<?php print $leName; ?><br>
	Detail :<?php print $lastCont; ?><br></h2>

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
   <div>
   		<a href="select.php">トップへ</a>
   </div>
   <br>
   <div>
   		<a href="signout.php">サインアウト</a>
   </div>
</div>
</body>
</html>