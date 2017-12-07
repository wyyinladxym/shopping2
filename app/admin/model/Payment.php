<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 支付管理
 */
class Payment extends Admin
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //列表
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $data = $this->where( $request['map'] )->limit($request['offset'], $request['limit'])->order('pay_sort desc')->select();
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
            $data[$key]['pay_status'] = $value['pay_status'] ? lang('Start') : lang('Off');
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
        $pay_name   = Payment::get(['pay_name' => $data['pay_name']]);
        $pay_code   = Payment::get(['pay_code' => $data['pay_code']]);
        if(!empty($pay_name)) {
            return info(lang('Name already exists'), 0);
        }
        if(!empty($pay_code)) {
            return info(lang('PaymentCode').lang('Already exists'), 0);
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
        $pay_name   = $this->where(['pay_name'=>$data['pay_name']])->where('id', '<>', $data['id'])->value('pay_name');
        $pay_code   = $this->where(['pay_code'=>$data['pay_code']])->where('id', '<>', $data['id'])->value('pay_code');
        if(!empty($pay_name)) {
            return info(lang('Name already exists'), 0);
        }
        if(!empty($pay_code)) {
            return info(lang('PaymentCode').lang('Already exists'), 0);
        }
        $res = $this->allowField(true)->save($data,['id'=>$data['id']]);
        if($res == 1) {
            return info(lang('Edit succeed'), 1);
        }else {
            return info(lang('Edit failed'), 0);
        }
    }

    //保存配置信息
    public function saveConfig( $data )
    {
        $id = $data['id'];
        unset($data['id']);
        $data['pay_config'] = json_encode($data);
        $res = $this->allowField(true)->save($data,['id'=>$id]);
        if($res == 1) {
            return info(lang('Edit succeed'), 1);
        }else {
            return info(lang('Edit failed'), 0);
        }
    }

    //数据删除
    public function deleteById( $id )
    {
        $result = Payment::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
