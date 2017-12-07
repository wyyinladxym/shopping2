<?php
namespace app\admin\model;

use think\Config;
use think\Model;
use think\Session;


/**
 * 商品分类管理
 */
class GoodsCat extends Admin
{
    //列表
    public function getList( $request )
    {
        $request = $this->fmtRequest( $request );
        $where = isset($request['map']) ? $request['map'] : ['parent_id'=>0];
        $data = $this->where( $where )->order('sort desc')->select();
        $total_page = $this->where( $where )->count();
        return array('total'=>$total_page,'rows'=>$this->_fmtData($data));            
    }

    //格式化数据
    private function _fmtData( $data )
    {
        if(empty($data) && is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $data[$key]['is_show'] = $value['is_show'] == 1 ? lang('Show') : lang('Hide');
        }

        return $data;
    }

    public function saveData( $data )
    {
        if( isset( $data['id']) && !empty($data['id'])) {
            $info = $this->edit( $data );
        } else {
            $info = $this->add( $data );
        }

        return $info;
    }

    //编辑
    public function edit( $data )
    {
        $result = $this->where(['id'=>$data['id']])->update( $data );
        if( false === $result) {
            $info = info(lang('Edit failed'), 0);
        } else {
            $info = info(lang('Edit succeed'), 1);
        }
        return $info;
    }

    //添加
    public function add( $data )
    {
        $id = $this->insertGetId( $data );
        if( false === $id) {
            $info = info(lang('Add failed'), 0);
        } else {
            $info = info(lang('Add succeed'), 1, '', $id);
        }

        return $info;
    }

    //删除
    public function deleteById( $id )
    {
        $result = GoodsCat::destroy($id);
        if ($result > 0) {
            return info(lang('Delete succeed'), 1);
        }
    }

    //分类树
    public function categoryTree()
    {
        $data = $this->order('sort desc')->select();
        return $this->getTree($data);

    }

    protected function getTree($list, $pid = 0)
    {
        $tree = [];  
        if (!empty($list)) {   //先修改为以id为下标的列表
            $newList = [];     
            foreach ($list as $k => $v) {        
                $newList[$v['id']]['id'] = $v['id'];
                $newList[$v['id']]['name'] = $v['name'];
                $newList[$v['id']]['parent_id'] = $v['parent_id'];
                $newList[$v['id']]['is_show'] = $v['is_show'];
                $newList[$v['id']]['sort'] = $v['sort'];
            }        //然后开始组装成特殊格式
            foreach ($newList as $value) {
                if ($pid == $value['parent_id']) {//先取出顶级
                    $tree[] = &$newList[$value['id']];
                } elseif (isset($newList[$value['parent_id']])) {//再判定非顶级的pid是否存在，如果存在，则再pid所在的数组下面加入一个字段items，来将本身存进去
                    $newList[$value['parent_id']]['items'][] = &$newList[$value['id']];
                }
            }
        }    return $tree;
    }

    //调取select
    public function tagCategory($option_id = 0, $s_name = 'parent_id', $top_title='顶级分类')
    {
        $list = $this->categoryTree();
        $html = '';
        $html .= '<select name="'.$s_name.'" class="form-control">';
        $html .= '<option value="0">'.$top_title.'</option>';
        $option_id = $option_id ? $option_id : 0;
        $html .= $this->getTreeOptoin($list, $option_id);
        $html .= '</select>';
        return $html;
    }

    protected function getTreeOptoin($data, $option_id = 0, $level = -1)
    {
        static $option_str = '';
        if(!empty($data) && is_array($data)) {
            $level++;
            foreach($data as $val) {
                $selected = isset($option_id) && $option_id == $val['id'] ? 'selected' : '';
                $option_str .= '<option value="'. $val['id'] .'" '. $selected .' >'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level).'|---'.$val['name'].'</option>';
                if(isset($val['items']) && !empty($val['items'])) {
                    $val['items'] = $this->getTreeOptoin($val['items'], $option_id, $level);
                }
            }
        }
        return $option_str;
    }

}
