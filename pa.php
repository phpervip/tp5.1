<?php
/**
 * Created by PhpStorm.
 * User: 11447474@qq.com
 * Date: 2018/6/30
 * Time: 14:19
 */
require 'vendor/autoload.php';
use QL\QueryList;
$url = 'http://www.sina.com.cn';
$reg = [
    'img' => array('img', 'src'),
];

$data = QueryList::Query($url, $reg)->data;
ini_set('date.timezone', 'Asia/Shanghai');
$filename = date("Ymdhis") . ".jpg";
$urls = [];
foreach ($data as $v) {
    foreach ($v as $value) {
        $urls[] = str_replace('//', 'http://', $value);
    }
}
$new_url = array();
$str = "n.sinaimg.cn";
foreach ($urls as $k => $v) {
    if (strpos($v, $str) != 0) {
        $new_url[] = $v;
    }
}



function download($url, $path = 'images/')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $file = curl_exec($ch);
    curl_close($ch);
    $filename = pathinfo($url, PATHINFO_BASENAME);
    $resource = fopen($path . $filename, 'a');
    fwrite($resource, $file);
    fclose($resource);
}


foreach ( $new_url as $url ) {
    download($url);
}

function dump($arr)
{
    echo '<pre>';
    var_dump($arr);
    echo '</pre>';
}

?>