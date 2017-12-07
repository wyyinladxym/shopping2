<?php 
	
	error_reporting(0);
	
	header('Content-Type: text/html; charset=utf-8');
	
	@$term = $_GET['term'];
	@$term = str_replace("'","",$term);
	@$term = str_replace(" ","",$term);
	
	
	@$lat = $_GET['lat'];
	@$lng = $_GET['lng'];
	//$term = urlencode($term);
	
	if($lat != "" && $lng != ""){
		//按地理位置搜索最近的
		$sql = 'select * from we7.residential_quarter where sqrt(((('.$lng.'-lng)*PI()*12656*cos((('.$lat.'+lat)/2)*PI()/180)/180) * (('.$lng.'-lng)*PI()*12656*cos ((('.$lat.'+lat)/2)*PI()/180)/180)) +  
    ((('.$lat.'-lat)*PI()*12656/180) *  (('.$lat.'-lat)*PI()*12656/180) ))<2 order by sqrt(((('.$lng.'-lng)*PI()*12656*cos((('.$lat.'+lat)/2)*PI()/180)/180) * (('.$lng.'-lng)*PI()*12656*cos ((('.$lat.'+lat)/2)*PI()/180)/180)) +  
    ((('.$lat.'-lat)*PI()*12656/180) *  (('.$lat.'-lat)*PI()*12656/180) )) asc limit 20;';
		
 		//echo $sql;
		try {
			$dbh = new PDO('mysql:host=localhost;dbname=we7', 'haile', '2223407');
			$array_string = "[";
			foreach($dbh->query($sql) as $row) {
				$array_string .= "\"".$row['name']."\",";
			}
			$array_string = rtrim($array_string, ",");
			$array_string .= "]";
			echo $array_string;
			$dbh = null;
			
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		
		exit();
	}
	
	
	if($term != ""){
		//按小区名称搜索
		$all_en = false;
		if(preg_match("/^[a-zA-Z\s]+$/",$term)){
			$all_en = true;
			$sql = "SELECT name FROM we7.residential_quarter where mpId is not null and mpId !='' and pinyin_handle like '%".$term."%' limit 20;";
		}else{
			$all_en = false;
			$sql = "SELECT name FROM we7.residential_quarter where mpId is not null and mpId !='' and name like '%".$term."%' limit 20;";
		}
		
		try {
			$dbh = new PDO('mysql:host=localhost;dbname=we7', 'haile', '2223407');
			$array_string = "[";
			foreach($dbh->query($sql) as $row) {
				$array_string .= "\"".$row['name']."\",";
			}
			$array_string = rtrim($array_string, ",");
			$array_string .= "]";
			echo $array_string;
			$dbh = null;
			
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		exit();		
	}
	
	
?>