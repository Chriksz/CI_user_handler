<!DOCTYPE html> 
<html>

<head>
<?php
if (!isset($title)){
$title = false;
  }
  echo "<title>addonline.hu - $title</title>";
  ?>
  

  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <?php if (isset($style)){
  if (is_array ($style)){
  foreach($style as $cssname){
  echo "<link rel='stylesheet' type='text/css' href='/style/$cssname.css' />";
  }
  }
  else{
  echo "<link rel='stylesheet' type='text/css' href='/style/$style.css' />";
  }
  }
  
  ?>
</head>
<body>

