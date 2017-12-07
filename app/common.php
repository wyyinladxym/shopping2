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


/**
 * 调试输出
 * @param unknown $data
 */
function print_data($data, $var_dump = false)
{
    header("Content-type: text/html; charset=utf-8");
    echo "<pre>";
    if ($var_dump) {
        var_dump($data);
    } else {
        print_r($data);
    }
    exit();
}

/**
 * 输出json格式数据
 * @param unknown $object
 */
function print_json($object)
{
    header("content-type:text/plan;charset=utf-8");
    echo json_encode($object);
    exit();
}

/**
 * 账户密码加密
 * @param  string $str password
 * @return string(32)       
 */
function md6($str)
{
	$key = 'account_nobody';
	return md5(md5($str).$key);
}

/**
 * 替换字符串中间位置字符为星号
 * @param  [type] $str [description]
 * @return [type] [description]
 */
function replaceToStar($str)
{
    $len = strlen($str) / 2; //a0dca4d0****************ba444758]
    return substr_replace($str, str_repeat('*', $len), floor(($len) / 2), $len);
}

function mduser( $str )
{
    $user_auth_key = \think\Config::get('user_auth_key');
    return md5(md5($user_auth_key).$str);
}



/**
 * 
 */

function ajaxFileUpload($file = false, $dirname = 'goods_img', $thumb = true, $vali_arr = ['size'=>1048576,'ext'=>'jpg,png,gif,jpeg'])
{
    $res_data = array();
    $res_data['error'] = 1;
    $res_data['msg'] = lang('Save failed');
    $upload_path = ROOT_PATH . 'public/static/uploads/' . $dirname . '/';
    if($file) {
        $info = $file->validate($vali_arr)->move($upload_path);
        if($info) {
            // 成功上传后 获取上传信息
            $saveName = str_replace('\\', '/', $info->getSaveName());
            $abs_path = $upload_path . $saveName;
            $res_data['error'] = 0;
            $res_data['data']['original_img'] = $dirname . '/' .$saveName;
            $res_data['msg'] = lang('Save success');

            if($thumb) {
                $image = \think\Image::open($abs_path);
                $pathinfo = pathinfo($abs_path);
                $imgname = basename($pathinfo['basename'], '.'.$pathinfo['extension']);
                $datedir_350 =  $dirname . '/' . date('Ymd',time()) . '/' . $imgname . '_350' . '.' . $pathinfo['extension'];
                $datedir_100 =  $dirname . '/' . date('Ymd',time()) . '/' . $imgname . '_100' . '.' . $pathinfo['extension'];
                $thumb_350_path = $pathinfo['dirname'] . '/' . $imgname . '_350' . '.' . $pathinfo['extension'];
                $thumb_100_path = $pathinfo['dirname'] . '/' . $imgname . '_100' . '.' . $pathinfo['extension'];
                if($thumb === 100) {
                    $image->thumb(100, 100)->save($thumb_100_path);
                    $res_data['data']['thumb100_img'] = $datedir_100;
                }
                if($thumb === 350) {
                    $image->thumb(350, 350)->save($thumb_350_path);
                    $res_data['data']['thumb350_img'] = $datedir_350;
                }
                if($thumb === true) {
                    $image->thumb(100, 100)->save($thumb_100_path);
                    $image->thumb(350, 350)->save($thumb_350_path);
                    $res_data['data']['thumb100_img'] = $datedir_100;
                    $res_data['data']['thumb350_img'] = $datedir_350;
                }
            }

        }else {
            // 上传失败获取错误信息
            $res_data['msg'] = $file->getError();
        }
    }
    return $res_data;
}

//获取缩略图
function getImageThumb($img, $size = 100)
{   
    if( empty($img) && ($size !== 100 && $size !== 350) ) {
        return false;
    }
    $pathinfo = pathinfo($img);
    $imgname = basename($pathinfo['basename'], '.'.$pathinfo['extension']);
    return $pathinfo['dirname'] . '/' . $imgname . '_' . $size . '.' . $pathinfo['extension'];
}

/**
 * PHP精确计算  主要用于货币的计算用
 * @param $n1 第一个数
 * @param $symbol 计算符号 + - * / %
 * @param $n2 第二个数
 * @param string $scale  精度 默认为小数点后两位
 * @return  string
 */
function ncPriceCalculate($n1,$symbol,$n2,$scale = '2'){
    $res = "";
    switch ($symbol){
        case "+"://加法
            $res = bcadd($n1,$n2,$scale);break;
        case "-"://减法
            $res = bcsub($n1,$n2,$scale);break;
        case "*"://乘法
            $res = bcmul($n1,$n2,$scale);break;
        case "/"://除法
            $res = bcdiv($n1,$n2,$scale);break;
        case "%"://求余、取模
            $res = bcmod($n1,$n2,$scale);break;
        default:
            $res = "";break;
    }
    return $res;
}
/**
 * 价格由元转分
 * @param $price 金额
 * @return int
 */
function ncPriceYuan2fen($price){
    $price = (int) ncPriceCalculate(100,"*", ncPriceFormat($price));
    return $price;
}
/**
* 价格格式化
*
* @param int    $price
* @return string    $price_format
*/
function ncPriceFormat($price) {
    $price_format   = number_format($price,2,'.','');
    return $price_format;
}

