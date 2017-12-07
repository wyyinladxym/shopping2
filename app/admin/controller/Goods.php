<?php
namespace app\admin\controller;

use think\Session;
use think\Request;
use think\Loader;
use think\Db;

/**
* 商品管理
*/
class Goods extends Admin
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
        $data = model('Goods')->getList( $request );
        return $data;
    }

    public function add()
    {
        $this->assign('select_str',model('GoodsCat')->tagCategory(0,'cat_id','请选择分类'));
        return $this->fetch('edit');
    }

    public function edit($id = 0)
    {
        $id = input('id', '', 'intval');
        $data = model('Goods')->get(['id'=>$id]);
        $data['goods_content'] = htmlspecialchars_decode($data['goods_content']);
        $this->assign('data', $data);
        $this->assign('select_str',model('GoodsCat')->tagCategory($data['cat_id'],'cat_id','请选择分类'));
        return $this->fetch();
    }

    public function saveData()
    {
        if( !request()->isAjax() ) {
            $this->error(lang('Request type error'));
        }
        $data = input('post.');
        return model('Goods')->saveData( $data );
    }

    //商品主图上传
    public function ajaxfileupload()
    {
        $file = request()->file('up_goods_img');
        $res = ajaxFileUpload($file);
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
        return model('Goods')->deleteById($id);
    }

}