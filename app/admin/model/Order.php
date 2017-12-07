<?php
namespace app\admin\model;

use \think\Config;
use \think\Model;
use \think\Session;


/**
 * 订单管理
 */
class Order extends Admin
{
    protected $updateTime = false;
    protected $insert     = ['ip', 'user_id','browser','os'];
    protected $type       = [
        'create_time' => 'int',
    ];

    /**
     * 记录ip地址
     */
    protected function setIpAttr()
    {
        return \app\common\tools\Visitor::getIP();
    }

    /**
     * 浏览器把版本
     */
    protected function setBrowserAttr()
    {
        return \app\common\tools\Visitor::getBrowser().'-'.\app\common\tools\Visitor::getBrowserVer();
    }

    /**
     * 系统类型
     */
    protected function setOsAttr()
    {
        return \app\common\tools\Visitor::getOs();
    }

    /**
     * 用户id
     */
    protected function setUserIdAttr()
    {
        $user_id = 0;
        if (Session::has('userinfo', 'admin') !== false) {
            $user = Session::get('userinfo','admin');
            $user_id = $user['id'];
        }
        return $user_id;
    }
 
    public function record($remark)
    {
        $this->save(['remark' => $remark]);
    }


    public function UniqueIpCount()
    {   
        $data = $this->column('ip');
        $data = count( array_unique($data) );
        return $data;
    }

    //列表
    public function getList( $request )
    {
       $request = $this->fmtRequest( $request );

        $data = $this->order('l.create_time desc')
                     ->alias('l')
                     ->field('l.*,u.username')
                     ->join('__USER__ u','l.user_id = u.id')
                     ->where( $request['map'] )
                     ->limit($request['offset'], $request['limit'])
                     ->select();
        $total_page = $this->alias('l')
                           ->field('l.*,u.username')
                           ->join('__USER__ u','l.user_id = u.id')
                           ->where( $request['map'] )
                           ->count();     
        return array('total'=>$total_page,'rows'=>$this->dataFormat($data));                    
    }

    //数据格式化
    private function dataFormat( $data )
    {
        foreach ($data as $key=>$val) {
            $data[$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
        }
        return $data;
    }

    public function deleteById( $id )
    {
        $result = logRecord::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

}
