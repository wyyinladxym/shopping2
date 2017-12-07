<?php
//更新首页 商品数据
session_start();
	header('Content-Type: text/html; charset=utf-8');
	set_time_limit(60000);
	$depot_mp_maping = array("0"=>"0401");
	
	
	try {
		
		$dbh = new PDO('mysql:host=shop.othello1888.com;dbname=shopping', 'root', 'OthelloAbc*');
		//$dbh = new PDO('mysql:host=localhost;dbname=shopping', 'root', 'root');
		$dbh->exec("SET NAMES 'utf8';");
		foreach($dbh->query('SELECT * FROM  iwebshop_category where parent_id<>0') as $row) {
			$list = array();
			$rows = array();
// 			$rows_depot = array();
			
// 			$rows_restock = array();
// 			$rows_restock_depot = array();
			
// 			foreach($depot_mp_maping as $depot){
// 				$rows_depot[$depot] = array();
// 				$rows_restock_depot[$depot] = array();
// 			}
			
			//echo $row["id"];
			//echo "<br />";
			
			//找到sku
			foreach($dbh->query('SELECT * FROM  iwebshop_category_extend where category_id='. $row["id"]) as $goods) {
				
// 				//对应不同depot的stock
// 				foreach($depot_mp_maping as $depot){
									
// 					$stock_rs = $dbh->query('SELECT * FROM iwebshop_goods where id="'. $goods["goods_id"]);
				
// 					if(!empty($stock_rs)){
// 						$stock = $stock_rs->fetch();
// 						$goods['item_stock_pos'] = $stock['stock'];
// 						$goods['mpid'] = $depot;
// 						if($goods['item_stock_pos']>0){
// 							$rows_depot[$depot][] = $goods;
// 							if($goods["item_no"]=="693336514342p"){
// 								//print_r( $goods)
// 							}		
// 						}else{
// 							$rows_restock_depot[$depot][] = $goods;
// 						}
// 					}
// 				}

				$goods_rs = $dbh->query('SELECT * FROM iwebshop_goods where is_del=0 and id='. $goods["goods_id"]);
				$goods_detail = $goods_rs->fetch();
				
				if(!empty($goods_detail)){
					$rows['pcate'] = $row['parent_id'];
					$rows['ccate'] = $row['id'];
					$rows['id'] = $goods_detail["id"];
					$rows['title'] = $goods_detail["name"];
					$rows['thumb'] = $goods_detail["img"];
					$rows['package'] = isset($goods_detail["package"]) ? $goods_detail["package"] : '';
					$rows['unit'] = $goods_detail["unit"];
					$rows['content'] = $goods_detail["content"];
					$rows['item_no'] = $goods_detail["goods_no"];
					$rows['item_no_pos'] = $goods_detail["goods_no"];
					$rows['item_stock_pos'] = $goods_detail["store_nums"];
					$rows['item_pre'] = 1;
					$rows['marketprice'] = $goods_detail["sell_price"];//批发价
					$rows['sellprice'] = $goods_detail["market_price"];//零售价
					$rows['productprice'] = $goods_detail["cost_price"];
					$rows['freepostage'] = isset($goods_detail["freepostage"]) ? $goods_detail["freepostage"] : ''; 
					$rows['limitationsCanbuy'] = isset($goods_detail["limitationsCanbuy"]) ? $goods_detail["limitationsCanbuy"] : '';
					$rows['mpid'] = '0601';
					$rows['thumb2'] = '';
					$thumb2 = array();
					
					foreach($dbh->query('SELECT * FROM  iwebshop_goods_photo_relation where goods_id='. $goods_detail["id"]) as $photos) {
						$photo_rs = $dbh->query('SELECT * FROM iwebshop_goods_photo where id="'. $photos["photo_id"]. '"');
						$photo = $photo_rs->fetch();
						if (!empty($photo)) {
							array_push($thumb2, $photo['img']);
						}
					}
					
					$rows['thumb2'] = implode(',', $thumb2);
					array_push($list, $rows);
				}
			}

			$fp = fopen("getCategoryProduct_".$row["id"]."_0401_.json", "w");
			fwrite($fp, json_encode($list, true));
			fclose($fp);
		 
// 			foreach($depot_mp_maping as $depot){
// 				$rows = array_merge($rows_depot[$depot], $rows_restock_depot[$depot]);
// 				$fp = fopen("getCategoryProduct_".$row["id"]."_".$depot."_.json", "w");
// 				fwrite($fp, json_encode($rows,true));
// 				fclose($fp);
// 			}
			
			//print_r($rows);
			

		}
		
		/*
		foreach($dbh->query('SELECT * FROM  ims_shopping_category_youmi  where status=1 and parentid=0') as $row) {
			$rows = array();
			$rows_restock = array();
			//$fp = fopen("getCategoryProduct_".$row["id"]."_.json", "w");
			foreach($dbh->query('SELECT id,pcate,ccate,title,thumb2,thumb,package,unit,content,item_no,item_no_pos,item_stock_pos,item_pre,marketprice,productprice,freepostage,limitationsCanbuy FROM  ims_shopping_goods_youmi where pcate='. $row["id"] .' and   status=1 and item_stock_pos is not null AND  ((item_stock_pos / item_pre >-10000 ) or (safetotal>0 and (item_stock_pos / item_pre >safetotal ) )) order by displayorder desc,scate asc; ') as $goods) {
				$goods['mpid'] = "0401";
				if($goods['item_stock_pos']>0){
					$rows[]=$goods;
				}else{
					$rows_restock[]=$goods;
				}
				
			}
			$rows = array_merge($rows, $rows_restock);
			//fwrite($fp, json_encode($rows,true));
			//fclose($fp);
		}
		*/
		
		
		$dbh = null;
	
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	
	
	
	

	
	echo "done";
	exit();
	
?>