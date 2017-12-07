<?php
namespace app\admin\controller;

use think\Session;
use think\Request;
use think\Loader;
use think\Db;

/**
* 商品分类管理
*/
class Goodscat extends Admin
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

    /**
     * 异步获取列表数据
     * @author xym
     * @return mixed
     */
    public function getList()
    {
        if(!request()->isAjax()) {
            $this->error(lang('Request type error'), 4001);
        }

        $request = request()->param();
        if(isset($request['search']) && $request['search'] != '') {
            $request['name'] = ['like','%'.$request['search'].'%'];
        }
        unset($request['search']);
        $data = model('GoodsCat')->getList( $request );
        return $data;
    }

    /**
     * 添加
     */
    public function add()
    {
        $this->assign('cate_str', model('GoodsCat')->tagCategory());
        return $this->fetch('edit');
    }

    /**
     * 编辑
     * @param  string $id 数据ID（主键）
     */
    public function edit($id = 0)
    {
        $id = input('id', '', 'intval');
        $data = model('GoodsCat')->get(['id'=>$id]);
        $this->assign('data', $data);
        $this->assign('cate_str', model('GoodsCat')->tagCategory($data['parent_id']));
        return $this->fetch();
    }

    /**
     * 保存数据
     * @param array $data
     *
     * @author xym
     */
    public function saveData()
    {
        if( !request()->isAjax() ) {
            $this->error(lang('Request type error'));
        }
        $data = input('post.');
        return model('GoodsCat')->saveData( $data );
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
        return model('GoodsCat')->deleteById($id);
    }

}