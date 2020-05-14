<?php
class testA{
    public function sayHi($str){
        return 'hi,'.$str;
    }

    public function add($a,$b){
        return $a+$b;
    }
}

$ss = new SoapServer(null, array('uri'=>'sampleA'));
$ss->setClass('testA');
$ss->handle();
