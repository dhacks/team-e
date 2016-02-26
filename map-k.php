<?php
require_once 'init.php';

$db = connectDb();

if(!isSignin()) {
	$signin_url = 'signin.php';
	header("Location: {$signin_url}");
	exit;
}


function getevent($pdo){	
	$sql = "SELECT * FROM event";
	$statement = $pdo->prepare($sql);
	$statement->execute();

	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows;
	}else{
		return false;
	}
}

function getEventId($pdo, $fac_id, $event_name){
	$sql = "SELECT id FROM event WHERE 'fac_id' = :fac_id, 'event_name' = :event_name";
	$statement->bindValue(':fac_id', $fac_id, PDO::PARAM_INT);
	$statement->bindValue(':event_name', $event_name, PDO::PARAM_STR);
	$statement = $pdo->prepare($sql);
	$statement->execute();

	if($rows = $statement->fetch(PDO::FETCH_ASSOC)) {
		return $rows['id'];
	}else{
		return false;
	}
}

function getEventName($pdo, $id){
	$sql = "SELECT event_name FROM event WHERE `id` = :id";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':id', $id, PDO::PARAM_INT);
	$statement->execute();
	if($rows = $statement->fetch(PDO::FETCH_ASSOC)) {
		return $rows['event_name'];
	}else{
		return false;
	}
}


function getFacility($pdo){
	$sql = "SELECT * FROM facility";
	$statement = $pdo->prepare($sql);
	$statement->execute();
	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows;
	}else{
		return false;
	}

}

function getFacName($pdo, $id){
	$sql = "SELECT fac_name FROM facility WHERE `id` = :id";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':id', $id, PDO::PARAM_INT);
	$statement->execute();
	if($rows = $statement->fetch(PDO::FETCH_ASSOC)) {
		return $rows['fac_name'];
	}else{
		return false;
	}
}


function OnIcon($db,$fac_id){
	switch ($fac_id){
			case 1://知真1
				echo '<form method="POST" action="map-k.php">
						<DIV style="position:absolute; top : 330px ; left : 440px ; ">
	   					<button type="button" onclick="clickIcon(1)" data-toggle="modal" data-target="#myModal" name="TC1"><img src="doushisha_icon.png" width="30px" height="30px" border="0"></button>
	   					</DIV>
						</form>';
				break;
			case 2://知真2
				echo '<form method="POST" action="map-k.php">
						<DIV style="position:absolute; top : 350px ; left : 290px ; ">
	   					<button type="button" onclick="clickIcon(2)" data-toggle="modal" data-target="#myModal" name="TC2"><img src="doushisha_icon.png" width="30px" height="30px" border="0"></button>
	   					</DIV>
						</form>';
				break;
			case 3://知真3
				echo '<form method="POST" action="map-k.php">
						<DIV style="position:absolute; top : 225px ; left : 415px ; ">
	   					<button type="button" onclick="clickIcon(3)" data-toggle="modal" data-target="#myModal" name="TC3"><img src="doushisha_icon.png" width="30px" height="30px" border="0"></button>
	   					</DIV>
						</form>';
				break;
			case 4://ローム記念館
				echo '<form method="POST" action="map-k.php">
						<DIV style="position:absolute; top : 480px ; left : 950px ; ">
	   					<button type="button" onclick="clickIcon(4)" data-toggle="modal" data-target="#myModal" name="TC3"><img src="doushisha_icon.png" width="30px" height="30px" border="0"></button>
	   					</DIV>
						</form>';
				break;
			case 5:
				
				break;
			case 6:
				
				break;
			default:
				break;
		}		
}

$event = getevent($db);
$facility = getFacility($db);

function getEventFromFacility($pdo, $fac_id){
	$sql = "SELECT * FROM event WHERE `fac_id` = :fac_id";
	$statement = $pdo->prepare($sql);
	$statement->bindValue(':fac_id', $fac_id, PDO::PARAM_INT);
	$statement->execute();
	if($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
		return $rows;
	}else{
		return false;
	}
}

function getEventNum($pdo){
	$sql = "SELECT id FROM event ORDER BY `id` DESC LIMIT 1";
	$statement = $pdo->prepare($sql);
	$statement->execute();
	if($rows = $statement->fetch(PDO::FETCH_ASSOC)) {
		return $rows['id'];
	}else{
		return false;
	}
}




$all_event_array = array();
foreach ($facility as $key => $value) {
	//var_dump($value);
	$fac_event = getEventFromFacility($db, $value["id"]);
	//var_dump($fac_event);
	$all_event_array[$value["id"]] = array(
		"event_info" => $fac_event,
		"fac_name" => $value["fac_name"]
	);
}

$eventNum = getEventNum($db);
/*
$box = 0;
while(1){
	$bid = $box + 1;
	//print $box;
	var_dump($box);
	print "--------------------------------------";
	var_dump($facility);
	//var_dump($facility[2]["fac_name"]);
	$fac_event = getEventFromFacility($db, $facility[$box][$bid]["id"]);
	$fac_name = $facility[$box][$bid]["fac_name"];
	$fac_info = array(
		'event_id' => $fac_event['id'],
		'event_name' => $fac_event["event_name"],
		'fac_name' => $fac_name
	);
	$all_event_array[$facility[$box][$bid]["id"]] = $fac_info;
	$box++;
	if ($box == $eventNum) {
		break;
	}
}
*/
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function clickIcon(fac_id){
			//var place = document.getElementById("eventPlace");
			//var name = document.getElementById("eventName");
			<?php 
				$all_event_json =  json_encode($all_event_array);
				$all_event_json = str_replace("\\r\\n", "", $all_event_json);
			?>
			var all_event = JSON.parse('<?php echo $all_event_json ?>');

			var place = $("#eventPlace");
			var name = $("#eventName");
			
			place.html(all_event[fac_id]['fac_name']);
			name.html("");
			for (var event = 0; event < all_event[fac_id]['event_info'].length; event++) {
				console.log(all_event[fac_id]['event_info'][event]['id']);
				var eventid = all_event[fac_id]['event_info'][event]['id'];
				name.append('<a href="./event.php?place=' 
				+ eventid.toString()
					+ '">'
					+ all_event[fac_id]['event_info'][event]['event_name'] + '</a><br>');
			};
			/*
			place.append(all_event[fac_id]['fac_name']);
			name.append('<form name="place" method="POST" action="./event.php"><input type="hidden" name="place" value="' 
			+ all_event[fac_id]['event_id'] + '"></form><a href="javascript:document.place.submit()">'
			+ all_event[fac_id]['event_name'] + '</a>');
			*/
		}

	</script>
	<style>
		button {
		    width: auto;
		    padding:0;
		    margin:0;
		    background:none;
		    border:0;
		    font-size:0;
		    line-height:0;
		    overflow:visible;
		    cursor:pointer;
		}
	</style>
</head>
<body>

<DIV style="position: relative; left : 0px">
<!-- 地図 //-->
<IMG src="kyotanabe.png" width="90%" height="auto" border="0"> 
	<!--同志社アイコン-->
	<?php
	foreach ($event as $key => $value) {
		if (isset($value)) {
			OnIcon($db,$value['fac_id']);
			
		}
	}
	?>
	
</DIV>
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="eventPlace">eventPlace</h4>
        </div>
        <div class="modal-body">
           	<p id="eventName">eventName</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</body>
</html>