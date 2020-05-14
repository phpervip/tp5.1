<?php


namespace app\index\controller;
use think\Controller;
// 学习wsdl
// 参考文章：https://blog.csdn.net/liujunlovephp/article/details/70138821

// 服务端
class Web extends Controller
{
    public function index()
    {
        WebService(url('web/index'), 'Web');
    }

    public function itemType($type = '', $style = '')
    {
        echo $type . $style;
    }
}