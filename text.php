<?php 
	// $db1 = new mysqli('127.0.0.1', 'root', '', 'b24_22765359_moviesmedia');
	// $db2 = new mysqli('127.0.0.1', 'root', '', 'moviesmedia');

	// $qr_1 = "SELECT DISTINCT * FROM collection WHERE user_id=0 GROUP BY mvid";

	// if ($db1->connect_error) {
	// 	echo "string";
	// }else {
	// 	if($result = $db1->query($qr_1)){
	// 		while($rs = $result->fetch_array(MYSQLI_ASSOC)) {
	// 			print_r($rs);
	// 			echo "<br>";
	// 			echo "<br>";
	// 			echo "<br>";
	// 			$mvid = (int)$rs['mvid'];
	// 			$user_id = (int)$rs['user_id'];
	// 			$seen = filter_var($rs['seen'], FILTER_VALIDATE_BOOLEAN);
	// 			$wlist = filter_var($rs['wlist'], FILTER_VALIDATE_BOOLEAN);

	// 			$qr_2 = "INSERT INTO collection (user_id, movie_id, watched, watch_list) VALUES ($user_id, $mvid, '$seen', '$wlist')";
	// 			if($db2->query($qr_2)) {
	// 				echo true;
	// 			}else {
	// 				echo $qr_2;
	// 				echo "<br>";
	// 				echo "<br>";
	// 				echo "<br>";
	// 				print_r($db2->error);
	// 				break;
	// 			}
	// 		}
	// 	}
	// }
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	
	<script>
		var obj = {};
		// obj.movie_id = 10138;
		// obj.title = "Iron Man 2";
		// obj.poster = "/ArqpkNYGfcTIA6umWt6xihfIZZv.jpg";
		// obj.release_date = "2010-04-28";
		// obj.rating = 7;
		// obj.gender = 12;
		// obj.runtime = 124;
		// obj.cast = "eyJuYW1lIjoiUm9iZXJ0IERvd25leSBKci4iLCJpbWciOiJcLzFZamRTeW0xalRHN3hqSFNJMHlHR1dFc3c1aS5qcGcifQ==";
		// obj.director = "eyJuYW1lIjoiSm9uIEZhdnJlYXUiLCJpbWciOiJcL3NKU0dKd0dhM2hqTWxKTlVDeEY3d1F3bzdmYi5qcGcifQ==";
		// obj.watched = true;
		// obj.watch_list = false;
		obj.username = "blackiso";
		obj.password = "ismail123";

		document.write(btoa(JSON.stringify(obj)));
	</script>
</body>
</html>