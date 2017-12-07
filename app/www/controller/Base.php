<?php
namespace app\www\controller;

use app\common\controller\Common;
use think\Controller;
use think\Loader;
use think\Session;
use think\Request;
use think\Url;

class Base extends Common
{
    protected $uid = 0;
	protected $role_id = 0;

	function _initialize()
	{
		parent::_initialize();
		//判断是否已经登录

		// if( !Session::has('userinfo', 'www') ) {
		// 	$this->error('Please login first', url('admin/Login/index'));
		// }
		// $userRow = Session::get('userinfo', 'admin');
		// //验证权限
		// $request = Request::instance();
		// $rule_val = $request->module().'/'.$request->controller().'/'.$request->action();
		// $this->uid = $userRow['id'];
		// $this->role_id = $userRow['role_id'];
		// if($userRow['administrator']!=1 && !$this->checkRule($this->uid, $rule_val)) {
		// 	$this->error(lang('Without the permissions page'));
		// }
	}
}