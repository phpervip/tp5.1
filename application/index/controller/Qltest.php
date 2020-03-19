<?php


namespace app\index\controller;
use think\Controller;
use QL\QueryList;


class Qltest extends Controller
{
	// tp5.ccc/index/qltest/amtb
	// public function amtb(){
	// 	$ql = new QueryList->getHtml();	
	// 	$urls = [
	// 		'https://amtbapi.amtb.de/amtbtv/docs/01-003-0001/txt/zh_TW',
	// 		'https://amtbapi.amtb.de/amtbtv/docs/01-003-0002/txt/zh_TW'
	// 	];
 //       foreach($urls as $k=>$url) {
	// 		$res = $ql->get($url)->query();
	// 		var_dump($res);
	// 		// 释放资源，销毁内存占用
	// 		// $ql->destruct();
	// 	}
	// }

	// tp5.ccc/index/qltest/sina
	public function sina(){
		$url = 'http://www.sina.com.cn';
		// $url = 'http://tp5.1.yyii.info';
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

		foreach ( $new_url as $url ) {
    		download($url);
		}

	}

	
}