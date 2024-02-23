<?php

/*
CREATE TABLE hash_table(
   hash_table_val TEXT NOT NULL,
   hash_table_hash TEXT NOT NULL,
);
*/

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 0);
set_time_limit(0);

$time_start = microtime(true);

if($argc != 3){
  die("Error: usage: php decompress.php <input_file> <output_file>");
}

$db = new SQLite3('database.sqlite');

$handle = @fopen($argv[1], "r");
$fp = fopen($argv[2], 'wb');

//$hex = bin2hex(fread ($handle , 131072));



if ($handle) {
    $file_str = "";
    $tmp_file_str = "";
    $i=0;
    echo "Decompressing...\n";
	
    while (!feof($handle)) {

        $hash = bin2hex(fread ($handle , 4));
		$check = bin2hex(fread ($handle , 4));

        //$hash = hash('crc32',$hex);
        //$short_hash = substr($hash, -2);

		$result = $db->query("SELECT * FROM hash_table WHERE hash_table_hash='$hash'");
		//$result = mysql_query("SELECT * FROM hash_table WHERE hash_table_hash='$hash'") or die(mysql_error());
		
         //while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		 while($row = $result->fetchArray()){

		//echo ":$hash:$row[hash_table_val]\n\n";
		//echo "SELECT * FROM hash_table WHERE hash_table_hash='$hash'\n";
		$tmp_file_str=$file_str.$row['hash_table_val'];

		//echo "$row[hash_table_val]:".hash('crc32',$tmp_file_str)."\n";
			//echo "H".$row['hash_table_val'];

		  if(!strcmp($check,hash('crc32',$tmp_file_str))){
			  //$write_array = str_split($row['hash_table_val'], 4096);
			  //foreach($write_array as $chunk){
			  //	echo $chunk."\n";
				  fwrite($fp, pack("H*",$row['hash_table_val']));
				  $file_str.=$row['hash_table_val'];
			  //}	
		  }
		  else{
			  //"\nno match for $row[hash_table_val]\n";
		  }
        }

	//echo ":$hash:\n\n";


	//$short_file_hash = substr($file_hash, -2);

	//if($i==0){
	//	fwrite($fp, pack("H*",$hex));
	//}

	//fwrite($fp, pack("H*", $hash));
	//fwrite($fp, pack("H*",$file_hash));

        //echo "$hex:$hash $file_hash\n";
        //$i++;
    }
    fclose($handle);
    fclose($fp);

}
else echo "Can't read file: $argv[1]";


$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Execution time: $time\n";

/*
  $result = mysql_query("SELECT * FROM hash_table WHERE hash_table_hash='f2'") or die(mysql_error());

  while ($hash = mysql_fetch_array($result, MYSQL_ASSOC)){
    //if($hash['hash_table_num'] == 3430)
      echo "$hash[hash_table_num]:$hash[hash_table_hash]:".hash('crc32',$hash['hash_table_num'])."\n";
  }

  $result = mysql_query("SELECT * FROM hash_table WHERE hash_table_hash='f3'") or die(mysql_error());

  while ($hash = mysql_fetch_array($result, MYSQL_ASSOC)){
    if("27" == substr(hash('crc32',"3430".$hash['hash_table_num']), -2))
      echo substr(hash('crc32',"3430".$hash['hash_table_num']), -2)." $hash[hash_table_num]:$hash[hash_table_hash]:".hash('crc32',$hash['hash_table_num'])."\n";
  }
*/
?>
