<?php
namespace app\www\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 商品模型
 */
class Goods extends Base
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    //列表
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $request['map']['is_on_sale'] = 1;
        $data = $this->order('id desc')
                     ->alias('g')
                     ->field('g.*,c.name as cat_name')
                     ->join('__GOODS_CAT__ c','g.cat_id = c.id')
                     ->where( $request['map'] )
                     ->limit($request['offset'], $request['limit'])
                     ->select();
           
        return $this->_fmtData($data);           
    }

    //格式化数据
    private function _fmtData( $data )
    {
        if(empty($data) && is_array($data)) {
            return $data;
        }
        $cart_data = cookie('cart_data');

        foreach ($data as $key => $value) {
            $data[$key]['cart_num'] = isset($cart_data[$value['goods_code']]) && !empty($cart_data[$value['goods_code']]) ? $cart_data[$value['goods_code']] : 0; //购物车数量
            $data[$key]['img_100'] = $value['goods_img'] ? getImageThumb($value['goods_img']) : 'default_goods_img.jpg';
            $data[$key]['goods_img'] = $value['goods_img'] ? $value['goods_img'] : 'default_goods_img.jpg';
        }

        return $data;
    }

    //商品详情
    public function getDetail( $id )
    {
        $data = $this->field('id,goods_code,goods_name,goods_img,market_price,shop_price,goods_content')->where(['id'=>$id])->find();
        if( empty($data) ) {
            return false;
        }
        $cart_data = cookie('cart_data');
        $data['cart_num'] = isset($cart_data[$data['goods_code']]) && !empty($cart_data[$data['goods_code']]) ? $cart_data[$data['goods_code']] : 0; //购物车数量
        $data['goods_content'] = htmlspecialchars_decode($data['goods_content']);
        $data['goods_img'] = $data['goods_img'] ? getImageThumb($data['goods_img'],350) : 'default_goods_img.jpg';
        return $data;
    }

    //获取购物车数据
    public function getCartData()
    {

        $cart_data = cookie('cart_data');
        if( empty($cart_data) ) {
            return false;
        }
        $goods_code_arr = array();
        foreach((array)$cart_data as $key => $val) {
            $goods_code_arr[] = $key;
        }
        $map['is_on_sale'] = 1;
        $map['goods_code'] = ['in', $goods_code_arr];
        $goods_data = $this->field('id,goods_code,goods_name,goods_img,shop_price')->where($map)->select();
        if( empty($goods_data) ) {
            return false;
        }
        return $this->_fmtData($goods_data);
    }

    //计算购物车总价和数量
    public function cartTotalPrice()
    {
        $data = array();
        $data['total_num'] = 0;
        $data['total_price'] = 0.00;
        $cart_data = $this->getCartData();
        if(!$cart_data) {
            return $data;
        }
        foreach((array)$cart_data as $val) {
            $data['total_num'] += $val['cart_num']; //购物车商品总数
            $data['total_price'] += ncPriceCalculate($val['shop_price'], '*', $val['cart_num']); 

        }
        return $data;
    }

     


}
