<?php 
	error_reporting(0);
	header('Content-Type: text/html; charset=utf-8');
	$cacode=$_GET["cacode"];
	$rows = array();
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=shopping', 'root', 'OthelloAbc*');
		$dbh->query('set names utf8;'); 
		foreach($dbh->query("select * from iwebshop_coupon where code like 'daijin_%' and sCACode = '".$cacode."' and status=0 order by endTime asc") as $row) {
			$row["endTime"]=explode(" ",$row["endTime"]);
			$quan=array();
			$quan["code"]= $row["code"];
			$quan["title"]= $row["title"];
			$quan["desc"]=$row["desc"];
 			$ary=explode("_",$row["code"]);
			$quan["value"]=$ary[2];
			$quan["FTF"]=$ary[4];
			$quan["start"]=$row["startTime"];
			$quan["expire"]=$row["endTime"][0];
			$quan["type"]=$ary[1];
			
			
			//如果是单品券，写入goods信息
			if($quan["type"]=="goods"){
				$quan["goods"]=$ary[5];
			}
			
			//如果是品类券，写入cates信息
			if($quan["type"]=="cate"){
				$quan["cates"]=$ary[5];
			}
			
			$rows[] = $quan;
		}
		echo json_encode($rows, JSON_UNESCAPED_UNICODE);		
		$dbh = null;
	
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
 ?>
 