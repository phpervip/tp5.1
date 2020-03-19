<?php
namespace app\index\controller;

use think\Controller;
use app\common\model\Watermark as mWatermark;

class Index extends Controller
{
    public function index(){
        echo '这是首页';exit;
        return $this->fetch();
    }

    public function reduction($sql='select ? from ',$val='a'){
            $i=0;
            session('item',$i);
            $res =  preg_replace_callback("/(\?)/",
                function($matches)use($val){
                     return $val;
            },$sql);

            $article_id = 101;
            $content = '<a href="http://www.baidu.com">内容</a>';
            $res1= preg_replace_callback(
            '/<a .*?href="(.*?)".*?>/is',
                function($matches)use($article_id){
                    return $matches[1];
                },
                $content
            );

            var_dump($res1);
    }




    public function index_old()
    {
        $pageParam  = ['query'=>[]];
        $mWatermark = new mWatermark();
        $list = $mWatermark->paginate(5,false,$pageParam);
        $this->assign('list',$list);
//        $builder = new \JKBuildHtml\Builder();
//        $builder->buildAll();
        return $this->fetchHtml();
    }

    protected function fetchHtml()
    {
        $builder = new \JKBuildHtml\Builder();
        $builder->buildFromFetch( $html = $this->fetch(), input('get.') );
        return $html;
    }

    public function bhtml($id)
    {
        $builder = new \JKBuildHtml\Builder();
        $builder->buildOne(url('index/index/view'), ['id' => $id]);
        return $this->success('操作成功');
    }

    public function view($id){
        $mWatermark = new mWatermark();
        $info = $mWatermark->where('id',$id)->find();
        $this->assign('info',$info);
        // return $this->fetchHtml();
        return $this->fetch();
    }



    public function watermark()
    {
        $filepath = '/upload/src/0001.jpg';
        $filepath_water = '/upload/water/0001.jpg';
        $image = \think\Image::open($filepath);
        $a = rand(1,999);
        // 给原图左上角添加水印并保存water_image.png
        $wz1 = [520,200];
        $wz2 = [425,280];
        $wz3 = [350,280];
        $wz4 = [290,280];
        $txt = [
            '阿弥陀经疏钞演义',
            '净空老法师',
            '一九八四年十二月',
            '台湾景美华藏图书馆'
        ];
        //
//        $txt = [
//            '一九八四年一月',
//            '二零一五年二月',
//            '一九八四年十二月',
//            '二零一五年一月'
//        ];
        foreach($txt as $k=>$v){
            $new_txt[$k] = str_add_delimiter($v,'|');
        }
       // $color = '#715F87';
        $color = '#FF1493';
        $ss = $image
            ->text($new_txt[0],'/upload/ziti/经典繁毛楷.ttf',77,$color,$wz1,0,0)
            ->text($new_txt[1],'/upload/ziti/经典繁毛楷.ttf',40,$color,$wz2,0,0)
            ->text($new_txt[2],'/upload/ziti/经典繁毛楷.ttf',50,$color,$wz3,0,0)
            ->text($new_txt[3],'/upload/ziti/经典繁毛楷.ttf',39,$color,$wz4,0,0)
            ->save($filepath_water);

        // echo "<img src='/".$a."text_image.png'/>";
        echo "<img src='/".$filepath_water."'/>";
    }

    public function watermark1()
    {
        $image = \think\Image::open('./upload/src/123.jpg');
        $a = rand(1,999);
        // 给原图左上角添加水印并保存water_image.png
        $ss = $image
            ->text('释|迦|牟|尼|佛','./upload/ziti/经典繁毛楷.ttf',20,'#ffffff',\think\Image::WATER_CENTER,0,0)
            ->text('阿|弥|陀|佛','./upload/ziti/经典繁毛楷.ttf',25,'#gggggg',\think\Image::WATER_EAST,0,0)
            ->save($a.'text_image.png');
        echo "<img src='/".$a."text_image.png'/>";
    }


    public function watermark2()
    {
        $time = date('Y-m-d');
        // 生成带水印的图片
        // 定义位置
        $path="./Upload/water/".$time.".jpg";
        $path1="water/".$time.".jpg";
        $wz=array(100,300);//水印位置
        $str = '文|字|水|印';
        \think\Image::open('./123.jpg')
            ->text($str, './经典繁毛楷.ttf', 25, '#ffffff',$wz, 0,0)
            ->save($path);
        // var_dump($path1);exit;
        echo "<img src='/".$path."'/>";
       // 保存到表
       // $info['url']=$path1;
       // $info['add_time']=time();
       // M('erweima')->add($info);
       //  return true;
    }


    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    // tp5.ccc/index/json/?num=01-003-0001(因为中划线，会导致找不到。）)
    // tp5.ccc/json/00001
    public function json($num=''){
        $file_path = 'upload/json/'.$num.".json";
        // var_dump(ROOT_PATH.$file_path);
        if(file_exists($file_path)){
            $str = file_get_contents($file_path); //将整个文件内容读入到一个字符串中
            return $str;
        }else{
            return '';
        }
    }

    // tp5.ccc/image/00001
     public function image($num=''){
        $file_path = '/upload/image/'.$num.".jpg";
        return request()->domain().$file_path;
    }

}
