<?php
namespace app\admin\controller;

use think\Session;
use think\Request;
use think\Loader;
use think\Db;

/**
* 系统日志
*/
class Logrecord extends Admin
{
    function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 列表
     */
    public function index()
    {
        return view();
    }

    public function getList()
    {
        if(!request()->isAjax()) {
            $this->error(lang('Request type error'), 4001);
        }

        $request = request()->param();
        //return $request['pageSize'];
        $data = model('LogRecord')->getList( $request );
        return $data;
    }

    /**
     * 删除
     * @param  string $id 数据ID（主键）
     */
    public function delete($id = 0){
        
        if(empty($id)){
            return info(lang('Data ID exception'), 0);
        }
        return model('LogRecord')->deleteById($id);
    }
}