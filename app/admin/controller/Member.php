<?php
namespace app\admin\controller;

use think\Session;
use think\Request;
use think\Loader;
use think\Db;

/**
 *  会员管理
 */
class Member extends Admin
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
        $data = model('Member')->getList( $request );
        return $data;
    }

    public function add()
    {
        $this->assign('group_data', model('MemberGroup')->select());
        $this->assign('status_data', model('Member')->getStatus());
        $this->assign('sales_data', model('Salesman')->select());
        return $this->fetch('edit');
    }


    public function edit($id = 0)
    {
        $id = input('id', '', 'intval');
        $data = model('Member')->get(['id'=>$id]);
        $data['headpic_100'] = getImageThumb($data['headpic']);
        $this->assign('data', $data);
        $this->assign('group_data', model('MemberGroup')->select());
        $this->assign('status_data', model('Member')->getStatus());
        $this->assign('sales_data', model('Salesman')->select());
        return $this->fetch();
    }

    public function saveData()
    {
        if( !request()->isAjax() ) {
            $this->error(lang('Request type error'));
        }
        $data = input('post.');
        return model('Member')->saveData( $data );
    }

    //会员头像上传
    public function ajaxfileupload()
    {
        $file = request()->file('up_headpic');
        $res = ajaxFileUpload($file, 'member_headpic', 100);
        return json($res);
    }

    /**
     * 删除
     * @param  string $id 数据ID（主键）
     */
    public function delete($id = 0)
    {
        if(empty($id)){
            return info(lang('Data ID exception'), 0);
        }
        return model('Member')->deleteById($id);
    }

}