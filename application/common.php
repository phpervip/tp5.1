<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use Endroid\QrCode\QrCode;

/**
 * 将字符串分割为数组
 * @param  string $str 字符串
 * @return array       分割得到的数组
 */
function mb_str_split($str){
    return preg_split('/(?<!^)(?!$)/u', $str );
}

function str_add_delimiter($str,$delimiter='|'){
    $str_arr = mb_str_split($str);
    $new_str = implode($delimiter,$str_arr);
    return $new_str;
}

function dateToChinese($date,$type='Ym')
{
    $chineseDate = '';
    //$date = '2018-10-29'
    if (false == empty($date)) {
        $chineseArr = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');//把数字化为中文
        $chineseTenArr = array('', '十', '二十', '三十');//十位数对应中文
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $day = date('d', strtotime($date));
        //转换为数组
        $yearArr = str_split($year);
        foreach ($yearArr as $value) {
            $chineseDate .= $chineseArr[$value];
        }
        $chineseDate .= '年';


        $monthArr = str_split($month);
        //月，日去除零
        if ($monthArr[1] != 0) {
            $chineseDate .= $chineseTenArr[$monthArr[0]] . $chineseArr[$monthArr[1]] . '月';
        } else {
            $chineseDate .= $chineseTenArr[$monthArr[0]] . '月';
        }

        if($type=='Ymd'){
            $dayArr = str_split($day);
            if ($dayArr[1] != 0) {
                $chineseDate .= $chineseTenArr[$dayArr[0]] . $chineseArr[$dayArr[1]] . '日';
            } else {
                $chineseDate .= $chineseTenArr[$dayArr[0]] . '日';
            }
        }
    }
    return $chineseDate;
}

function yearToChinese($date)
{
    $chineseDate = '';
    // $date = '2018'
    if (false == empty($date)) {
        $chineseArr = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');//把数字化为中文
        $year = date('Y', strtotime($date));
        //转换为数组
        $yearArr = str_split($year);
        foreach ($yearArr as $value) {
            $chineseDate .= $chineseArr[$value];
        }
        $chineseDate .= '年';
    }
    return $chineseDate;
}

//第一个是原串,第二个是 部份串
function startWith($str, $needle) {

    return strpos($str, $needle) === 0;

}

//第一个是原串,第二个是 部份串
function endWith($haystack, $needle) {

    $length = strlen($needle);
    if($length == 0)
    {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}


function ttf_url($fileName,$url='./upload/ziti/'){
    return $url.$fileName.'.ttf';
}

/**
 * 产生验证码
 *
 * @param string $nchash 哈希数
 * @return string
 */
function makeSeccode($nchash){
    $seccode = random(6, 1);
    $seccodeunits = '';

    $s = sprintf('%04s', base_convert($seccode, 10, 23));
    $seccodeunits = 'ABCEFGHJKMPRTVXY2346789';
    if($seccodeunits) {
        $seccode = '';
        for($i = 0; $i < 4; $i++) {
            $unit = ord($s{$i});
            $seccode .= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
        }
    }
    // setNcCookie('seccode'.$nchash, encrypt(strtoupper($seccode)."\t".(time())."\t".$nchash,MD5_KEY),3600);
    return $seccode;
}

/**
 * 取得随机数
 *
 * @param int $length 生成随机数的长度
 * @param int $numeric 是否只产生数字随机数 1是0否
 * @return string
 */
function random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

/**
 * 调用phpqrcode生成二维码
 * @param string $url 二维码解析的地址
 * @param int $level  二维码容错级别
 * @param int $size   二维码大小
 * @param int $label  二维码文字
 * @param int $fontsize label字体大小
 */
function qrcode($url="http://www.baidu.com",$level='high',$size=150,$label='',$fontsize=16,$name='png')
{
    $qrCode = new QrCode();
    $qrCode->setText($url)
        ->setSize($size)  // 大小
        ->setLabelFontPath(VENDOR_PATH.'endroid/qrcode/assets/noto_sans.otf')
        ->setErrorCorrectionLevel($level)
        ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
        ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
        ->setLabel($label)
        ->setLabelFontSize($fontsize)
        ->setWriterByName($name);
    header('Content-Type: '.$qrCode->getContentType());
    echo $qrCode->writeString();
    exit;
}




