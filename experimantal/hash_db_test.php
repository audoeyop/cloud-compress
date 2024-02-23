<?php
	$scale = 131072;
	$hash_db = array();
	for($i=0;$i<$scale;$i++){
		$hash = hash('crc32',$i);
		if(array_key_exists($hash, $hash_db)){
			array_push($hash_db[$hash], $i);
			print_r($hash_db[$hash]);
		}
		else{
			$hash_db[$hash] = array();
			array_push($hash_db[$hash], $i);
		}
		//echo "$i:$hash\n";
	}
	
?>
