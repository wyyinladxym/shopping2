<?php
namespace app\admin\controller;

use think\Controller;
use think\Loader;

/**
 * 支付管理
 */
class Payment extends Admin
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
            $request['pay_name|pay_code'] = ['like','%'.$request['search'].'%'];
           
        }
        unset($request['search']);
		$data = model('Payment')->getList( $request );
		return $data;
	}

    public function payConfig()
    {   
        if( request()->isPost() ) {
            $data = input('post.');
            return model('Payment')->saveConfig( $data );
        }
        $id = input('id', '', 'intval');
        $config = input('config', '', 'string');
        $data = model('Payment')->where(['id'=>$id])->value('pay_config');
        $data = json_decode($data,true);
        $data['id'] = $id;
        $this->assign('data', $data);
        return $this->fetch(strtolower($config));
    }

	public function add()
    {
        return $this->fetch('edit');
    }


    public function edit($id = 0)
    {
        $id = input('id', '', 'intval');
        $data = model('Payment')->get(['id'=>$id]);
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function saveData()
    {
        if( !request()->isAjax() ) {
            $this->error(lang('Request type error'));
        }
        $data = input('post.');
        return model('Payment')->saveData( $data );
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
        return model('Payment')->deleteById($id);
    }
}