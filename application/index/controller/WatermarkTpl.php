<?php
/**
 * Created by mac.
 * User: mac
 * Date: 2019/11/28
 * Time: 3:25 PM
 */

namespace app\index\controller;

use think\Controller;
use app\common\model\WatermarkTpl as mWatermarkTpl;
use app\common\model\Watermark  as mWatermark;

class WatermarkTpl extends Controller
{
    /**
     * 水印模板
     */
    public function tpl_list(){
        $mWatermarkTpl = new mWatermarkTpl;
        $list = $mWatermarkTpl->all();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 水印模板编辑
     * //  'type' => string '2' (length=1)
     * //  'vah' => string '1' (length=1)
     * //  'cols' => string '4' (length=1)
     * //  'pointxy' => string '520,200|425,280|350,280|290,280' (length=31)
     * //  'font_size' => string '77|40|50|39' (length=11)
     * //  'font_ttf' => string '经典繁毛楷|经典繁毛楷|经典繁毛楷|经典繁毛楷' (length=63)
     * //  'font_color' => string '#ff1493' (length=7)
     */
    public function tpl_edit($tpl_id=''){
        $mWatermarkTpl = new mWatermarkTpl;
        if(request()->isPost()){
            $data       = $_POST;
            $lecture_num = input('lecture_num');
            unset($data['lecture_num']);
            $filepath   = './upload/src/0001.jpg';
            $image = \think\Image::open($filepath);
            $filepath_water = './upload/water_tpl/'.$lecture_num.'.jpg';   // 以时间戳为文件名
            // 给原图左上角添加水印并保存water_image.png
            $pointxy_arr = explode('|',$data['pointxy']);
            foreach($pointxy_arr as $k=>$v){
                $wz[$k] = explode(',',$v);
            }
            $size_arr = explode('|',$data['font_size']);
            $ttf_arr = explode('|',$data['font_ttf']);
            $mWatermark = new mWatermark();
            $info = $mWatermark->where('num',$lecture_num)->find();
            $txt = [
                $info['title_new'],
                $info['author'],
                $info['speech_date_start_cn'],
                $info['adress']
            ];

            foreach($txt as $k=>$v){
                $new_txt[$k] = str_add_delimiter($v,'|');
            }
            $color = $data['font_color'];
            if($data['cols']==4){
                $image
                    ->text($new_txt[0],ttf_url($ttf_arr[0]),$size_arr[0],$color,$wz[0],0,0)
                    ->text($new_txt[1],ttf_url($ttf_arr[1]),$size_arr[1],$color,$wz[1],0,0)
                    ->text($new_txt[2],ttf_url($ttf_arr[2]),$size_arr[2],$color,$wz[2],0,0)
                    ->text($new_txt[3],ttf_url($ttf_arr[3]),$size_arr[3],$color,$wz[3],0,0)
                    ->save($filepath_water);
            }elseif($data['cols']==5){
                $image
                    ->text($new_txt[0],ttf_url($ttf_arr[0]),$size_arr[0],$color,$wz[0],0,0)
                    ->text($new_txt[1],ttf_url($ttf_arr[1]),$size_arr[1],$color,$wz[1],0,0)
                    ->text($new_txt[2],ttf_url($ttf_arr[2]),$size_arr[2],$color,$wz[2],0,0)
                    ->text($new_txt[3],ttf_url($ttf_arr[3]),$size_arr[3],$color,$wz[3],0,0)
                    ->text($new_txt[4],ttf_url($ttf_arr[4]),$size_arr[4],$color,$wz[4],0,0)
                    ->save($filepath_water);
            }else{
                $this->success('目前4列或5列');
            }

            // echo "<img src='/".$a."text_image.png'/>";
            // echo "<img src='/".$filepath_water."'/>";
            $data['img_view_url'] = trim($filepath_water,'.');
            $mWatermarkTpl->where('tpl_id',input('tpl_id'))->update($data);
            $this->success('保存成功');
        }
        $info = $mWatermarkTpl->get($tpl_id);
        $this->assign('info',$info);
        return $this->fetch();
    }
}