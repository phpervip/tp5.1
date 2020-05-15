<?php
define('WSDL_FILE','ts2.wsdl');
if(!file_exists(WSDL_FILE)){
    require_once('../../extend/soap/SoapDiscovery.class.php');
    $sd = new SoapDiscovery('testD', 'liang');
    $str = $sd->getWSDL();
    file_put_contents(WSDL_FILE, $str);
}

$ss = new \SoapServer(WSDL_FILE);
$ss->setClass('testD');
$ss->handle();

class testD{
    public function sayHello($world){
        return 'hello,'.$world;
    }

    public function add($a, $b){
        return $a+$b;
    }
}
