<?php
/**
 * Created by mac.
 * User: mac
 * Date: 2019/11/30
 * Time: 11:16 AM
 */

namespace app\index\controller;
use think\Controller;
class Caiji extends Controller
{	
	// 多线程curl采集
	// tp5.ccc/index/Caiji/index1/
	public function index1(){
		$urls = [
			[
				'url'=>'https://amtbapi.amtb.de/amtbtv/docs/01-003-0001/txt/zh_TW',
				'id'=>1
			]
		];

		$mh = curl_multi_init();
		$conn = array();
		foreach ($urls as $urlItem) {
		    $ch = curl_init($urlItem['url']);
		    $conn[(int)$ch] = $urlItem;    				//记录资源与参数映射
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);  //不直接输出结果
		    curl_multi_add_handle($mh, $ch);
		}
		$active = null;
		$res = array();
		do {
		    $status = curl_multi_exec($mh, $active);
		    $info = curl_multi_info_read($mh);
		    if (false !== $info) {
		        //采集信息处理
		        $res[] = array(
		            'content'   => curl_multi_getcontent($info['handle']),
		            'info'      => $info,
		            'param'     => $conn[(int)$info['handle']],
		        );
		        curl_close($info['handle']);
		    }
		} while ($status === CURLM_CALL_MULTI_PERFORM || $active);
		curl_multi_close($mh);
		var_dump($res);

	}

	// tp5.ccc/index/Caiji/index2/
	public function index2(){
		error_reporting(0);
		 // CURL多线程采集
		set_time_limit(0);
		$start_time=time();
		$cj_array = [
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
		$cj_data =Curl_http($cj_array,'20');
		ob_clean();
		foreach ((array)$cj_data as $k=>$v)
		{
		    if($v !=''){
		    file_put_contents($k, $v);
		    }
		    else
		    {
		       unset($k,$v);
		    }
		}
		$end_time=time();
		$use_time = $end_time-$start_time;
		echo $use_time;
	}

	

}