<?php
//Libs
require_once ($_SERVER['DOCUMENT_ROOT']. '/modules/lk/config.php');
//Code
foreach ($servers as $server)
  {
  echo  "'".$server['title']."': '".$server['title']."',";
  }
?>