<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 会员组管理
 */
class MemberGroup extends Admin
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //列表
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $data = $this->order('id desc')->where( $request['map'] )->limit($request['offset'], $request['limit'])->select();
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
            $data[$key]['status'] = $value['status'] ? lang('Start') : lang('Off');
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
        $group_name   = MemberGroup::get(['group_name' => $data['group_name']]);
        if(!empty($group_name)) {
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
        $group_name   = $this->where(['group_name'=>$data['group_name']])->where('id', '<>', $data['id'])->value('group_name');
        if(!empty($group_name)) {
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
        $result = MemberGroup::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
