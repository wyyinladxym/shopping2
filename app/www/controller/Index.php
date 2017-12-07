<?php
namespace app\www\controller;

use app\common\controller\Common;
use think\Controller;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;
use think\Config;

class Index extends Base
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

    //获取商品
    public function getGoods()
    {
    	$request = request()->param();
    	if(isset($request['cat_id']) && !$request['cat_id']) {
    		unset($request['cat_id']);
    	}
    	return model('Goods')->getList( $request );
    }

    //加入购物车
    public function addCart()
    {
    	$num 		= input('post.num');
    	$goods_code = input('post.goods_code');
    	$type = input('post.type');
    	$cart_data  = cookie('cart_data');
    	$cart_data  = empty($cart_data) ? array() : $cart_data;
    	if(!$type) {
    		$cart_data[$goods_code] = isset($cart_data[$goods_code]) && !empty($cart_data[$goods_code]) ? $cart_data[$goods_code] + $num : $num;
    	}else{
    		$cart_data[$goods_code] = (int)$num;
    	}
    	if( $cart_data[$goods_code] <= 0 ) {
    		unset($cart_data[$goods_code]);
    	}
    	cookie('cart_data', $cart_data);

    	return isset($cart_data[$goods_code]) && !empty($cart_data[$goods_code]) ? $cart_data[$goods_code] : 0;
    }

    //获取商品详情
    public function goodsDetail()
    {
    	$id = input('id', '', 'intval');
    	return model('Goods')->getDetail( $id );
    }

    //获取购物车列表
    public function getCartList()
    {
    	return model('Goods')->getCartData();	
    }

    //计算购物车总数及商品数量
    public function cartTotalPrice()
    {
    	return model('Goods')->cartTotalPrice();
    }

    //清空购物车数据
    public function delCart()
    {
    	cookie('cart_data', null);
    	return true;
    }

}