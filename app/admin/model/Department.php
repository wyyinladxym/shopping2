<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 部门管理
 */
class Department extends Admin
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //列表
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $data = $this->where( $request['map'] )->limit($request['offset'], $request['limit'])->order('sort desc, id desc')->select();
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
            $data[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
        }
        return $data;
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
        $name   = Department::get(['name' => $data['name']]);
        if(!empty($name)) {
            return info(lang('Name already exists'), 0);
        }
        $data['create_time'] = time();
        $this->allowField(true)->save($data);
        if($this->id > 0){
            return info(lang('Add succeed'), 1, '', $this->id);
        }else {
            return info(lang('Add failed') ,0);
        }
    }

    public function edit(array $data = [])
    {
        $name   = $this->where(['name'=>$data['name']])->where('id', '<>', $data['id'])->value('name');
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
        $result = Department::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
