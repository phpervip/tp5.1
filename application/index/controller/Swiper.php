<?php


namespace app\index\controller;
use think\Controller;
// 原文写extends Common，Common里是什么呢？
class Swiper extends Controller
{
    public function index()
    {
        // 这里itemType是什么呢
        $client = WebClient();
        $res = $client->itemType('swiper_price', '1234');
    }
}
