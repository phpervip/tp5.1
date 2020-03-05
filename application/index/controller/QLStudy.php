<?php
/**
 * Created by mac.
 * User: mac
 * Date: 2019/11/30
 * Time: 11:16 AM
 */

namespace app\index\controller;
use GuzzleHttp\Client;
use QL\QueryList;
use think\Controller;
use app\common\model\Article as mArticle;
use QL\Ext\AbsoluteUrl;
use QL\Ext\CurlMulti;
use GuzzleHttp\Exception\RequestException;

class QLStudy extends Controller
{

    // 来源页面：http://querylist.cc/

    // 看看PHP用QueryList做采集到底有多简洁吧!
    public function caiji(){
        //采集某页面所有的图片
        $data = QueryList::get('http://cms.querylist.cc/bizhi/450.html')->find('img')->attrs('src');
        // var_dump($data->all());
        //采集某页面所有的超链接和超链接文本内容
        //可以先手动获取要采集的页面源码
        $html = file_get_contents('http://cms.querylist.cc/google/list_1.html');
        //然后可以把页面源码或者HTML片段传给QueryList
        $data = QueryList::html($html)->rules([
            // 采集所有a标签的href属性
            'link'=>['a','href'],
            // 采集所有a标签的文本内容
            'text'=>['a','text']
        ])->query()->getData();
        // var_dump($data->all());
    }


    // 上面的采集结果有很多“杂质”，一定不会满足你的要求，来获取我们真正想要的结果。
    public function caiji2(){
        // 采集该页面[正文内容]中所有的图片
        $data = QueryList::get('http://cms.querylist.cc/bizhi/450.html')->find('.post_content img')->attrs('src');
        // var_dump($data->all());
        // 采集该页面文章列表中所有[文章]的超链接和超链接文本内容
        $data = QueryList::get('http://cms.querylist.cc/google/list_1.html')->rules([
            'link'=>['h2>a','href','',function($content){
                //利用回调函数补全相对链接
                $baseUrl = 'http://cms.querylist.cc';
                return $baseUrl.$content;
            }],
            'text'=>['h2>a','text']
        ])->range('.cate_list li')->query()->getData();
        //打印结果
        var_dump($data->all());
    }


    /**
     * 下面来完整的演示采集一篇文章页的文章标题、发布日期和文章内容并实现图片本地化
     */
    public function caiji3(){
        // 需要采集的目标页面
        $page = 'http://cms.querylist.cc/news/566.html';
        // 采集规则

        $reg  = [
            //采集文章标题
            'title'=>['h1','text'],
            //采集文章发布日期,这里用到了QueryList的过滤功能，过滤掉span标签和a标签
            'date'=>['.pt_info','text','-span -a',function($content){
                $arr = explode(' ',$content);
                return $arr[0];
            }],
            //采集文章正文内容,利用过滤功能去掉文章中的超链接，但保留超链接的文字，并去掉版权、JS代码等无用信息
            'content' => ['.post_content','html','a -.content_copyright -script']

        ];

        $rang = '.content';
        $ql = QueryList::get($page)->rules($reg)->range($rang)->query();

        $data = $ql->getData(function($item){
            //利用回调函数下载文章中的图片并替换图片路径为本地路径
            //使用本例请确保当前目录下有image文件夹，并有写入权限
            $content = QueryList::html($item['content']);
            $content->find('img')->map(function($img){
                $src = 'http://cms.querylist.cc'.$img->src;
                $localSrc = 'upload/image/'.md5($src).'.jpg';
                $stream = file_get_contents($src);
                file_put_contents($localSrc,$stream);
                $img->attr('src','/'.$localSrc);
            });

            $item['content'] = $content->find('')->html();
            return $item;
        });

        //打印结果
        // var_dump($data->all());

        $mArticle = new mArticle();
        $mArticle->insertAll($data->all());

        echo '完成!';

    }



