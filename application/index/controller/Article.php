<?php
/**
 * Created by mac.
 * User: mac
 * Date: 2019/12/1
 * Time: 9:37 AM
 */

namespace app\index\controller;

use app\common\model\Article as mArticle;
class Article
{
    public function article_list(){

    }

    public function article_edit(){

    }

    public function article_view($id){
        $mArticle = new mArticle();
        $info = $mArticle::get($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
}