<?php
// 第一步 ：创建一个service的文件夹专门存放您的api接口项目
// 第二步：下载SoapDiscovery.class.php类文件，放在您的service文件夹里面
// 第三步：在service文件夹下面创建您自己api接口类文件api.php，文件内容示例如下：

class api{
    //我的测试接口方法
    public function test(){
        return "hello world";
    }
}
// 在本地时，生成 http://tp5.ccc/service/create_wsdl.php 时要注释下面的。
// 第七步 ：在api的类文件api.php的最下面，加上调用的程序，加上之后api.php的文件内容如下：
$server = new SoapServer('api.wsdl', array('soap_version' => SOAP_1_2)); ##此处的Service.wsdl文件是上面生成的
$server->setClass("api"); //注册Service类的所有方法
$server->handle();
?>