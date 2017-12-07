<?php
	header('Content-Type: text/html; charset=utf-8');
	
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=we7', 'haile', '2223407');
		foreach($dbh->query('SELECT * FROM  ims_shopping_category_youmi where status=1 and parentid<>0') as $row) {
			$rows = array();
			$rows_restock = array();
			$fp = fopen("getCategoryProduct_".$row["id"]."_.json", "w");
			foreach($dbh->query('SELECT id,pcate,ccate,title,thumb2,thumb,package,unit,content,item_no,item_no_pos,item_stock_pos,item_pre,marketprice,productprice,freepostage,limitationsCanbuy FROM  ims_shopping_goods_youmi where ccate='. $row["id"] .' and   status=1 and item_stock_pos is not null AND  ((item_stock_pos / item_pre >-10000 ) or (safetotal>0 and (item_stock_pos / item_pre >safetotal ) )) order by displayorder desc,scate asc; ') as $goods) {
				$goods['mpid'] = "0401";
				if($goods['item_stock_pos']>0){
					$rows[]=$goods;
				}else{
					$rows_restock[]=$goods;
				}
				
			}
			$rows = array_merge($rows, $rows_restock);
			fwrite($fp, json_encode($rows,true));
			fclose($fp);
		}
		
		
		foreach($dbh->query('SELECT * FROM  ims_shopping_category_youmi  where status=1 and parentid=0') as $row) {
			$rows = array();
			$rows_restock = array();
			$fp = fopen("getCategoryProduct_".$row["id"]."_.json", "w");
			foreach($dbh->query('SELECT id,pcate,ccate,title,thumb2,thumb,package,unit,content,item_no,item_no_pos,item_stock_pos,item_pre,marketprice,productprice,freepostage,limitationsCanbuy FROM  ims_shopping_goods_youmi where pcate='. $row["id"] .' and   status=1 and item_stock_pos is not null AND  ((item_stock_pos / item_pre >-10000 ) or (safetotal>0 and (item_stock_pos / item_pre >safetotal ) )) order by displayorder desc,scate asc; ') as $goods) {
				$goods['mpid'] = "0401";
				if($goods['item_stock_pos']>0){
					$rows[]=$goods;
				}else{
					$rows_restock[]=$goods;
				}
				
			}
			$rows = array_merge($rows, $rows_restock);
			fwrite($fp, json_encode($rows,true));
			fclose($fp);
		}
		     
		//推荐商品
		$rows = array();
		$rows_restock = array();
		foreach($dbh->query('SELECT id,pcate,ccate,title,thumb2,thumb,package,unit,content,item_no,item_no_pos,item_stock_pos,item_pre,marketprice,productprice,freepostage,limitationsCanbuy FROM  ims_shopping_goods_youmi where   status=1   and isRecommend>0 and item_stock_pos is not null AND  ((item_stock_pos / item_pre >-10000 ) or (safetotal>0 and (item_stock_pos / item_pre >safetotal ) )) order by displayorder desc; ') as $goods) {
				$goods['mpid'] = "0401";
				if($goods['item_stock_pos']>0){
					$rows[]=$goods;
				}else{
					$rows_restock[]=$goods;
				}
		}

		$rows = array_merge($rows, $rows_restock);
		$fp = fopen("getCategoryProduct_999_.json", "w");
		fwrite($fp, json_encode($rows,true));
		fclose($fp);
		/*
		$rows = array();
		foreach($dbh->query('SELECT id,pcate,ccate,title,thumb2,thumb,package,unit,content,item_no,item_no_pos,item_stock_pos,item_pre,marketprice,productprice FROM  ims_shopping_goods_youmi order by id desc limit 50,120;') as $row) {
			$rows[] = $row;
		}
		
		print json_encode($rows);
		*/
		$dbh = null;
	
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
	
	echo "done";
	exit();
	
?>