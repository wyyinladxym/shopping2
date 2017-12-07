<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	@$id = $_GET['id'];
	$return = array();
	
	try {
		
		$dbh = new PDO('mysql:host=shop.othello1888.com;dbname=shopping', 'root', 'OthelloAbc*');
		$dbh->exec("SET NAMES 'utf8';");
		
		if($id!=""){
			foreach($dbh->query("SELECT * FROM iwebshop_areas where parent_id=".$id." order by sort") as $row) {
				$arr = array();
				$arr['area_id'] = $row['area_id'];
				$arr['area_name'] = $row['area_name'];
				array_push($return, $arr);
			}
		}

		$dbh = null;
		echo json_encode($return, JSON_UNESCAPED_UNICODE);
		exit();
	
	} catch (PDOException $e) {
		$dbh = null;
		echo json_encode($return, JSON_UNESCAPED_UNICODE);
		exit();
	}

?>

