<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class DrawLog extends Common
{
    /**
     * @param $post
     *
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

        $roleModel = new \app\common\model\Goods();
        $extendModel = new \app\common\model\GoodsExtend();
        $userModel = new \app\common\model\User();

        $tableWhere = $this->tableWhere($post);

        $list = $this->where($tableWhere['where'])->order('id desc')->paginate($limit);

        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        foreach ($list as $key => $value) {
        	$list[$key]['username'] = $userModel->where('id',$value['user_id'])->value('username');
        	$list[$key]['role_name'] = $roleModel->where('id',$value['goods_id'])->value('name');
        	$list[$key]['role_img'] = _sImage($extendModel->where('goods_id',$value['goods_id'])->value('role_img')).'?x-oss-process=image/resize,w_1920,m_lfit/format,webp';
        	$list[$key]['ctime'] = date('Y-m-d H:i:s',$value['ctime']);
        }

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }

    /**
     * where 搜索条件
     * author: tianyu
     * @param $post
     *
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        $map = [];
        if(isset($post['name']) && $post['name'] != ""){
            $roleModel = new \app\common\model\Goods();
            $map[] = ['name', 'like', '%'.$post['name'].'%'];
            $ids = $roleModel->where($map)->column('id');
            $where[] = ['goods_id','in',$ids];
        }
        // dump($where);die;
        // $where[] = ['goods_id','eq',$post['id']];
        $result['where'] = $where;
        $result['field'] = "*";
        return $result;
    }
}
