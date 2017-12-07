<?php
	header('Content-Type: text/html; charset=utf-8');
	@$uid = $_GET['uid'];
	@$orderid = $_GET['orderid'];
	$return = false;

	try {
		//$dbh = new PDO('mysql:host=shop.othello1888.com;dbname=shopping', 'root', 'OthelloAbc*');
		$dbh = new PDO('mysql:host=localhost;dbname=shopping', 'root', '');
		$dbh->exec("SET NAMES 'utf8';");

		//检查订单是否是已付款订单。
        $row =array();
        $row = $dbh->query("SELECT pay_status FROM iwebshop_order where user_id='" . $uid . "' and id = '".$orderid."' AND if_del=0 AND negotiate_status = 0 order by id desc;");
        $row = $row->fetch();

        //已付款订单，更新订单状态;
		if(!empty($uid)&&$row['pay_status']=='1'){
            $sql = "UPDATE iwebshop_order SET status='8' WHERE id=".$orderid;
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $return =1;
		}

		$dbh = null;
		//var_dump($return);
		echo json_encode($return, JSON_UNESCAPED_UNICODE);
		exit();

	} catch (PDOException $e) {
		$dbh = null;
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		exit();
	}

?>