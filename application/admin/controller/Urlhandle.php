<?php
namespace app\admin\controller;
use think\Controller;
class Urlhandle extends Controller
{

	// http://tp5.ccc/admin/urlhandle/view_qrcode?url=http://www.baidu.com&size=1000
	public function view_qrcode($url='http://www.baidu.com',$size='',$name=''){
        $url = input('url');
        if(empty(input('size'))){
            $size = 300;
        }else{
            $size = input('size');
        }
        if(empty(input('name'))){
            $name = 'png';
        }else{
            $name = input('name');
        }
        qrcode($url,$level='high',$size,$label='',$fontsize=16,$name);
    }

}
