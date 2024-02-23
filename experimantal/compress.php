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
  die("Error: usage: php compress.php <input_file> <output_file>");
}

$db = new SQLite3('database.sqlite');

$handle = @fopen($argv[1], "r");
$fp = fopen($argv[2], 'wb');

if ($handle) {
    echo "Compressing...\n";
    $file_str = "";
    $i=0;
    while (!feof($handle)) {
        $hex = bin2hex(fread ($handle , 131072));

        $hash = hash('crc32',$hex);
        //$short_hash = substr($hash, -2);

		$file_str.=$hex;

        $file_hash = hash('crc32',$file_str);
	  //$short_file_hash = substr($file_hash, -2);
  
	  //if($i==0){
	  //	fwrite($fp, pack("H*",$hex));
	  //}
  
	  //echo "$hex:$hash";
  
	  $result = $db->query("SELECT * FROM hash_table WHERE hash_table_val='$hex'");
	  //$result = mysql_query("SELECT * FROM hash_table WHERE hash_table_val='$hex'") or die(mysql_error());
	  
	  if($row = $result->fetchArray());
	  else {
	   //if($row = mysql_fetch_array($result, MYSQL_ASSOC));
	  //else{
			$db->query("INSERT into hash_table (hash_table_val,`hash_table_hash`) VALUES('$hex','$hash')");
	  //	mysql_query("INSERT into hash_table (hash_table_val,`hash_table_hash`) VALUES('$hex','$hash')");
	  //}
	  }
	  fwrite($fp, pack("H*", $hash));
	  fwrite($fp, pack("H*",$file_hash));

        //echo "$hex:$hash $file_hash\n";
        $i++;
    }
    fclose($handle);
    fclose($fp);

}
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
