<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class Links extends Common
{
    /**
     * @param $post
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
           
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        if($data){
        	foreach ($data as $key => $value) {
        		if(!empty($value['path'])){
        			$data[$key]['path'] = _sImage($value['path']);
        		}

                if(!empty($value['parent_id'])){
                    $data[$key]['parent'] = $this->where('id',$value['parent_id'])->value('name');
                }else{
                    $data[$key]['parent'] = '一级链接';
                }
        	}
        }

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }

    /**
     * WHERE 搜索条件
     * User:tianyu
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['name']) && $post['name'] != ""){
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        // if(isset($post['ctime']) && $post['ctime'] != "") {
        //     $date_array = explode('~',$post['ctime']);
        //     $stime = strtotime($date_array[0].'00:00:00',time());   //从当天0点开始
        //     $etime = strtotime($date_array[1].'23:59:59',time());   //当天最后时间
        //     $where[] = ['ctime',['EGT',$stime],['ELT',$etime],'and'];
        // }

        if(isset($post['type']) && $post['type'] != ""){
            $where[] = ['type', 'eq', $post['type']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id'=>'desc','sort'=>'ASC'];
        return $result;
    }

    public function getLinksList()
    {
        if(getLng() == 'en'){
            $field_1 = 'name_eng as name,links,id';
            $field_2 = 'name_eng as name,links';
            $field_3 = 'name_eng as name,links,path';
        }else{
            $field_1 = 'name,links,id';
            $field_2 = 'name,links';
            $field_3 = 'name,links,path';
        }
        

        $list['parent'] = $this->field($field_1)->where(['type' => 1,'status' => 1,'parent_id' => 0])->order('sort asc')->select();
        if($list['parent']){
            foreach ($list['parent'] as $key => $value) {
                $list['parent'][$key]['links'] = $this->field($field_2)->where(['type' => 1,'status' => 1,'parent_id' => $value['id']])->order('sort asc')->select();
            }
        }

        
        $list['img'] = $this->field($field_3)->where(['type' => 2,'status' => 1])->order('sort asc')->select();

        if($list['img']){
            foreach ($list['img'] as $key => $value) {
                if(!empty($value['path'])){
                    $list['img'][$key]['path'] = _sImage($value['path']);
                }
            }
        }

        return $list;
    }
}
