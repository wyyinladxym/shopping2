<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 商品管理
 */
class Goods extends Admin
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //列表
    public function getList( $request )
    {
       $request = $this->fmtRequest( $request );

        $data = $this->order('id desc')
                     ->alias('g')
                     ->field('g.*,c.name as cat_name')
                     ->join('__GOODS_CAT__ c','g.cat_id = c.id')
                     ->where( $request['map'] )
                     ->limit($request['offset'], $request['limit'])
                     ->select();
        $total_page = $this->where( $request['map'] )->count();     
        return array('total'=>$total_page,'rows'=>$this->_fmtData($data));                    
    }

    //格式化数据
    private function _fmtData( $data )
    {
        if(empty($data) && is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $data[$key]['is_on_sale'] = $value['is_on_sale'] == 1 ? lang('The shelves') : lang('Off the shelf');
            $data[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
        }

        return $data;
    }


    public function saveData( $data )
    {
        if( isset( $data['id']) && !empty($data['id'])) {
            $result = $this->edit( $data );
        } else {
            $result = $this->add( $data );
        }
        return $result;
    }

    public function add(array $data = [])
    {
        $goodsValidate = validate('Goods');
        if(!$goodsValidate->scene('add')->check($data)) {
            return info($goodsValidate->getError(), 0);
        }
        $user = Goods::get(['goods_code' => $data['goods_code']]);
        if (!empty($user)) {
            return info(lang('Coding repetition'), 0);
        }
        $data['goods_content'] = htmlspecialchars($data['goods_content']);
        $data['create_time'] = time();
        $id = $this->insertGetId( $data );
        if( false === $id) {
            return info(lang('Add failed'), 0);
        } else {
            return info(lang('Add succeed'), 1);
        }
    }

    public function edit(array $data = [])
    {
        $goodsValidate = validate('Goods');
        if(!$goodsValidate->scene('edit')->check($data)) {
            return info($goodsValidate->getError(), 0);
        }
        $moblie = $this->where(['goods_code'=>$data['goods_code']])->where('id', '<>', $data['id'])->value('goods_code');
        if (!empty($moblie)) {
            return info(lang('Coding repetition'), 0);
        }
        $data['goods_content'] = htmlspecialchars($data['goods_content']);
        $res = $this->allowField(true)->save($data,['id'=>$data['id']]);
        if($res == 1){
            return info(lang('Edit succeed'), 1);
        }else{
            return info(lang('Edit failed'), 0);
        }
    }

    //数据软删除
    public function deleteById( $id )
    {
        $result = Goods::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
