<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class Prop extends Common
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
        $tableWhere = $this->tableWhere($post);
        $list = $this->where($tableWhere['where'])->paginate($limit);


        foreach($list as $key => $val)
        {
            if($val['type'] == 1){
               $list[$key]['cover'] = $val['prop_img'];
               $list[$key]['role_img'] = $val['prop_img'].'?x-oss-process=image/resize,w_600,h_600/format,webp';
               $list[$key]['prop_img'] = 'https://pzss.oss-cn-guangzhou.aliyuncs.com/static/uploads/images/2022/09/20/16636604266329718abbeb7.png';
            }else{
                $list[$key]['cover'] = _sImage($val['cover']);
                $list[$key]['role_img'] = _sImage($val['role_img']);
                $list[$key]['prop_img'] = _sImage($val['prop_img']);
            }
            
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
       
        if(!empty($data['labels'])){
            $data['labels'] = serialize($data['labels']);
        }
        
        $data['code'] = makeCode();
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
            $where[] = ['name', 'like', '%'.$post['title'].'%'];
        }
        
        $where[] = ['goods_id','eq',$post['id']];
        $result['where'] = $where;
        $result['field'] = "*";
        return $result;
    }


    /**
     * 获取游戏列表
     * @param bool $type_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGameList($type_id = 0, $page = 1, $limit = 10,$user_id = '')
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        $cateModel = new ArticleType();
        $label = new \app\common\model\Label();

        // $type_name = "游戏分类";
        // if($type_id != 0){
        //     $info = $gameCateModel->where('c_id',$type_id)->find();
        //     if($info){
        //         $type_name = $info['cate_name'];
        //     }
        // }

        $where = [];
        // 发布状态
        // $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        if(!empty($type_id)){
             $where[] = ['cate_id', 'eq', $type_id];
        }

        $list = $this
            ->where($where)
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)
            ->count();

        if(!$list->isEmpty())
        {
            // $list = $list->hidden(['is_pub', 'isdel']);
            foreach ($list as &$v)
            {
                $v['cover'] = _sImage($v['cover']);
                // $v['ctime'] = getTime($v['ctime']);
                if(!empty($v['labels'])){
                    $temp_label = unserialize($v['labels']);
                    $v['labels'] = $label->whereIn('id',$temp_label)->select();

                }
                
                $v['cate_name'] = $cateModel->where('id',$v['cate_id'])->value('type_name');
                $v['cate_name_eng'] = $cateModel->where('id',$v['cate_id'])->value('type_name_eng');

                //判断是否是已点赞
                if(!empty($user_id)){
                    $v['is_care'] = isUserCare($user_id,$v['id'],3);
                }else{
                    $v['is_care'] = false;
                } 
            }
        }

        //子文章分类
        // $articleTypeModel = new ArticleType();
        // foreach ($list as &$v){
        //     $res = $articleTypeModel->where(['id'=>$v['type_id']])->find();
        //     $v['type_name'] = $res['type_name'];
        // }
        // $articleTypeList = $articleTypeModel->where('pid', $type_id)->order('sort asc')->select();

        $result['data'] = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
            // 'article_type' => $articleTypeList,
            // 'type_name' => $type_name
        ];

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


    /**
     * 获取指定id 的游戏详情
     * @param $article_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGameInfo($game_id = '',$user_id = '')
    {
        $result = error_code(10002); //文章不存在或已删除
        if(empty($game_id)){
            return $result;
        }

        $where[] = ['g_id', 'eq', $game_id];
        // $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $data = $this
            ->where($where)
            ->find();

        if(!empty($data))
        {
            //$data['content'] = clearHtml($data['content'], ['width', 'height']);//清除文章中宽高
            $data['game_content'] = str_replace("clear: both;", "", $data['game_content']);
            $data['game_content'] = str_replace("<img", "<img style='max-width: 100%'", $data['game_content']);

            if(!empty($data['labels'])){
                $label = new \app\common\model\Label();
                $labels = unserialize($data['labels']);
                $data['labels'] = $label->whereIn('id',$labels)->select();
            }else{
                $data['labels'] = [];
            }
            
            $data['cover'] = _sImage($data['cover']);

            $gameCateModel = new ArticleType();

            $cate_info = $gameCateModel->where('id',$data['cate_id'])->find();
            $data['cate_name'] = $cate_info['type_name'];
            $data['cate_name_eng'] = $cate_info['type_name_eng'];

            //判断是否是已点赞
            if(!empty($user_id)){
                $data['is_care'] = isUserCare($user_id,$data['g_id'],3);
            }else{
                $data['is_care'] = false;
            } 
            //上一篇，下一篇
            // $data['up'] = [];
            // $data['down'] = [];

            // $uwhere['is_pub'] = ['is_pub', 'eq', self::IS_PUB_YES];
            // $uwhere['type_id'] = ['type_id', 'eq', $data['type_id']];
            // $list = $this->field('id,title')->where($uwhere)->select();
            // if(count($list)>1){
            //     foreach($list as $k => $v){
            //         if($v['id'] == $data['id']){
            //             if($k == 0 || $k == count($list)-1){
            //                 if($k == 0){
            //                     $data['down'] = $list[$k+1];
            //                 }else{
            //                     $data['up'] = $list[$k-1];
            //                 }
            //             }else{
            //                 $data['up'] = $list[$k-1];
            //                 $data['down'] = $list[$k+1];
            //             }
            //         }
            //     }
            // }

            $result['status'] = true;
            $result['msg'] = '获取成功';
            $result['data'] = $data;
        }
        return $result;
    }


    /**
     * @return \think\model\relation\HasOne
     */
    public function articleType()
    {
        return $this->hasOne('ArticleType','id','type_id')->bind(['type_name']);
    }

    // 删除
    public function remove($id = '')
    {
        return $this->where('g_id',$id)->delete();
    }
}
