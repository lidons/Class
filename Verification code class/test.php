<?php
session_start();
require_once 'code.php';

$code= new Code(4,1,50,100);
$_SESSION['code']= $code->getCode();
$code->outImage();


