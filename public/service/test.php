<?php
// 第八步：测试，在service外随意的位置（只要能访问得到）创建测试文件 命名为：test.php,文件内容如下：
// http://tp5.ccc/service/api.php?wsdl
$client = new SoapClient("http://tp5.ccc/service/api.php?wsdl"); //这里的链接换成你自己的访问链接
print_r($client->__getFunctions());
echo "<br/>";
echo "ok";

