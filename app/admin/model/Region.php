<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 地区管理
 */
class Region extends Admin
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //列表
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $data = $this->where( $request['map'] )->limit($request['offset'], $request['limit'])->select();
        $total_page = $this->where( $request['map'] )->count();     
        return array('total'=>$total_page,'rows'=>$data);                    
    }

    public function saveData( $data )
    {
        if( isset( $data['id']) && !empty($data['id'])) {
            $result = $this->edit( $data );
        }else {
            $result = $this->add( $data );
        }
        return $result;
    }

    public function add(array $data = [])
    {
        $name   = Region::get(['region_name' => $data['region_name']]);
        if(!empty($name)) {
            return info(lang('Name already exists'), 0);
        }
        $this->allowField(true)->save($data);
        if($this->id > 0){
            return info(lang('Add succeed'), 1, '', $this->id);
        }else {
            return info(lang('Add failed') ,0);
        }
    }

    public function edit(array $data = [])
    {
        $name   = $this->where(['name'=>$data['region_name']])->where('id', '<>', $data['id'])->value('region_name');
        if(!empty($name)) {
            return info(lang('Name already exists'), 0);
        }
        $res = $this->allowField(true)->save($data,['id'=>$data['id']]);
        if($res == 1) {
            return info(lang('Edit succeed'), 1);
        }else {
            return info(lang('Edit failed'), 0);
        }
    }

    //数据删除
    public function deleteById( $id )
    {
        $result = Region::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
