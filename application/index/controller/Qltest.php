<?php
/**
 * Created by mac.
 * User: mac
 * Date: 2019/11/30
 * Time: 11:16 AM
 */

namespace app\index\controller;
use think\Controller;
use QL\QueryList;

// tp5.ccc/altest/index
class Qltest extends Controller
{
	public function index(){
		$ql = new QueryList->getHtml() ;	
		$urls = [
			'https://amtbapi.amtb.de/amtbtv/docs/01-003-0001/txt/zh_TW',
			'https://amtbapi.amtb.de/amtbtv/docs/01-003-0002/txt/zh_TW'
		];
       foreach($urls as $k=>$url) {
			$res = $ql->get($url)->query();
			var_dump($res);
			// 释放资源，销毁内存占用
			// $ql->destruct();
		}
	}

	
}