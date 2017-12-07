<?php
namespace app\admin\validate;

use think\Validate;

class Goods extends Validate
{

    protected $rule =   [
        'goods_name'              => 'require',
        'goods_code'              => 'require',
        'cat_id'                  => 'number|gt:0',
        'shop_price'              => 'require',
    ];

    protected $message  =   [
        'goods_name.require'      => '商品名称不能为空',
        'goods_code.require'      => '商品编码不能为空',
        'cat_id.number'           => '选择分类错误',
        'cat_id.gt'               => '分类不能为空',
        'shop_price.require'      => '销售价不能为空',
    ];

    protected $scene = [
        'add'  => ['goods_name','goods_code', 'cat_id', 'shop_price'],
        'edit' => ['goods_name','goods_code', 'cat_id', 'shop_price'],
    ];
}


