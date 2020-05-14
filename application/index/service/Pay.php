<?php
/**
 * Created by mac.
 * User: mac
 * Date: 2019/10/11
 * Time: 4:48 PM
 */
namespace app\index\service;
use think\Exception;
// extend/WxPay/WxPay.Api.php
// Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
require(ROOT_PATH.'extend/WxPay/WxPay.Api.php');
class Pay
{
    private $orderID;
    function __construct($orderID)
    {
        if (!$orderID)
        {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    public function pay()
    {
        //订单号可能根本就不存在
        //订单号确实是存在的，但是，订单号和当前用户是不匹配的
        //订单有可能已经被支付过
        //进行库存量检测
        // 按订单预付价格，无须再次计算。
        return [
            'result'=>'success',
            'data'  => [
                'order_no'       => 10001,
                'order_name'     => '商品名称',
                'amount'         => 100,
            ]
        ];
    }

}