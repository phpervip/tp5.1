<?php
namespace app\admin\controller;
use seccode\seccode;
use think\Controller;
class Login extends Controller
{
    // http://tp5.ccc/admin/login/index 后台登录
    public function index(){
        return $this->fetch('login');
    }
    // http://tp5.ccc/admin/login/makecodeOp?admin=1&nchash=e7c90f22
    // ShopNC 生成验证码 的方法
    public function makecodeOp(){
        //$refererhost = parse_url($_SERVER['HTTP_REFERER']);
        //$refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';
        $seccode = makeSeccode($_GET['nchash']);
        @header("Expires: -1");
        @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
        @header("Pragma: no-cache");
        $code = new seccode();
        $code->code = $seccode;
        $code->width = 90;
        $code->height = 26;
        $code->background = 1;
        $code->adulterate = 1;
        $code->scatter = '';
        $code->color = 1;
        $code->size = 0;
        $code->shadow = 1;
        $code->animator = 0;
        $code->datapath =  ROOT_PATH.'/public/resource/seccode/';
        $code->display();
    }
}