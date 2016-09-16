<?php
	class SDbBase {
		static function connect(){
			$link=mysqli_connect(localhost, root, "", testBD)
				or die("Error: ".mysqli_error($link));
			if(!mysqli_set_charset($link,"utf8")){
				printf("Error: ".mysqli_error($link));
			} 
			return $link;
		}
	}
	class SDb extends SDbBase {
		static function insert($link, $ip, $port, $speed, $type, $anonymity, $dateUpdate, $dateCreate) {
			$sql_check = "SELECT * FROM proxy_list WHERE ip = '" . $ip . "' AND port = " . $port;
			$check = mysqli_query($link, $sql_check);
			if (mysqli_num_rows($check) == 0) {
				$sql = "INSERT INTO proxy_list (`ip`, `port`, `speed`, `type`, `anonymity`, `dateUpdate`, `dateCreate`) VALUES ('%s', '%d', '%s', '%s', '%s', '%d', '%d')"; 				$query=sprintf($sql,mysqli_real_escape_string($link,$ip),mysqli_real_escape_string($link,$port),mysqli_real_escape_string($link,$speed),mysqli_real_escape_string($link,$type),mysqli_real_escape_string($link,$anonymity),mysqli_real_escape_string($link,$dateUpdate),mysqli_real_escape_string($link,$dateCreate)); 
				$result=mysqli_query($link,$query);
				if(!$result) 
					die(mysqli_error($link)); 
			} else {
				SDb::update($link, $ip, $port, $dateUpdate);
			}
		}
		static function update($link, $ip, $port, $dateUpdate) {
			$sql = "UPDATE proxy_list SET dateUpdate = '" . $dateUpdate . "' WHERE ip = '" . $ip . "' AND port = " . $port;
				$result = mysqli_query($link, $sql);
				if(!$result) 
					die(mysqli_error($link)); 
		}
		static function read($link) {
      $sql = "SELECT * FROM (SELECT `port`,COUNT(*) as `kol`, GROUP_CONCAT(`ip`SEPARATOR ', ') as `ip` FROM proxy_list GROUP BY `port` ORDER BY COUNT(*) DESC) as y WHERE `kol`<21";
			$result=mysqli_query($link,$sql);
			if(!$result) 
				die(mysqli_error($link));	
			$n=mysqli_num_rows($result);  
			for($i=0;$i<$n;$i++){ 
				$row=mysqli_fetch_assoc($result); 
				$list[]=$row; 
			} 
			return $list;
		}
	}

    
?>