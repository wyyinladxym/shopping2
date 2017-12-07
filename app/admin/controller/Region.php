<?php
namespace app\admin\controller;

use think\Controller;
use think\Loader;

/**
 * 地区管理
 */
class Region extends Admin
{
	public function index()
	{
		return view();
	}

	// 异步获取列表数据
	public function getList()
	{
		if(!request()->isAjax()) {
			$this->error(lang('Request type error'), 4001);
		}
		$request = input('get.');
		if(isset($request['search']) && $request['search'] != '') {
            $request['region_name|id'] = ['like','%'.$request['search'].'%'];
           
        }
        unset($request['search']);
		$data = model('Region')->getList( $request );
		return $data;
	}

	public function add()
    {
        return $this->fetch('edit');
    }


    public function edit($id = 0)
    {
        $id = input('id', '', 'intval');
        $data = model('Region')->get(['id'=>$id]);
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function saveData()
    {
        if( !request()->isAjax() ) {
            $this->error(lang('Request type error'));
        }
        $data = input('post.');
        return model('Region')->saveData( $data );
    }

    /**
     * 删除
     * @param  string $id 数据ID（主键）
     */
    public function delete($id = 0)
    {
        if(empty($id)) {
            return info(lang('Data ID exception'), 0);
        }
        return model('Region')->deleteById($id);
    }
}