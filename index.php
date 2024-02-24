<?php
/*
Cloud Compress
==============
Author: Akaninyene Udoeyop
Cloud Compress is an application that compresses
files by hashing binary blobs of their contents. 

IMPORTANT: Create the following table in your DB
================================================
CREATE TABLE hash_table(
hash_table_hash TEXT PRIMARY KEY,
hash_table_val  TEXT
);
*/

error_reporting(E_ALL);					      // report all errors
ini_set('display_errors', '1');			  // display errors
ini_set("log_errors", 1);             // log errors
ini_set("error_log", "./error.log");  // error log file
ini_set('max_execution_time', 0);		  // no limit on execution time
ini_set('upload_max_filesize', '2M');	// 2M maximum file upload size
ini_set('post_max_size', '2M');			  // 2M maximum post request size
set_time_limit(0);                   	// ignore php timeout
session_start();  						        // begin php session

$block_size = pow(2, 16); 				    // block size in bytes
$db_type = "sqlite";					        // mysql, sqlite

if(!strcmp($db_type,"mysql")){
	$dbhost = 'localhost:8889';
	$mysqli = new mysqli($dbhost, "root", "root", "compress");
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
}

ob_implicit_flush(true);             	// output stuff directly
  
// get session id
$session_id = session_id();

if(isset($_GET['online'])){
  if(isset($_GET['compress'])){

    // initialize datsbase
  	if(!strcmp($db_type,"sqlite"))
    	$db = new SQLite3('database.sqlite');

    // read temporary file
    readfile($_FILES["file"]["tmp_name"]);

    // create session directory if it doesn't exist
    if (!file_exists($session_id)) {
      mkdir($session_id, 0777, true);
    }

    // get uploaded tmp file
    $handle = fopen($_FILES["file"]["tmp_name"], "r");

    // delete .ccp tmp file if it already exists
    if (file_exists($session_id . "/" . basename($_FILES["file"]["name"]).".ccp")) {
      unlink($session_id . "/" . basename($_FILES["file"]["name"]).".ccp");
    }

    // open .cpp tem file
    $fp = fopen($session_id . "/" . basename($_FILES["file"]["name"]).".ccp", 'wb');

    while (!feof($handle)) {

      // get hex of binary blob
      $hex = bin2hex(fread ($handle , $block_size));
      
      // get crc32 hash of binary
      $hash = hash('crc32',$hex);

      // search whether hash_table_val exists
      if(!strcmp($db_type,"sqlite"))
        $result = $db->query("SELECT * FROM hash_table WHERE hash_table_val='$hex'");
      elseif(!strcmp($db_type,"mysql")){
        $result = $mysqli->query("SELECT * FROM hash_table WHERE hash_table_val='$hex'");
      }

      // insert record into hash_table if it does not exist
      if(!strcmp($db_type,"sqlite")){
        if($row = $result->fetchArray());
        else {
          if(!strcmp($db_type,"sqlite"))
            $db->query("INSERT into hash_table (hash_table_val,`hash_table_hash`) VALUES('$hex','$hash')");
          }
        }
        elseif(!strcmp($db_type,"mysql")){
          if ($row = $result->fetch_assoc());
          else{
            if(!strcmp($db_type,"mysql"))
              $mysqli->query("INSERT into hash_table (hash_table_val,`hash_table_hash`) VALUES('$hex','$hash')");
          }
        }

        // write data to .ccp tmp file
        fwrite($fp, pack("H*", $hash));
    }

    // close file handlers
    fclose($handle);
    fclose($fp);
    exit(0);
  }
  else if(isset($_GET['decompress'])){

 	  //initialize database
    if(!strcmp($db_type,"sqlite"))
    	$db = new SQLite3('database.sqlite');

      // get filename from .cpp file
      if(!strcmp(substr(basename($_FILES["file"]["name"]), -4),".ccp"))
      {
        $filename = substr(basename($_FILES["file"]["name"]), 0, -4);
      }
      else{
        $filename = basename($_FILES["file"]["name"]).".decompressed";
      }

      // create session directory if it doesn't exist
      if (!file_exists($session_id)) {
        mkdir($session_id, 0777, true);
      }

      // delete decompressed file if it exists in session directory
      if (file_exists($session_id . "/" . $filename)) {
          unlink($session_id . "/" . $filename);
      }

      // open the decomressed file handler
      $fp = fopen($session_id . "/" . $filename, 'wb');

      // read uploaded tmp  file
      $handle = fopen($_FILES["file"]["tmp_name"], "r");

      // traverse through file
      while (!feof($handle)) {

        // get hash of file blob
        $hash = bin2hex(fread ($handle , 4));

        // search for file contents via hash
        if(!strcmp($db_type,"sqlite"))
            $result = $db->query("SELECT * FROM hash_table WHERE hash_table_hash='$hash'");
        elseif(!strcmp($db_type,"mysql"))
            $result = $mysqli->query("SELECT * FROM hash_table WHERE hash_table_hash='$hash'");

        // write data to decompressed file
        if(!strcmp($db_type,"sqlite")){
          while($row = $result->fetchArray()){
            fwrite($fp, pack("H*",$row['hash_table_val']));
          }
        }
        elseif(!strcmp($db_type,"mysql")){
          while($row = $result->fetch_assoc()){
            fwrite($fp, pack("H*",$row['hash_table_val']));
			    }
        }
	    }
    }
    fclose($handle);
    fclose($fp);
    exit(0);
  }
  

