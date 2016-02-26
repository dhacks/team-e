<?php
require_once 'init.php';

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
if(!isSignin()) {
	$signin_url = 'signin.php';
	header("Location: {$signin_url}");
	exit;
}

$user_id = $_SESSION['user_id'];
$db = connectDb();
$place = 0;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (isset($_GET['place'])) {
		$place = $_GET['place'];
	}
}else{
		$map_url = 'map.php';
		header("Location: {$map_url}");
		exit;
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

function getEventPlace($pdo, $place){
	$sql = "SELECT fac_id FROM event WHERE id = :place";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':place', $place, PDO::PARAM_INT);
	$statement->execute();

	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows[0]['fac_id'];
	}else{
		return false;
	}
}

//$rows = $db->query('SELECT id,event_id,name,type,thumb_data,date FROM image WHERE id = :$place')->fetchAll();
function getImage($pdo, $id){
	$sql = "SELECT id,event_id,name,type,thumb_data,date FROM image WHERE id = :id";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':id', $id, PDO::PARAM_INT);
	$statement->execute();
	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
	
}	
$rows = $db->query('SELECT id,event_id,name,type,thumb_data,date FROM image ORDER BY date DESC')->fetchAll();
//var_dump($rows);
function getPlace($pdo, $fac_id){
	$sql = "SELECT fac_name FROM facility WHERE id = :fac_id";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':fac_id', $fac_id, PDO::PARAM_INT);
	$statement->execute();

	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows[0]['fac_name'];
	}else{
		return false;
	}
}

function getEventUrl($pdo, $event_id){
	//$sql = "SELECT url FROM event WHERE id = 36";
	$sql = "SELECT url FROM event WHERE id = :event_id";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':event_id', $event_id, PDO::PARAM_INT);
	$statement->execute();


	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows[0]['url'];
	}else{
		return false;
	}
}

$event_place = getEventPlace($db, $place);
$fac_name = getPlace($db,$event_place);

$event_name = getEventName($db, $place);
//print $event_name;
$event_content = getEventContent($db, $place);
//print $event_content;
$event_url = getEventUrl($db, $place);
$image = getImage($db, $place);
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<h2>イベント名 :<?php print $event_name; ?></h2><br>
<h2>内容 :<?php print $event_content; ?></h2><br>
<h2>場所 :<?php print $fac_name; ?></h2><br>
<fieldset>
     <legend>イベント画像</legend>
		<?php foreach ($rows as $i => $row): ?>
			<?php if ($row['event_id']==$place): ?>
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
   </fieldset><br>
   <h2>url :<a href="<?php print $event_url ?>">link</a></h2>
</body>
</html>