<?php
// 第六步：注册api类文件中的所有方法，在service文件夹下新建一个注册类文件的执行文件命名为：cometrue.php，文件内容如下：
$server = new SoapServer('api.wsdl', array('soap_version' => SOAP_1_2)); ##此处的Service.wsdl文件是上面生成的
$server->setClass("api"); //注册Service类的所有方法
$server->handle();
