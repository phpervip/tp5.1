<?php
/**
 * Created by mac.
 * User: mac
 * Date: 2019/11/28
 * Time: 9:30 AM
 */

namespace app\index\controller;

use think\Controller;
use app\common\model\Watermark as mWatermark;

class Watermark extends Controller
{


    /**
     * 水印文字列表
     */
    public function index(){

        $pageParam  = ['query'=>[]];
        $mWatermark = new mWatermark();
        $list = $mWatermark->paginate(20,false,$pageParam);
        $this->assign('list',$list);

        $builder = new \JKBuildHtml\Builder();
        $builder->buildAll();
        return $this->fetch();
    }


        /**
         * 水印文字列表
         */
        public function wm_list(){

            $pageParam  = ['query'=>[]];
            $mWatermark = new mWatermark();
            $list = $mWatermark->paginate(20,false,$pageParam);
            $this->assign('list',$list);

            $builder = new \JKBuildHtml\Builder();
            $builder->buildAll();
            return $this->fetch();
        }

        /**
         * 批量转换阿拉伯数字的日期为中文数字日期
         */
        public function createdatecn(){
            // https://blog.csdn.net/qq_38906555/article/details/83507006
            // 1。先分隔，开始时间，结束时间
            // set sql_safe_updates=0;
            // update lecture.jz_hz_video_album set speech_date_start=substring_index(speech_date,'-',1), speech_date_end=substring_index(speech_date,'-','-1') where speech_date is not null;
            // update lecture.jz_hz_video_album set speech_date_end='' where speech_date_start=speech_date_end;
            // UPDATE `jz_hz_video_album` SET `speech_date_start_b` = replace (`speech_date_start`,'.','-') WHERE `speech_date_start` is not null;
            // 2。再转换。
            // 3。目前有这些情况
            // 9.23
            // 1984
            // 1978.2
            // 1984.12
            // 1994.6.4
            // 1993.10
            // 1994.8.9
            // 1998.4.4
            // 早期
            // 新字段为, speech_date_start_cn
            $mWatermark = new mWatermark();
            $list = $mWatermark->where('speech_date_start_b','<>','')->all();
            $update = []; // 更新的数据集
            foreach($list as $k=>$v){
                $update[$k]['id'] = $v['id'];
                $speech_date_start_b = $v['speech_date_start_b'];  // 1998-4-4
                $length = strlen((string)($speech_date_start_b));
                if($speech_date_start_b!='早期'){
                    // 进行处理。
                    if($length>4){
                        $speech_date_start_cn = dateToChinese($speech_date_start_b,'Ym');
                    }else{
                        $speech_date_start_cn = yearToChinese($speech_date_start_b);
                    }
                    $update[$k]['speech_date_start_cn'] = $speech_date_start_cn;
                }else{
                    // 不处理
                    $update[$k]['speech_date_start_cn'] = '早期';
                }
            }
            $res = $mWatermark->saveAll($update);
            var_dump(count($res));   // 更新的记录数。 2629,  全部有2984
        }

            protected function fetchHtml()
            {
                $builder = new \JKBuildHtml\Builder();
                $builder->buildFromFetch( $html = $this->fetch(), input('get.') );
                return $html;
            }





}