    /**
     * 下面来利用QueryList插件来组合上面的例子，实现多线程采集文章
     */
    public function caiji4(){
        // 注册插件
        $ql = QueryList::use([
            AbsoluteUrl::class,  // 转换URL相对路径到绝对路径
            CurlMulti::class     // Curl多线程采集
        ]);

        // 获取文章列表链接集合，使用AbsoluteUrl插件转换URL相对路径到绝对路径
        $urls = $ql->get('http://cms.querylist.cc/news/list_2.html',[
            'param1'=>'testvalue',
            'param2'=>'somevalue'

        ],[
            'headers'=>[
                'Referer'=>'https://querylist.cc/',
                'User-Agent'=>'testing/1.0',
                'Accept'=>'application/json',
                'X-Foo'=>['Bar','Baz'],
                'Cookie'=>'abc=111;xxx=222'
            ]

        ])->absoluteUrl('http://cms.querylist.cc/news/list_2.html')->find('h2>a')->attrs('href')->all();

        // 使用CurlMulti多线程插件采集文档内容
        $ql->rules([  // 设置采集规则
            'title' => ['h1','text'],
            'date'  => ['.pt_info','text','-span -a',function($content){
                // 用回调函数进一步过滤日期
                $arr = explode(' ',$content);
                return $arr[0];
            }],
            'content'=>['.post_content','html','a -.content_copyright -script']
        ])
        // 设置采集任务
        ->curlMulti($urls)
        // 每个任务成功完成调用此回调
        ->success(function(QueryList $ql, CurlMulti $curl, $r){
            echo "Content url:{$r['info']['url']} \r\n";
            $data = $ql->range('.content')->query()->getData();
           // var_dump($data->all());
            $mArticle = new mArticle();
            $mArticle->insertAll($data->all());
        })
        // 开始执行多线程采集
        ->start([
            // 最大并发数
            'maxThread' => 10,
            // 错误重试次数
            'maxTry'    => 3
        ]);
    }

    // 采集百度搜索结果列表的标题和链接。
    public function caiji5(){

        $data = QueryList::get('https://www.baidu.com/s?wd=QueryList')
            ->rules([
                'title'=>array('h3','text'),
                'link' => array('h3>a','href')
            ])
            ->queryData();

        /// 为何写两遍才会用
        $data = QueryList::get('https://www.baidu.com/s?wd=QueryList')
            // 设置采集规则
            ->rules([
                'title'=>array('h3','text'),
                'link'=>array('h3>a','href')
            ])
            ->queryData();

       // var_dump($data);

        $data = QueryList::get('https://www.liaotuo.com/fojiaogushi/fojing/')
            ->rules([
                'title'=>array('.B4_box>b>a','title'),
                'link'=>array('.B4_box>b>a','href')
            ])
            ->encoding('UTF-8')
            ->queryData();
        // 空的 中文乱码
        var_dump($data);


    }

    public function caiji6(){
        $ql = QueryList::get('https://www.baidu.com/s?wd=QueryList');
        $ql = QueryList::get('https://www.baidu.com/s?wd=QueryList');
        $titles = $ql->find('h3>a')->texts(); //获取搜索结果标题列表
        $links = $ql->find('h3>a')->attrs('href');//获取搜索结果链接列表
       // var_dump($titles);
       // var_dump($links);
    }

    public function caiji7(){
        $client = new Client();
        $res = $client->request('GET','https://www.liaotuo.com/fojiaogushi/fojing/',[]);
        $html = (string)$res->getBody();
        $data = QueryList::html($html)->encoding('UTF-8')->find('.B4_box>b>a')->attrs('title');
        var_dump($data);
    }


    public function caiji8(){
        // post操作和get操作是cookie共享的,意味着你可以先调用post()方法登录，然后get()方法就可以采集所有登录后的页面。
        $ql = QueryList::post('http://xxx.com/admin',[
            'username'=>'admin',
            'password'=>'123456'
        ])->get('http://xxx.com/admin/index/index');

        // echo $ql->getHtml();

        $ql->get('http://xxx.com/admin/index/dev_log');

        // 获取抓取到的HTML
        echo $ql->getHtml();
    }

    /**
     * 捕获HTTP异常
     */
    public function caiji9(){
        // 捕获HTTP异常
        try{
            $ql = QueryList::get('https://www.sfasd34234324.com');
        }catch(RequestException $e){
            // var_dump($e->getRequest());
            echo 'Http Error';
        }

    }

