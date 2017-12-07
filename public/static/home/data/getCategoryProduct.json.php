<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once '../libs/phpanalysis2/phpanalysis.class.php';

	$dbh = new PDO('mysql:host=shop.othello1888.com;dbname=shopping', 'root', 'OthelloAbc*');
	$dbh->exec("SET NAMES 'utf8';");
	$searchkey=$_GET["cate"];
	$depot=$_GET["depot"];
	$rows = array();
	
	$pa=new PhpAnalysis();
	$pa->SetSource($searchkey);
	$pa->resultType=2;
	$pa->differMax=true;
	$pa->StartAnalysis();
	$arr=$pa->GetFinallyIndex();
	$sql = "";
	//var_dump($arr);
	
	foreach($arr as $k=>$v)
	{
		$sql .= "SELECT * FROM iwebshop_goods where is_del=0 and name like '%".$k."%'";
	}
	
	$rows = array();
	
	foreach($dbh->query($sql) as $goods_detail)
	{
		$row['pcate'] = "";
		$row['ccate'] = "";
		$row['id'] = $goods_detail["id"];
		$row['title'] = $goods_detail["name"];
		$row['thumb'] = $goods_detail["img"];
		$row['package'] = isset($goods_detail["package"]) ? $goods_detail["package"] : '';
		$row['unit'] = $goods_detail["unit"];
		$row['content'] = $goods_detail["content"];
		$row['item_no'] = $goods_detail["goods_no"];
		$row['item_no_pos'] = $goods_detail["goods_no"];
		$row['item_stock_pos'] = $goods_detail["store_nums"];
		$row['item_pre'] = 1;
		$row['marketprice'] = $goods_detail["sell_price"];
		$row['marketprice'] = $goods_detail["sell_price"];//批发价
		$row['sellprice'] = $goods_detail["market_price"];//零售价
		$row['freepostage'] = isset($goods_detail["freepostage"]) ? $goods_detail["freepostage"] : ''; 
		$row['limitationsCanbuy'] = isset($goods_detail["limitationsCanbuy"]) ? $goods_detail["limitationsCanbuy"] : '';
		$row['mpid'] = '0601';
		$row['thumb2'] = '';
					
		$rows[] = $row;
	}
	if(!empty($rows)){
		print json_encode($rows);
	}	
		
	$dbh = null;
	
	 
	
?>