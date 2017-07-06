<?php
// var_dump($_SERVER);
/*
$url="http://fanyi.baidu.com/index.php?page=1";
var_dump(parse_url($url));*/

/*将传递上来的转化为关联数组*/
/*
$str = 'username=dons&password=123456';
parse_str($str,$arr);
var_dump($arr);*/

/*将关联数组转化为query*/
// $arr = ['username'=>'dons','password=>123456'];
// $str = http_build_query($arr);
// var_dump($str);

$home = $_SERVER['REQUEST_SCHEME'];
var_dump($home);