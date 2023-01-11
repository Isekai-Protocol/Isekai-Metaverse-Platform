<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Validate;

class ArticleType extends Common
{
    const TOP_CLASS_PARENT_ID = 0;          //顶级分类父类ID

    protected $rule = [
        'type_name' => 'require|max:25',
        'sort'      => 'number',
    ];

    protected $msg = [
        'type_name.require' => '分类名称必须',
        'type_name.max'     => '名称最多不能超过25个字符',
        'sort'              => '排序必须是数字',
    ];


    /**
     * 后台分类 树形列表
     *
     * @param $post
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = 20; // 后台列表分页数量默认50条
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        
        $user = new \app\common\model\User();
        if($data){
            foreach ($data as $key => $value) {
                $data[$key]['cover'] = _sImage($value['cover']);

                $user_info = $user->where('id',$value['user_id'])->field('username,avatar,id')->find();
                
                $data[$key]['user_id'] = $user_info['id'] ? $user_info['id'] : 0;
                $data[$key]['username'] = $user_info['username'] ? $user_info['username'] : '平台';
                if(empty($value['headimg'])){
                    $data[$key]['headimg'] = _sImage($user_info['avatar']);
                }else{
                    $data[$key]['headimg'] = _sImage($value['headimg']);
                }
                
            }
        }

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['type_name']) && $post['type_name'] != ""){
            $where[] = ['type_name', 'like', '%'.$post['type_name'].'%'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }


    /**
     *  分类添加
     * User:tianyu
     *
     * @param $data
     *
     * @return array
     */
    public function addData($data)
    {
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => []
        ];

        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data)) {
                $result['status'] = false;
                $result['msg']    = error_code(10004, true);
            }
        }
        return $result;
    }


    /**
     *  分类修改
     * User:tianyu
     *
     * @param $data
     *
     * @return array
     */
    public function editData($data)
    {
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => []
        ];
        $where = [
            'id' => $data['id']
        ];
        if (!$this->checkDie($data['id'], $data['pid'])) {
            return error_code(10802);
            // $result['msg']    = error_code(10802, true); //无法选择自己和自己的子级为父级
            // return $result;
        }
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if ($this->allowField(true)->save($data, $where) === false) {
                // $result['status'] = false;
                // $result['msg']    = error_code(10004, true);
                return error_code(10004);
            }
        }
        return $result;
    }


    /**
     * 递归遍历表格输出
     * User:tianyu
     *
     * @param     $arr //要输出的数组
     * @param int $pid //父id
     * @param int $step //节点替换次数
     *
     * @return array
     */
    public function getTree($arr = [], $pid = 0, $step = 0)
    {
        if (!$arr) {
            $arr = $this->order('sort asc')->select();
            if (!$arr->isEmpty()) {
                $arr = $arr->toArray();
            }
        }
        $tree = [];
        foreach ($arr as $key => $val) {
            if ($val['pid'] == $pid) {
                $flg              = str_repeat('└─', $step);
                $val['type_name'] = $flg . $val['type_name'];
                $tree[]           = $val;
                $tree = array_merge($tree, $this->getTree($arr, $val['id'], $step + 1));
            }
        }
        return $tree;
    }


    /**
     * 预先判断死循环
     * @param $id       当前id
     * @param $p_id     预挂载的父id
     * @param int $n    循环次数
     * @return bool     如果为true就是通过了，否则就是未通过
     */
    private function checkDie($id, $pid, $n = 10)
    {
        //设置计数器，防止极端情况下陷入死循环了（其他地方如果设置的有问题死循环的话，这里就报错了）
        if ($n <= 0) {
            return false;
        }
        if ($id == $pid) {
            return false;
        }
        if ($pid == self::TOP_CLASS_PARENT_ID) {
            return true;
        }
        $pinfo = $this->where(['id' => $pid])->find();
        if (!$pinfo) {
            return false;
        }
        if ($pinfo['pid'] == $id) {
            return false;
        }
        $n--;
        return $this->checkDie($id, $pinfo['pid'], $n);
    }


    /**
     * 获取文章分类列表
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleTypeList()
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => []
        ];

        $list                   = $this->field('id,pid,type_name')->select();
        $tree                   = $this->getArticleTypeTree($list, 0);
        $result['data']['list'] = $tree;

        return $result;
    }


    /**
     * 树状图递归
     *
     * @param     $data
     * @param int $pid
     *
     * @return array
     */
    public function getArticleTypeTree($data, $pid = 0)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['child'] = $this->getArticleTypeTree($data, $v['id']);
                $tree[]     = $v;
            }
        }
        return $tree;
    }


    /**
     * 获取文章分类父级分类
     *
     * @param $type_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleTypeFather($type_id)
    {
        return $this->getArticleTypeFatherTree($type_id);
    }


    /**
     * 递归获取文章父类分类树状图
     *
     * @param $type_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleTypeFatherTree($type_id)
    {
        $tree    = [];
        $where[] = ['id', 'eq', $type_id];
        $info    = $this->where($where)->find();
        if ($info['pid'] != 0) {
            $info['father'] = $this->getArticleTypeFatherTree($info['pid']);
        } else {
            $info['father'] = [];
        }
        $tree[] = $info;

        return $tree;
    }


    /**
     * 文章分类 与 文章 一对多关联
     *
     * @return \think\model\relation\HasMany
     */
    public function comments()
    {
        return $this->hasMany('Article');
    }

    // 获取专辑列表
    public function getCateList($page = 1, $limit = 10,$type = 0,$cate = 0,$user_id = 0,$is_my = 0,$other_user_id = 0,$is_draft = -1,$keyword = '',$status = -1)
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];

        $user = new \app\common\model\User();
        $articleModel = new \app\common\model\Article();
        $musicModel = new \app\common\model\Files();
        $gameModel = new \app\common\model\Game();
        $imgModel = new \app\common\model\Pictures();
        $roleModel = new \app\common\model\Goods();

        $where = [];

        if($status != -1){
            $where[] = ['status','eq',$status];
        }

        if($type == 0){
            $where[] = ['is_market','eq',$type];
        }else{
            if($type > 0){
                // 筛选市场内专辑，专辑内含有市场内藏品即可
                $article_cate_ids = $articleModel->where('is_market',1)->column('type_id');
                $music_cate_ids = $musicModel->where('is_market',1)->column('cat_id');
                $game_cate_ids = $gameModel->where('is_market',1)->column('cate_id');
                $img_cate_ids = $imgModel->where('is_market',1)->column('cate_id');
                $cIds = $article_cate_ids+$music_cate_ids+$game_cate_ids+$img_cate_ids;
                $where[] = ['id','IN',array_unique(array_filter($cIds))];
            }
        }

        if(!empty($other_user_id)){
            $where[] = ['user_id','eq',$other_user_id];
        }else{
            if(!empty($user_id) && $is_my == 1){
                $where[] = ['user_id','eq',$user_id];
            }
        }

        if(!empty($cate)){
            $where[] = ['type','eq',$cate];
        }

        if($is_draft >= 0){
            $where[] = ['is_draft','eq',$is_draft];
        }

        if(!empty($keyword)){
            $where[] = ['type_name','like','%'.$keyword.'%'];
        }

        $list = $this
            ->order(['sort','id'=>'desc'])
            ->where($where)
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)
            ->count();
        // dump($this->getLastSql());die;
        if(!$list->isEmpty())
        {
            foreach ($list as &$v)
            {
                if(getLng() == 'en'){
                    $v['type_name'] = $v['type_name_eng'];
                    $v['desc'] = $v['desc_eng'];
                }

                $v['cover'] = _sImage($v['cover']).addImageSuffix(2);
                if(!empty($v['user_id'])){
                    $v['user_name'] = $user->where('id',$v['user_id'])->value('username');
                    if(empty($v['headimg'])){
                        $v['headimg'] = _sImage($user->where('id',$v['user_id'])->value('avatar')).addImageSuffix(3);
                    }else{
                        $v['headimg'] = _sImage($v['headimg']).addImageSuffix(3);
                    }
                    
                }else{
                    $v['user_name'] = '平台';
                    $v['headimg'] = _sImage();
                }

                $v['back_url'] = _sImage($v['back_url']);

                $included_total = 0;

                // 数据统计
                if($v['type'] == 1){
                    // 文章
                    $included_total = $articleModel->where('type_id',$v['id'])->count();
                    $min_price = $articleModel->where('type_id',$v['id'])->order('price asc')->value('price');
                    $v['type_txt'] = '文章';
                }elseif($v['type'] == 2){
                    // 音频
                    $included_total = $musicModel->where('cat_id',$v['id'])->count();
                    $min_price = $musicModel->where('cat_id',$v['id'])->order('price asc')->value('price');
                    $v['type_txt'] = '音频';
                }elseif($v['type'] == 3){
                    // 游戏
                    $included_total = $gameModel->where('cate_id',$v['id'])->count();
                    $min_price = $gameModel->where('cate_id',$v['id'])->order('price asc')->value('price');
                    $v['type_txt'] = '游戏';
                }elseif($v['type'] == 4){
                    // 插画
                    $included_total = $imgModel->where('cate_id',$v['id'])->count();
                    $min_price = $imgModel->where('cate_id',$v['id'])->order('price asc')->value('price');
                    $v['type_txt'] = '插画';
                }else{
                    // 角色
                    $included_total = $roleModel->where('cate_id',$v['id'])->count();
                    $min_price = $roleModel->where('cate_id',$v['id'])->order('price asc')->value('price');
                    $v['type_txt'] = '角色';
                }

                $v['included_total'] = empty($included_total) ? 0 : $included_total;//收录数
                $v['min_price'] = empty($min_price) ? 0 : $min_price;//地板价
                if($v['status'] == 0){
                    $v['status_txt'] = '待审核'; 
                }elseif($v['status'] == 1){
                    $v['status_txt'] = '通过'; 
                }else{
                    $v['status_txt'] = '驳回';
                }
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
     * pc端文章列表页和文章详情页左侧使用，取当前文章分类的兄弟节点和子节点，并且把热门文章怼出来
     *
     * @param $type_id
     */
    public function leftInfo($type_id)
    {
        if ($type_id != self::TOP_CLASS_PARENT_ID) {
            $info = $this->where(['id' => $type_id])->find();
            if ($info) {
                $pid = $info['pid'];
            } else {
                $pid = self::TOP_CLASS_PARENT_ID;
            }
        } else {
            $pid = self::TOP_CLASS_PARENT_ID;
        }
        $type = $this->where(['pid' => $pid])->order('sort asc')->select();
        foreach ($type as $k => $v) {
            $type[$k]['child'] = $this->where(['pid' => $v['id']])->order('sort asc')->select();
        }

        //取热销文章
        $articleModel = new Article();
        $hot = $articleModel
            ->field('id,title,cover,ctime,pv')
            ->where(['is_pub' => $articleModel::IS_PUB_YES])
            ->order("pv desc")
            ->limit(5)
            ->select();
        foreach ($hot as $k => $v) {
            $hot[$k]['cover'] = _sImage($v['cover']);
            $hot[$k]['ctime'] = getTime($v['ctime']);
        }

        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [
                'list' => $type,
                'hot' => $hot
            ]
        ];
        return $result;
    }

    // 添加专辑
    public function addArticleType($user_id,$data = array())
    {
        $article = new Article();
        $files = new \app\common\model\Files();
        $pictures = new \app\common\model\Pictures();
        $game = new \app\common\model\Game();

        $preg = "/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is";
        if(!empty($data['links']) && !preg_match($preg, $data['links'])){
            return error_code(10214);
        }

        $addData = [
            'type_name' => $data['cate_name'],//专辑名称
            'type' => $data['type'],//专辑类型:1文章2音频3游戏4插画
            'desc' => $data['desc'],//专辑描述
            // 'headimg' => $data['headimg'],//专辑头像
            'cover' => $data['cover'],//专辑封面
            'back_url' => $data['back_url'],//专辑背景
            'links' => $data['links'],//链接
            'user_id' => $user_id,
            'is_draft' => $data['is_draft']//是否存为草稿:0否1是
        ];

        // 判断是否有id
        if(isset($data['id']) && !empty($data['id'])){
            // 编辑
            $res = $this->where('id',$data['id'])->update($addData);
            $id = $data['id'];
        }else{
            // 新增
            $res = $this->insertGetId($addData);
            $id = $res;
        }

        // 保存NFT
        if($id){
            if(isset($data['nft_data']) && !empty($data['nft_data'])){
                foreach ($data['nft_data'] as $key => $value) {
                    if($data['type'] == 1){
                        // 文章
                        $res = $article->where('id',$value['id'])->update(['type_id' => $id]);
                    
                    }elseif($data['type'] == 2){
                        // 音频
                        $res = $files->where('id',$value['id'])->update(['cat_id' => $id]);
                    
                    }elseif($data['type'] == 3){
                        // 游戏
                        $res = $game->where('g_id',$value['g_id'])->update(['cate_id' => $id]);
                    }else{
                        $res = $pictures->where('id',$value['id'])->update(['cate_id' => $id]);
                    }
                }
            }else{
                $res = $article->where('type_id',$id)->update(['type_id' => 0]);
                $res = $files->where('cat_id',$id)->update(['cat_id' => 0]);
                $res = $game->where('cate_id',$id)->update(['cate_id' => 0]);
                $res = $pictures->where('cate_id',$id)->update(['cate_id' => 0]);
            }
        }

        return ['status' => true,'msg' => '成功','data' => $id];
    }

    // 删除专辑
    public function delCate($id = '')
    {
        if(empty($id)){
            return error_code(10003);
        }

        $article = new Article();
        $files = new \app\common\model\Files();
        $pictures = new \app\common\model\Pictures();
        $game = new \app\common\model\Game();

        $cate = $this->where('id',$id)->count();
        if($cate <= 0){
            return error_code(10002);
        }

        $a_ids = $article->where('type_id',$id)->column('id');
        $f_ids = $files->where('cat_id',$id)->column('id');
        $p_ids = $pictures->where('cate_id',$id)->column('id');
        $g_ids = $game->where('cate_id',$id)->column('g_id');

        $res = $this->where('id',$id)->delete();

        if(!empty($a_ids)){
            $article->whereIn('id',$a_ids)->update(['type_id' => 0]);
        }

        if(!empty($f_ids)){
            $files->whereIn('id',$f_ids)->update(['cat_id' => 0]);
        }

        if(!empty($p_ids)){
            $pictures->whereIn('id',$p_ids)->update(['cate_id' => 0]);
        }

        if(!empty($g_ids)){
            $game->whereIn('g_id',$g_ids)->update(['cate_id' => 0]);
        }
        
        return $res ? ['status' => true,'msg' => '删除成功'] : error_code(10018);
    }

    // 专辑详情
    public function getArticleCateInfo($id = '',$keyword = '',$user_id = '',$page =1,$limit = 10)
    {
        if(empty($id)) return error_code(10002);

        $user = new \app\common\model\User();
        // 专辑详情
        $info['info'] = $this->where('id',$id)->find();
        if($info['info']){
            if(getLng() == 'en'){
                $info['info']['type_name'] = $info['info']['type_name_eng'];
                $info['info']['desc'] = $info['info']['desc_eng'];
            }

            $info['info']['cate_name'] = $info['info']['type_name'];
            $info['info']['cover'] = _sImage($info['info']['cover']);
            if(!empty($info['info']['user_id'])){
                $userInfo = $user->where('id',$info['info']['user_id'])->field('username,avatar,id')->find();
            }
            
            $info['info']['user_id'] = isset($userInfo['id']) && !empty($userInfo['id']) ? $userInfo['id'] : 0;
            $info['info']['username'] = isset($userInfo['username']) && !empty($userInfo['id']) ? $userInfo['username'] : '平台';
            $info['info']['headimg'] = _sImage($userInfo['avatar']);
            $info['info']['back_url'] = _sImage($info['info']['back_url']);

            $info['info']['is_my'] = ($info['info']['user_id'] == $user_id) ? true : false; 

        }else{
            return error_code(10002);
        }

        $article = new Article();
        $files = new \app\common\model\Files();
        $pictures = new \app\common\model\Pictures();
        $game = new \app\common\model\Game();
        $roleModel = new \app\common\model\Goods();
        $orderItemsModel = new \app\common\model\OrderItems();

        $label = new \app\common\model\Label();
        $list = [];

        // 数据统计
        // 发行数量
        $article_num = $article->where('type_id',$id)->count();
        $music_num = $files->where('cat_id',$id)->count();
        $game_num = $game->where('cate_id',$id)->count();
        $img_num = $pictures->where('cate_id',$id)->count();

        $info['info']['post_count'] = $article_num+$music_num+$game_num+$img_num;

        // 拥有者
        $info['info']['user_count'] = $this->where('user_id','>',0)->where('id',$id)->count();

        // 交易量
        $info['info']['buy_count'] = $orderItemsModel->where('cate_id',$id)->count();

        if($info['info']['type'] == 1){
            // 文章
            if(!empty($keyword)){
                if(getLng() == 'en'){
                    $map[] = ['title_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['title', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['type_id','eq',$id];
            $article_list = $article->page($page,$limit)->where($map)->select();
            $total = $article->where($map)->count();

            if($article_list){
                foreach ($article_list as $key => $value) {
                    $article_list[$key]['cover'] = _sImage($value['cover']);
                    $article_label = $value['labels'] ? unserialize($value['labels']) : [];

                    if(!empty($article_label)){
                        $article_list[$key]['labels'] = $label->whereIn('id',$article_label)->select();
                    }else{
                        $article_list[$key]['labels'] = [];
                    }

                    if(getLng() == 'en'){
                        $article_list[$key]['title'] = $value['title_eng'];
                        $article_list[$key]['brief'] = $value['brief_eng'];
                        $article_list[$key]['auother'] = $value['auother_eng'];
                    }

                    $article_list[$key]['name'] = $article_list[$key]['title'];       
                }

            }

            $list = $article_list;

        }elseif($info['info']['type'] == 2){
            // 音频
            if(!empty($keyword)){
                if(getLng() == 'en'){
                    $map[] = ['name_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['name', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['cat_id','eq',$id];

            $audio_list = $files->where($map)->page($page,$limit)->select();
            $total = $files->where($map)->count();
            if($audio_list){
                foreach ($audio_list as $k => $v) {
                    $audio_list[$k]['path'] = _sImage($v['path']);
                    if(getLng() == 'en'){
                        $audio_list[$k]['name'] = $v['name_eng'];
                        $audio_list[$k]['desc'] = $v['desc_eng'];
                    }

                    $audio_list[$k]['cover'] = $audio_list[$k]['path'];
                    $audio_list[$k]['file_url'] = $audio_list[$k]['url'];
                }

            }

            $list = $audio_list;

        }elseif($info['info']['type'] == 3){
            // 游戏
            if(!empty($keyword)){
                if(getLng() == 'en'){
                    $map[] = ['game_name_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['game_name', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['cate_id','eq',$id];

            $game_list = $game->where($map)->page($page,$limit)->select();
            $total = $game->where($map)->count();
            if($game_list){
                foreach ($game_list as $ky => $val) {
                    $game_list[$ky]['cover'] = _sImage($val['cover']);
                    $game_label = $val['labels'] ? unserialize($val['labels']) : [];
                    if(!empty($game_label)){
                        $game_list[$ky]['labels'] = $label->whereIn('id',$game_label)->select();
                    }else{
                        $game_list[$ky]['labels'] = [];
                    }

                    if(getLng() == 'en'){
                        $game_list[$ky]['game_name'] = $val['game_name_eng'];
                        $game_list[$ky]['game_type'] = $val['game_type_eng'];
                        $game_list[$ky]['game_content'] = $val['game_content_eng'];
                    }

                    $game_list[$ky]['id'] = $game_list[$ky]['g_id'];
                    $game_list[$ky]['name'] = $game_list[$ky]['game_name'];
                    $game_list[$ky]['file_url'] = '';
                }
            }

            $list = $game_list;
        }else{
            // 插画
            if(!empty($keyword)){
                if(getLng() == 'en'){
                    $map[] = ['name_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['name', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['cate_id','eq',$id];

            $picture_list = $pictures->where($map)->page($page,$limit)->select();
            $total = $pictures->where($map)->count();
            if($picture_list){
                $user = new \app\common\model\User();

                foreach ($picture_list as $ke => $va) {
                    if(!empty($va['user_id'])){
                        $user_info = $user->where('id',$va['user_id'])->field('username,avatar,id')->find();
                    }

                    $picture_list[$ke]['cover'] = _sImage($va['cover']);
                    $picture_list[$ke]['user_id'] = isset($user_info['id']) && !empty($user_info['id']) ? $user_info['id'] : 0;
                    $picture_list[$ke]['username'] = isset($user_info['username']) && !empty($user_info['username']) ? $user_info['username'] : '平台';
                    $picture_list[$ke]['avatar'] = isset($user_info['avatar']) && !empty($user_info['avatar']) ? $user_info['avatar'] : _sImage();

                    if(getLng() == 'en'){
                        $picture_list[$ke]['name'] = $va['name_eng'];
                        $picture_list[$ke]['desc'] = $va['desc_eng'];
                    }

                    $picture_list[$ke]['file_url'] = $va['path'];
                }
            }

            $list = $picture_list;
        }

        // 地板价
        $min_price = 0;

        foreach ($list as $price_key => $price_val) {
            $min_price = $price_val['price'];
            if($min_price < $price_val['price']){
                $min_price = $price_val['price'];
            } 
        }

        $info['info']['min_price'] = $min_price;

        // $info['article_list'] = $article_list;
        // $info['audio_list'] = $audio_list;
        // $info['picture_list'] = $picture_list;
        // $info['game_list'] = $game_list;

        $info['list'] = $list;

        $new_data = $info['info'];
        $new_data['method'] = 'articles.addalbum';
        $new_data['nft_data'] = $list;

        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $info,
            'info'   => $new_data,
            'total' => $total
        ];
        return $result;
    }
}
