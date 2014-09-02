<!DOCTYPE html> 
<html>

<head>
<?php
echo "<title>addonline.hu - $title</title>";
  ?>
  

<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?php 
if (!empty($style))
{
    foreach((array) $style as $cssname)
    {
        echo "<link rel='stylesheet' type='text/css' href='/style/$cssname.css' />";
    }
  }
  
  ?>
</head>
<body>