    /**
     * 获取单个元素的单个属性
     */
    public function caiji10(){
        $html = <<<STR
<div id="one">
    <div class="two">
        <a href="http://querylist.cc">QueryList官网</a>
        <img src="http://querylist.com/1.jpg" alt="这是图片" abc="这是一个自定义属性">
        <img class="second_pic" src="http://querylist.com/2.jpg" alt="这是图片2">
        <a href="http://doc.querylist.cc">QueryList文档</a>
    </div>
    <span>其它的<b>一些</b>文本</span>
</div> 
STR;
        $ql = QueryList::html($html);
        // var_dump($ql->getHtml());
        $rt = [];

        // 获取第一张图片的链接地址
        $rt[] = $ql->find('img')->attr('src');
        $rt[] = $ql->find('img')->src;
        $rt[] = $ql->find('img:eq(0)')->src;
        $rt[] = $ql->find('img')->eq(0)->src;

        // 获取第一张图片的alt属性
        $rt[] = $ql->find('img')->alt;
        // 获取第一张图片的abc属性
        $rt[] = $ql->find('img')->abc;
        // var_dump($rt);

        // 获取第二张图片的链接地址
        $rt[] = $ql->find('img')->eq(1)->alt;
        $rt[] = $ql->find('img:eq(1)')->alt;
        $rt[] = $ql->find('.second_pic')->alt;

        // 获取元素的所有属性
        //
        //属性匹配支持通配符*,表示匹配当前元素的所有属性。
        $rt[] = $ql->find('img')->eq(0)->attr('*');
        $rt[] = $ql->find('a:eq(1)')->attr('*');


        // 获取元素内的html内容或text内容
        //
        $rt = [];
        //text内容与html内容的区别是，text内容中去掉了所有html标签，只剩下纯文本。
        $rt[] = $ql->find('#one>.two')->html();
        $rt[] = $ql->find('.two')->text();

        var_dump($rt);

        // 获取多个元素的单个属性
        //
        //map()方法用于遍历多个元素的集合，find()方法返回的其实是多个元素的集合，这一点与jQuery也是一致的。
        $data1  = $ql->find('.two img')->map(function($item){
            return $item->alt;
        });

        var_dump($data1->all());

        // 获取选中元素的所有html内容和text内容
        $texts = $ql->find('.two>a')->texts();
        $htmls = $ql->find('#one span')->htmls();

        var_dump($texts->all());
        var_dump($htmls->all());

    }

    // 实战 - 采集IT之家文章页
    public function caiji11(){
        $ql = QueryList::get('https://lapin.ithome.com/html/digi/460252.htm');

        $rt = [];

        $rt['title'] = $ql->find('.post_title>h1')->text();

        $rt['author'] = $ql->find('#author_baidu>strong')->text();

        $rt['content'] = $ql->find('.post_content')->html();

        var_dump($rt);
    }


    // 下面学习如何采集列表, 简例
    public function caiji12(){
        $url= 'https://lapin.ithome.com/html/digi/460252.htm';
        $rules = [
            'title'=>['.post_title>h1','text'],
            'author'=>['#author_baidu>strong','text'],
            'content'=>['.post_content','text'],
        ];

        $rt = QueryList::get($url)->rules($rules)->query()->getData();

        var_dump($rt);
    }


    //
    public function caiji13(){
        // 如图，利用浏览器的开发者工具可以很容易分析出切片选择器为：.ulcl>li,
        // 然后我们需要在这每个切片区域中去采集文章的标题、文章链接、简介以及缩略图，
        // 利用同样的方式分析出每个元素的选择器，这里不再赘述，最终列表采集代码为：

        $url= 'https://it.ithome.com/chuangye/';
        $rules = [
            // 采集文章标题
            'title'=>['h2>a','text'],
            // 采集链接
            'link' =>['h2>a','href'],
            // 采集缩略图
            'img'  =>['.list_thumbnail>img','data-original'],
            // 采集文档简介
            'memo' =>['.memo','text'],
        ];
        // 我们称之为切片选择器或范围选择器，也就是range 。
        // 切片选择器,跳过第一条广告
        $range = '.ulcl>gt(0)';
        $rt = QueryList::get($url)->rules($rules)->range($range)->query()->getData();

        var_dump($rt);
    }

    /**
     * 内容过滤
     */
    public function caiji14(){

    }



}