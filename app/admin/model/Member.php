<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 会员管理
 */
class Member extends Admin
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //后台列表分页数据
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $data = $this->alias('m')
                     ->field('m.*, g.group_name, s.name as sales_name')
                     ->join('__MEMBER_GROUP__ g', 'm.group_id = g.id', 'LEFT')
                     ->join('__SALESMAN__ s', 'm.salesman_id = s.id', 'LEFT')
                     ->where( $request['map'] )
                     ->limit($request['offset'], $request['limit'])
                     ->order('m.id desc')
                     ->select();
        $total_page= $this->alias('m')
                     ->join('__MEMBER_GROUP__ g', 'm.group_id = g.id', 'LEFT')
                     ->join('__SALESMAN__ s', 'm.salesman_id = s.id', 'LEFT')
                     ->where( $request['map'] )
                     ->count();
        return array('total'=>$total_page,'rows'=>$this->_fmtData($data));                    
    }


    //获取状态
    public function getStatus($status_key = false) {
        $status = array(
                    0 => lang('Unaudited'), //未审核
                    1 => lang('Normal'),    //正常
                    2 => lang('Locking'),   //锁定
                  );
       return $status_key === false && !is_numeric($status_key)? $status : $status[$status_key];
    }

    //格式化数据
    private function _fmtData( $data )
    {
        if(empty($data) && is_array($data)) {
            return $data;
        }
        foreach ($data as $key => $value) {
            $data[$key]['status_name'] = $this->getStatus($value['status']);
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
        $nickname   = Member::get(['nickname' => $data['nickname']]);
        $username   = Member::get(['username' => $data['username']]);
        $mobile = Member::get(['mobile' => $data['mobile']]);
        if(!empty($nickname)) {
            return info(lang('Nickname already exists'), 0);
        }
        if(!empty($username)) {
            return info(lang('Username already exists'), 0);
        }
        if (!empty($mobile)) {
            return info(lang('Mobile already exists'), 0);
        }
        if($data['password2'] != $data['password']){
            return info(lang('The password is not the same twice'), 0);
        }
        $data['password'] = mduser($data['password']);
        $data['create_time'] = time();
        $this->allowField(true)->save($data);
        if($this->id > 0) {
            return info(lang('Add succeed'), 1, '', $this->id);
        }else {
            return info(lang('Add failed') ,0);
        }
    }

    public function edit(array $data = [])
    {
        $nickname   = $this->where(['nickname'=>$data['nickname']])->where('id', '<>', $data['id'])->value('nickname');
        $username   = $this->where(['username'=>$data['username']])->where('id', '<>', $data['id'])->value('username');
        $mobile = $this->where(['mobile'=>$data['mobile']])->where('id', '<>', $data['id'])->value('mobile');
        if(!empty($nickname)) {
            return info(lang('Nickname already exists'), 0);
        }
        if(!empty($username)) {
            return info(lang('Username already exists'), 0);
        }
        if (!empty($mobile)) {
            return info(lang('Mobile already exists'), 0);
        }
        if($data['password2'] != $data['password']) {
            return info(lang('The password is not the same twice'), 0);
        }
        if(!empty($data['password'])){
            $data['password'] = mduser($data['password']);
        }else {
            unset($data['password']); //密码为空则不修改
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
        $result = Member::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
