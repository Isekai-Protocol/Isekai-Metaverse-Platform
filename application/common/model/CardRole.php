<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class CardRole extends Common
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
        // $tableWhere = $this->tableWhere($post);

        $goodsModel = new \app\common\model\Goods();
        $goodsExtendModel = new \app\common\model\GoodsExtend();
        $where = [];

        if(isset($post['title']) && $post['title'] != ""){
            $ids = $goodsModel->where('name', 'like', '%'.$post['title'].'%')->column('id');
            if(!empty($ids)){
                $where[]= ['goods_id','in',$ids];
            }else{
                return [
                    'code' => 0,
                    'msg' => '',
                    'count' => 0,
                    'data' => []
                ];
            }
        }

        $list = $this->where($where)->paginate($limit);

        foreach($list as $key => $val)
        {
            $role_info = $goodsModel->where('id',$val['goods_id'])->find();
            $list[$key]['cover'] = _sImage($goodsExtendModel->where('goods_id',$val['goods_id'])->value('role_img')).'?x-oss-process=image/resize,w_1920,m_lfit/format,webp';
            $list[$key]['name'] = $role_info['name'];
        }
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }


    /**
     *  添加文章数据   方法
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
    
        if (!$this->allowField(true)->save($data))
        {
            $result['status'] = false;
            $result['msg'] = error_code(10004,true);
        }
        

        return $result;
    }


    /**
     *  文章编辑更新
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        
        if(!$this->allowField(true)->save($data,['g_id'=>$data['g_id']]))
        {
            $result['status'] = false;
            $result['msg'] = error_code(10004,true);
        }
       
        return $result;
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
        if(isset($post['title']) && $post['title'] != ""){
            $where[] = ['game_name', 'like', '%'.$post['title'].'%'];
        }
        
        // if(isset($post['type_id']) && $post['type_id'] != ""){
        //     $where[] = ['cate_id', 'eq', $post['type_id']];
        // }
        
        $result['where'] = $where;
        $result['field'] = "*";
        return $result;
    }

    /**
     * 文章搜索
     * @param $search_name
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search($search_name, $page = 1, $limit = 10){

        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        // 发布状态
        $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $where[] = ['title|brief', 'like', '%'.$search_name.'%'];

        $list = $this->where($where)
            ->field('id,title,cover,type_id,ctime,utime,sort,is_pub,pv,brief')
            ->order('sort asc')
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)->count();

        if(!$list->isEmpty())
        {
            $list = $list->hidden(['is_pub', 'isdel']);
            foreach ($list as &$v)
            {
                $v['cover'] = _sImage($v['cover']);
                $v['ctime'] = getTime($v['ctime']);
            }
        }

        $result['data'] = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
        ];

        return $result;

    }


    // 删除
    public function remove($id = '')
    {
        return $this->where('id',$id)->delete();
    }
}
