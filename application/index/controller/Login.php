<?php
namespace app\index\controller;

use think\Controller;
use Env;
use vendor\Wxlogin\Wxlogin;

class Login extends Controller
{
		public function index(){
			return $this->fetch();
		}

	/**
     * # +========================================================================
     * # | - @name        授权处理
     * # | - @author     cq <just_leaf@foxmail.com> 
     * # | - @copyright zmtek 2018-11-07
     * # +------------------------------------------------------------------------
     * 
     * # +========================================================================
     */    
    public function authorization() {
        $code  = $_GET['code'];
        $state = $_GET['state'];
        if(!$code || !$state) die('参数不能为空');
        # 验证参数，防刷
        if($state != session('state')){
           // die('错误state');
        }
        include_once(Env::get('root_path').'vendor/Wxlogin/Wxlogin.php');
		$Wx = new \Wxlogin();

        // $Wx = new \Wxlogin();
         
        # 确认授权后会，根据返回的code获取token
        $token = $Wx->get_access_token($_GET['code']); 
        
        # 获取用户信息
        $user_info = $Wx->get_user_info($token['access_token'],$token['openid']); 
　　　　　var_dump($user_info);
    }
}