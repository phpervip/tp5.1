<?php
// 第四部：写一个生成wsdl文件的执行程序，在这里我来新建一个文件 creat_wsdl.php，同样放在service同级目录下面，内容如下：
// 第五步：执行creat_wsdl.php文件
// http://tp5.ccc/service/create_wsdl.php
include("api.php");
include("../../extend/soap/SoapDiscovery.class.php");
$disc = new SoapDiscovery('api','service');//api类文件名，service接口目录
$disc->getWSDL();
?>