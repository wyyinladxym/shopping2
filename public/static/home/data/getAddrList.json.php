<?php 
	
	error_reporting(0);
	
	header('Content-Type: text/html; charset=utf-8');
	
	@$sCACode = $_GET['scacode'];
	@$uid = $_GET['uid'];
	@$id  = $_GET['id'];
	@$setdefault = $_GET['setdefault'];
	@$getdefault = $_GET['getdefault'];
	
	$dbh = new PDO('mysql:host=shop.othello1888.com;dbname=shopping', 'root', 'OthelloAbc*');
	$dbh->query('set names utf8;');
		
	$rows = array();
	
	if(!empty($id)){
		$sql = "SELECT * FROM iwebshop_address where id=".$id ;
	}else if($getdefault=="true"){
		$sql = "SELECT * FROM iwebshop_address where user_id=".$uid." and is_default=1 limit 1;" ;
	}else{
		$sql = "SELECT * FROM iwebshop_address where user_id=".$uid ;
	}
	
	foreach($dbh->query($sql) as $row) {
		$rows[] = $row;
	}
	
	$uid = $row["user_id"];
	
	if($setdefault=="true"){
		$sql = "UPDATE iwebshop_address SET is_default=0 WHERE user_id=".$uid;
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$sql = "UPDATE iwebshop_address SET is_default=1 WHERE id=".$id;
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
	}
	
	echo json_encode($rows, JSON_UNESCAPED_UNICODE);


	
	$dbh = null;
		
		
?>