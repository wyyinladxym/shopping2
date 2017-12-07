<?php
namespace app\www\model;

use app\common\model\Common;
use think\Db;
use think\Model;

/**
 * 前台model基础类
 *
 * Class Admin
 * @package app\admin\model
 */
class Base extends Common
{
    //格式化请求参数
    protected function fmtRequest( $request = [] )
    {
        if( empty($request) ) {
            return ['map'=>[]];
        }
        $limit = 10;
        if (isset($request['limit']) && is_numeric($request['limit']) ) {
            $limit = $request['limit'];
            unset($request['limit']);
        }
        $offset = 0;
        if (isset($request['p']) && is_numeric($request['p']) ) {
            $offset = $request['p'];
            unset($request['p']);
        }
        
        $ret = [
            'offset'=>$offset,
            'limit'=>$limit,
            'map'=>$request
        ];
        return $ret;
    }
}
