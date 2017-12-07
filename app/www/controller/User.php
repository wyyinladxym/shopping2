<?php
namespace app\www\controller;

use app\common\controller\Common;
use think\Controller;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;
use think\Config;

class User extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    //获取商品分类
    public function getGoodsCat()
    {	
    	return json(model('GoodsCat')->categoryTree());
    }

    

}