<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;
use traits\model\SoftDelete;


/**
 * 业务员管理
 */
class Salesman extends Admin
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //列表
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $data = $this->alias('s')
                     ->field('s.*, d.name as depart_name')
                     ->join('__DEPARTMENT__ d', 's.department_id = d.id', 'LEFT')
                     ->where( $request['map'] )
                     ->limit($request['offset'], $request['limit'])
                     ->order('s.id desc')
                     ->select();
        $total_page = $this->alias('s')
                     ->field('s.*, d.name as depart_name')
                     ->join('__DEPARTMENT__ d', 's.department_id = d.id', 'LEFT')
                     ->where( $request['map'] )
                     ->count();  
        return array('total'=>$total_page,'rows'=>$this->_fmtData($data));                    
    }

    //获取性别
    public function getSex($sex_num) {
        switch ($sex_num) {
            case 1:
            $sex = lang('Male');
                break;
            case 2:
            $sex = lang('Female');
                break;
            default:
            $sex = lang('Secrecy');
                break;
        }
        return $sex;
    }

    //格式化数据
    private function _fmtData( $data )
    {
        if(empty($data) && is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $data[$key]['sex'] = $this->getSex($value['sex']);
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
        $salesman_mobile = Salesman::get(['mobile' => $data['mobile']]);
        $salesman_name   = Salesman::get(['name' => $data['name']]);
        if (!empty($salesman_mobile)) {
            return info(lang('Mobile already exists'), 0);
        }
        if(!empty($salesman_name)) {
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
        $salesman_name   = $this->where(['name'=>$data['name']])->where('id', '<>', $data['id'])->value('name');
        $salesman_mobile = $this->where(['mobile'=>$data['mobile']])->where('id', '<>', $data['id'])->value('mobile');
        if (!empty($salesman_mobile)) {
            return info(lang('Mobile already exists'), 0);
        }
        if(!empty($salesman_name)) {
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
        $result = Salesman::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