?>


<html>
  <head>
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Muli:400italic|Chango' rel='stylesheet' type='text/css'>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </head>
  <body style="margin:0;padding:0;font-family: 'Muli', sans-serif;color: #656565;font-size: 15px;">
    <nav class="navbar navbar-default" role="navigation" style="position:absolute;top:0px;width:100%">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="?">
            <img src="assets/cloud.png" style="height:30px;"/>&nbsp;
            <span id="cloudcompress" style="font-family:impact;font-size:17px;color: #333333">CloudCompress</span>
          </a>
        </div>
    
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <div style="margin:auto;width:30vw;margin-top:30px;margin-top:100px;" id="cloud">
      <!-- <img src="assets/inverse_cloud.png" style="width:30vw;"/> -->
      <img src="assets/big_data.jpg" style="width:30vw;"/>
    </div>
    
    <div style='text-align: center'>
      <?php if(isset($_GET['online'])): ?>
      <span style="color:#339933"><span class="glyphicon glyphicon-ok"></span> Success!</span><br/><br/>
      Your file has been compressed, and is being downloaded.
      <?php else: ?>
      <b style="font-size:5vw">CloudCompress</b>&#8482;<br/>
      Turn big files into tiny files, then back into big files.
      <?php endif; ?>
    </div>
    <div class="container" style="margin-top: 60px">
      <div id="step1" style="text-align:center;margin-bottom:30px;line-height:" class="row">
        <?php if(isset($_GET['online'])): ?>
          <a href="?" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;&nbsp;Back to Home</a>
        <?php else: ?>
        <div class="col-sm-6">
          <div style="margin-bottom:10px">
          	<a href="?compress=1&step1=1" class="btn btn-primary compress"><span class="glyphicon glyphicon-resize-small"></span>&nbsp;&nbsp;Compress File</a>
          </div>
        </div>
        <div class="col-sm-6">
          <div style="margin-bottom:10px">
          	<a href="?decompress=1&step1=1" class="btn btn-primary decompress"><span class="glyphicon glyphicon-resize-full"></span>&nbsp;&nbsp;Decompress File</a>
          </div>
        </div>
        
        <?php endif; ?>
      </div>
    </div>
    
    <div id="step2" style="height:400px; background-color:#efefef;border-top: solid 1px #cccccc;display: none;">
      <div class="container">
        <div class="row">
          <h2 class="text-center"></h2>
          <div style="text-align:center;margin-top:60px">
            <span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;Select a file that you would like to <span class="action"></span><br>
            <small>
            <span class="action-hint"></span>
            </small>
            <br/>
            <form id="upload-form" method="post" enctype="multipart/form-data" action="">
              <input class="form-control" id="file" type="file" name="file" style="width:400px;margin:auto" />
              <br/>
              <div>
                <small class="progress"></small>
              </div>
              <br/>

                <div class="btn btn-primary compress-submit">
                  <span class="action-button"></span> + Download&nbsp;&nbsp;<span class="glyphicon glyphicon-cloud-download"></span>
                </div>
            </form>
            <br/><br/>
            <script>
              function progressHandlingFunction(e){
                  if(e.lengthComputable){
                      //$('progress').attr({value:e.loaded,max:e.total});
                      $('.progress').html('<img src="assets/ajax-loader.gif" />&nbsp;&nbsp;Uploading and ' + $('.action').html() + 'ing file. This may take some time.');
                  }
              }
              function completeHandler(e){
                  $('.step3-action').html($('.action').html() + "ed");
                  $('#step3').slideDown()
                  $('#step3 h2').html("<span class='glyphicon glyphicon-cloud-download'></span>&nbsp;&nbsp;You're Done!")
                  $('html, body').animate({ scrollTop: $(document).height() }, "slow");
                  $('.progress').html('<span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;File uploaded and ' + $('.action').html() + 'ed.');

                  var filename = '';
                  if($('.action').html() == 'compress'){
                    filename = $('[name=file]').val().split('\\').pop()+".ccp";
                  }
                  else if($('.action').html() == 'decompress'){
                    filename = $('[name=file]').val().split('\\').pop();

                    if(filename.substr(-4) == ".ccp"){

                      filename = filename.substr(0,filename.length-4);
                    }
                    else{
                      filename = filename + ".decompressed";
                    }
                  }
                  $('#step3 .step3-button').html("<a href='<?php echo $session_id ?>/"+filename+"' download='"+filename+"'class='btn btn-success'>Download "+$('.action-button').html()+"ed File&nbsp;&nbsp;<span class='glyphicon glyphicon-cloud-download'></span></a>")
              }
              // Attach a submit handler to the form
              $( ".compress-submit").click(function( event ) {
                //event.preventDefault();

                  if($('[name=file]').val()) {
                    var action = '';
                    if($('.action').html() == 'compress'){
                      action = '?online=1&compress=1';
                    }
                    else if($('.action').html() == 'decompress'){
                      action = '?online=1&decompress=1';
                    }
                    var formData = new FormData($('#upload-form')[0]);
                    $.ajax({
                        url: action,  //Server script to process data
                        type: 'POST',
                        xhr: function() {  // Custom XMLHttpRequest
                            var myXhr = $.ajaxSettings.xhr();
                            if(myXhr.upload){ // Check if upload property exists
                                myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // For handling the progress of the upload
                            }
                            return myXhr;
                        },
                        //Ajax events
                        //beforeSend: beforeSendHandler,
                        success: completeHandler,
                        //error: errorHandler,
                        // Form data
                        data: formData,
                        //Options to tell jQuery not to process data or worry about content-type.
                        cache: false,
                        contentType: false,
                        processData: false
                      });
                    }
                    else{
                      alert("Please select a file.")
                    }
                  });
                  
                  

            </script>
          </div>
        </div>
      </div>
      <script>
          $('#step1 .btn').click(function(e){
            if($(this).hasClass('compress')){
              $('.action-button').html('Compress');
              $('.action-hint').html('');
              $('.action').html('compress');
              $('#upload-form').attr('action','?online=1&compress=1');
              $('#step2 h2').html("<span class='glyphicon glyphicon-cloud-upload'></span>&nbsp;&nbsp;Select file to compress");
            }
            else if($(this).hasClass('decompress')){
              $('.action-button').html('Decompress');
              $('.action-hint').html('<br/><span class="badge">Must be a Cloud Compressed (.ccp) file.</span><br/><br/>');
              $('.action').html('decompress')
              $('#upload-form').attr('action','?online=1&decompress=1');
              $('#step2 h2').html("Select file to decompress");
            }
            $('#step2').slideDown()
            $('html, body').animate({ scrollTop: $(document).height() }, "slow");
            $('#step3').hide();
          })

      </script>
    </div>
    
    <div id="step3" style="height:400px; background-color:#dedede;border-top: solid 1px #cccccc;display: none;">
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <h2 class="text-center"></h2>
            <div class="text-center" style="margin-top:30px">
              <span class="glyphicon glyphicon-floppy-save"></span>&nbsp;&nbsp;Congratulations! Your file has successfully been <span class="step3-action"></span>. Download the <span class="step3-action"></span> file by clicking on the green button below.<br/><br/><br/>
              <span class="step3-button"></span>
              <br/><br/><br/><br/><br/>
            </div>
            <div class="col-sm-6 text-center">
              <a href="?compress=1&step1=1" class="btn btn-primary online"><span class="glyphicon glyphicon-resize-small"></span>&nbsp;&nbsp;Compress another file</a>
            </div>
            <div class="col-sm-6 text-center">
              <a href="?decompress=1&step1=1" class="btn btn-primary online"><span class="glyphicon glyphicon-resize-full"></span>&nbsp;&nbsp;Decompress another file</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <?php
    if(isset($_GET['compress']) && isset($_GET['step1'])): ?>
      <script>
        $('#step1 .compress').click()
      </script>
    <?php
    elseif(isset($_GET['decompress']) && isset($_GET['step1'])): ?>
      <script>
        $('#step1 .decompress').click()
      </script>
    <?php endif; ?>
    <script>

      var uploadField = document.getElementById("file");

      uploadField.onchange = function() {
          if(this.files[0].size > 2100000){
            alert("The maximum file size is 2.1 MB");
            this.value = "";
          };
      };
      // function get_random(val,random_max){
      //   var num = Math.floor(Math.random() * (random_max+1));
      //   if (Math.random() > 0.5 && num != 0)
      //     num *= -1;
      //   //console.log(num)
      //   if ((val + num) >= 255) return 255;
      //   else if ((val + num) <= 0) return 0;
      //   else return (val + num);
      // }
      
      // var init_light_buffer = 120;
      // var r = init_light_buffer + Math.floor((Math.random() * (256 - init_light_buffer)));
      // var b = init_light_buffer + Math.floor((Math.random() * (256 - init_light_buffer)));
      // var g = init_light_buffer + Math.floor((Math.random() * (256 - init_light_buffer)));
      
      // var random_max = 1;
      
      // setInterval(function() {
      //     r = get_random(r, random_max);
      //     g = get_random(g, random_max);
      //     b = get_random(b, random_max);
          
      //     $('#cloud').css('background-color','rgb(' + r + ',' + g + ',' + b + ')');
      // }, 1)


    </script>
  </body>
</html>