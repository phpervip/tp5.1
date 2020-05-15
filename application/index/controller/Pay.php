<?php
namespace app\index\controller;
use think\Controller;
use app\index\service\Pay as PayService;
// 学习wsdl
// 参考教程:http://blog.yuanrb.com/houduan/197/  (下面代码与教程不同，有改过)
// 参考教程：http://blog.yuanrb.com/houduan/191/
// 服务端
// http://tp5.ccc/index/Pay/creatWsdl
// http://tp5.ccc/index/Pay/index?wsdl
// 读出来是一个个空白的框
// 客户端
// http://tp5.ccc/index/Pay/client
// 客户端方法里，但是$data是什么呢？？代码运行不了

class Pay extends Controller {
    protected $PayService;
    protected $error;

    public function _initialize(){
        $this->PayService = new PayService();
        $this->error = '';
        $this->creatWsdl();
    }
    public function index(){
        // 实例化SoapServer服务
        // 第一个参数是刚才生成的wsdl文件的地址
        // 第二个是soap版本
        $server = new \SoapServer(ROOT_PATH.config('WSDL_PATH').'appPay'.".wsdl", array('soap_version' => SOAP_1_2));
        // 注册对应类的方法，这里是当前类
        $server->setClass(Pay::class);
        //处理请求
        $server->handle(); 
    	return;
    }
    public function payRequest($params){
        if(!$params){
            $this->error = '参数不能为空';
            return $this->returnInfo();
        }
    	$result = '收到数据';
    	if(!$result){
    		$this->error = $this->PayService->getError();
    		return $this->returnInfo();
    	}
    	return $this->returnInfo();
    }
    public function returnInfo(){
    	$data = array();
    	$data['MSGCODE'] = '0';
    	$data['MSG'] = '';
    	if($this->error){
    		$data['MSGCODE'] = '-1';
    		$data['MSG'] = $this->error;
    	}
    	return base64_encode(json_encode($data));
    }
    public function creatWsdl(){
        // 获取配置文件中写的wsdl文件存放目录
        $path = ROOT_PATH.config('WSDL_PATH');
        // 判断对应目录是否存在，没有则创建
        if(!is_dir($path)){
            mkdir($path);
        }
        //判断wsdl文件是否存在，没有则创建
        //这里是以当前类名字来命名的wsdl文件，所以用到tp的CONTROLLER_NAME
        if (!file_exists($path.'Pay'.".wsdl" )){
            // 引入SoapDiscovery.class.php，我把它放在/extend/soap/目录下
            require(ROOT_PATH.'extend/soap/SoapDiscovery.class.php');
            // 这里传入第一个参数是当前类的路径（生成的wsdl文件以当前类命名）
            // 第二个参数是服务名字（可根据具体用处命名）
            // 第三个是wsdl文件存放的地址，不传默认是tp的根目录（在SoapDiscovery.class.php中定义的）
            $disco = new \SoapDiscovery( Pay::class,'soap',$path);
            // 生成wsdl文件
            $disco->getWSDL();
        }
    }


    public function client(){
        $soapUrl = 'http://tp5.ccc/index/Pay/index?wsdl';
        try{
            $client = new \SoapClient($soapUrl);
            $result = $client->PayRequest(base64_encode($data));
            $result = json_decode(base64_decode($result),true);
            if($result['MSGCODE']!=0){
                $this->error = $result['MSG'];
                return false;
            }
            return true;

        }catch(SoapFault $e){
            echo $e->getMessage();

        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}