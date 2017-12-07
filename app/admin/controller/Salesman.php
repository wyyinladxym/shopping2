<?php
namespace app\admin\controller;

use think\Controller;
use think\Loader;

/**
 * 业务员管理
 */
class Salesman extends Admin
{
	
	public function index()
	{
		return view();
	}

	
	//异步获取列表数据
	public function getList()
	{
		if(!request()->isAjax()) {
			$this->error(lang('Request type error'), 4001);
		}
		$request = input('get.');
        if(isset($request['search']) && $request['search'] != '') {
            $request['s.name|s.mobile'] = ['like','%'.$request['search'].'%'];
           
        }
        unset($request['search']);
		$data = model('Salesman')->getList( $request );
		return $data;
	}

	public function add()
    {
        $this->assign('depart_data', model('Department')->select());
        return $this->fetch('edit');
    }


    public function edit($id = 0)
    {
        $id = input('id', '', 'intval');
        $data = model('Salesman')->get(['id'=>$id]);
        $data['headpic_100'] = getImageThumb($data['headpic']);
        $this->assign('depart_data', model('Department')->select());
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function saveData()
    {
        if( !request()->isAjax() ) {
            $this->error(lang('Request type error'));
        }
        $data = input('post.');
        return model('Salesman')->saveData( $data );
    }

    //业务员头像上传
    public function ajaxfileupload()
    {
        $file = request()->file('up_headpic');
        $res = ajaxFileUpload($file, 'salesman_headpic', 100);
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
        return model('Salesman')->deleteById($id);
    }
}