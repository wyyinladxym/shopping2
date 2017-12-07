<?php
namespace app\www\model;

use think\Config;
use think\Model;
use think\Session;


/**
 * 分类模型
 */
class GoodsCat extends Base
{
    
    //分类树
    public function categoryTree()
    {
        $data = $this->where(['is_show'=>1])->order('sort desc')->select();
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


}
