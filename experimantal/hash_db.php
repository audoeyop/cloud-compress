<?php
  $link = mysql_connect('localhost', 'root', '');
  if (!$link) {
    die('Not connected : ' . mysql_error());
  }

  $db_selected = mysql_select_db('hash', $link);
  if (!$db_selected) {
      die ('Can\'t use foo : ' . mysql_error());
  }

//$init_str = '0';

//$target_str = str_repeat("f", $total_len);
/*
while($init_str != $target_str){
  //echo "$init_str ";
  $init_str = getPassword($init_str, 3); 
}
*/

/*
  $result = mysql_query("SELECT * FROM hash_table WHERE hash_table_hash='c4ca4238a0b923820dcc509a6f75849b'") or die(mysql_error());

  while ($hash = mysql_fetch_array($result, MYSQL_ASSOC)){
    echo "$hash[hash_table_num]\n";
  }
*/


$handle = @fopen("file.js", "r");
$fp = fopen('output', 'wb');

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

      $result = mysql_query("SELECT * FROM hash_table WHERE hash_table_val='$hex'") or die(mysql_error());
      
      if($row = mysql_fetch_array($result, MYSQL_ASSOC));
      else{
        mysql_query("INSERT into hash_table (hash_table_val,`hash_table_hash`) VALUES('$hex','$hash')");
      }
      fwrite($fp, pack("H*", $hash));
      fwrite($fp, pack("H*",$file_hash));

      //echo "$hex:$hash $file_hash\n";
      $i++;
    }
    fclose($handle);
    fclose($fp);

}


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
