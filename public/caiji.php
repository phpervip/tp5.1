<?php

 // CURL多线程采集
set_time_limit(0);
$start_time=time();
$imgarray = [
	'01-003-0001.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0001/txt/zh_TW',
	'01-003-0002.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0002/txt/zh_TW',
	'01-003-0003.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0003/txt/zh_TW',
	'01-003-0004.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0004/txt/zh_TW',
	'01-003-0005.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0005/txt/zh_TW',
	'01-003-0006.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0006/txt/zh_TW',
	'01-003-0007.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0007/txt/zh_TW',
	'01-003-0008.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0008/txt/zh_TW',
	'01-003-0009.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0009/txt/zh_TW',
	'01-003-0010.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0010/txt/zh_TW',
	'01-003-0011.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0011/txt/zh_TW',
	'01-003-0012.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0012/txt/zh_TW',
	'01-003-0013.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0013/txt/zh_TW',
	'01-003-0014.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0014/txt/zh_TW',
	'01-003-0015.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0015/txt/zh_TW',
	'01-003-0016.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0016/txt/zh_TW',
	'01-003-0017.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0017/txt/zh_TW',
	'01-003-0018.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0018/txt/zh_TW',
	'01-003-0019.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0019/txt/zh_TW',
	'01-003-0020.json'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0020/txt/zh_TW',
];
$dataimg =Curl_http($imgarray,'20');
ob_clean();
foreach ((array)$dataimg as $kk=>$vv)
{
    if($vv !=''){
    file_put_contents($kk, $vv);
    }
    else
    {
       unset($kk,$vv);
    }
}

$end_time=time();

$use_time = $end_time-$start_time;
echo $use_time;


function Curl_http($array,$timeout='15')
{
    $res = array();
    $mh = curl_multi_init();//创建多个curl语柄
    foreach($array as $k=>$url)
    {
        $conn[$k]=curl_init($url);//初始化
        curl_setopt($conn[$k], CURLOPT_TIMEOUT, $timeout);//设置超时时间
        curl_setopt($conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($conn[$k], CURLOPT_MAXREDIRS, 7);//HTTp定向级别 ，7最高
        curl_setopt($conn[$k], CURLOPT_HEADER, false);//这里不要header，加块效率
        curl_setopt($conn[$k], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($conn[$k], CURLOPT_RETURNTRANSFER,1);//要求结果为字符串且输出到屏幕上
        curl_setopt($conn[$k], CURLOPT_HTTPGET, true);
        curl_multi_add_handle ($mh,$conn[$k]);
    }

    do
    {
        $mrc = curl_multi_exec($mh,$active);//当无数据，active=true
    }
    while ($mrc == CURLM_CALL_MULTI_PERFORM);//当正在接受数据时
    while ($active and $mrc == CURLM_OK)
    {//当无数据时或请求暂停时，active=true
        if (curl_multi_select($mh) != -1)
        {
            do {
              $mrc = curl_multi_exec($mh, $active);
            }
            while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }
    foreach ($array as $k => $url)
    {
        if(!curl_errno($conn[$k]))
        {
            $data[$k]=curl_multi_getcontent($conn[$k]);//数据转换为array
            $header[$k]=curl_getinfo($conn[$k]);//返回http头信息
            curl_close($conn[$k]);//关闭语柄
            curl_multi_remove_handle($mh , $conn[$k]); //释放资源
        }
        else
        {
            unset($k,$url);
        }
    }
    curl_multi_close($mh);
    return $data;
}
