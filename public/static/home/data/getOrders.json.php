<?php
	session_start();
	require_once "../do/pay.inc.php";
	header('Content-Type: text/html; charset=utf-8');
	@$uid = $_GET['uid'];
	@$orderid = $_GET['orderid'];
	$return = array();

	try {
		//$dbh = new PDO('mysql:host=shop.othello1888.com;dbname=shopping', 'root', 'OthelloAbc*');
		$dbh = new PDO('mysql:host=localhost;dbname=shopping', 'root', 'root');
		$dbh->exec("SET NAMES 'utf8';");

		//检查快递信息 有则插入
		foreach($dbh->query("SELECT order_no, logistics FROM iwebshop_order where user_id='" . $uid . "' AND if_del=0 AND negotiate_status = 0 order by id desc;") as $row) {
			if($row['logistics']==''){
				$logistics = GET_LOGISTICS($row['order_no']);
				$logistics = $logistics==false?'':implode('|',$logistics);
				$dbh->query("UPDATE `iwebshop_order` SET  `logistics` =  '".$logistics."' WHERE `order_no` =".$row['order_no']);
			}
		}
		$return = array();
		
		if($uid!=""){
			foreach($dbh->query("SELECT id, order_no, create_time, status, pay_type, pay_status, logistics, accept_name, telphone FROM iwebshop_order where user_id='" . $uid . "' AND if_del=0 AND negotiate_status = 0 order by id desc;") as $row) {		
				$order = array();
				$row['logistics'] = $row['logistics']==''?'暂无快递信息':$row['logistics'];//处理快递信息
				$order['info']= $row;
				foreach($dbh->query("SELECT id, img, goods_price, real_price, goods_nums FROM shopping.iwebshop_order_goods where order_id=".$row['id'].";") as $goods_row) {		
					$order['goods'][]= $goods_row;
					//var_dump($goods_row);
				}
				
				$pay_type_recordset = $dbh->query("SELECT id, name, description FROM shopping.iwebshop_payment where id=".$row['pay_type']." limit 1;");
				$pay_type = $pay_type_recordset->fetch()['name'];
				
				//var_dump($pay_type_recordset->fetch()['description']);
				if($pay_type=="微信支付"){
					if($order['info']['pay_status']==0){
						$pay_type = $pay_type . " [未支付]";
					}else{
						$pay_type = $pay_type . " [已支付]";
					}
				}elseif($pay_type=="电汇付款"){
					if($order['info']['pay_status']==0){
						$pay_type = $pay_type . " [未确认]";
					}else{
						$pay_type = $pay_type . " [已确认]";
					}
				}elseif($pay_type=="协议付款"){
					if($order['info']['pay_status']==0){
						$pay_type = $pay_type . " [未确认]";
					}else{
						$pay_type = $pay_type . " [已确认]";
					}
				}
				$order['info']['pay_type']= $pay_type;
				array_push($return, $order);
			}
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