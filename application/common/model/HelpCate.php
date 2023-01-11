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

class HelpCate extends Common
{
    const TOP_CLASS_PARENT_ID = 0;          //顶级分类父类ID

    protected $rule = [
        'cate_name' => 'require|max:25',
        'sort'      => 'number',
    ];

    protected $msg = [
        'cate_name.require' => '分类名称必须',
        'cate_name.max'     => '名称最多不能超过25个字符',
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
        
        if($data){
            foreach ($data as $key => $value) {
                $data[$key]['cate_img'] = _sImage($value['cate_img']);
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
        if(isset($post['cate_name']) && $post['cate_name'] != ""){
            $where[] = ['cate_name', 'like', '%'.$post['cate_name'].'%'];
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
        // if (!$this->checkDie($data['id'], $data['pid'])) {
            // return error_code(10802);
            // $result['msg']    = error_code(10802, true); //无法选择自己和自己的子级为父级
            // return $result;
        // }
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


    // 删除
    public function remove($id = '')
    {   
        if(empty($id)){
            return false;
        }

        return $this->where('id',$id)->delete();
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
    public function getCateList($page = 1, $limit = 10,$type = 0,$cate = 0,$user_id = 0,$is_my = 0)
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];

        $where = [];

        if($type >= 0){
            $where['is_market'] = $type;
        }
        
        if(!empty($user_id) && $is_my == 1){
            $where['user_id'] = $user_id;
        }
        
        if(!empty($cate)){
            $where['type'] = $cate;
        }

        $list = $this
            ->order('sort asc')
            ->where($where)
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)
            ->count();

        if(!$list->isEmpty())
        {
            $user = new \app\common\model\User();
            $articleModel = new \app\common\model\Article();
            $musicModel = new \app\common\model\Files();
            $gameModel = new \app\common\model\Game();
            $imgModel = new \app\common\model\Pictures();
            $roleModel = new \app\common\model\Goods();

            foreach ($list as &$v)
            {
                if(getLng() == 'en'){
                    $v['type_name'] = $v['type_name_eng'];
                    $v['desc'] = $v['desc_eng'];
                }

                $v['cover'] = _sImage($v['cover']);
                if(!empty($v['user_id'])){
                    $v['user_name'] = $user->where('id',$v['user_id'])->value('username');
                    if(empty($v['headimg'])){
                        $v['headimg'] = $user->where('id',$v['user_id'])->value('avatar');
                    }else{
                        $v['headimg'] = _sImage($v['headimg']);
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
                }elseif($v['type'] == 2){
                    // 音频
                    $included_total = $musicModel->where('cat_id',$v['id'])->count();
                    $min_price = $musicModel->where('cat_id',$v['id'])->order('price asc')->value('price');
                }elseif($v['type'] == 3){
                    // 游戏
                    $included_total = $gameModel->where('cate_id',$v['id'])->count();
                    $min_price = $gameModel->where('cate_id',$v['id'])->order('price asc')->value('price');

                }elseif($v['type'] == 4){
                    // 插画
                    $included_total = $imgModel->where('cate_id',$v['id'])->count();
                    $min_price = $imgModel->where('cate_id',$v['id'])->order('price asc')->value('price');
                }else{
                    // 角色
                    $included_total = $roleModel->where('cate_id',$v['id'])->count();
                    $min_price = $roleModel->where('cate_id',$v['id'])->order('price asc')->value('price');

                }

                $v['included_total'] = empty($included_total) ? 0 : $included_total;//收录数
                $v['min_price'] = empty($min_price) ? 0 : $min_price;//地板价

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
        $addData = [
            'type_name' => $data['cate_name'],//专辑名称
            'type' => $data['type'],//专辑类型:1文章2音频3游戏4插画
            'desc' => $data['desc'],//专辑描述
            'headimg' => $data['headimg'],//专辑头像
            'cover' => $data['cover'],//专辑封面
            'back_url' => $data['back_url'],//专辑背景
            'links' => $data['links'],//链接
            'user_id' => $user_id,
            'is_draft' => $data['is_draft']//是否存为草稿:0否1是
        ];

        // 判断是否有id
        if(isset($data['id'])){
            // 编辑
            $res = $this->where('id',$data['id'])->update($addData);
        }else{
            // 新增
            $res = $this->insert($addData);
        }

        return $res ? ['status' => true,'msg' => '成功'] : error_code(10004);
    }


    // 专辑详情
    public function getArticleCateInfo($id = '',$keyword = '')
    {
        if(empty($id)) return error_code(10002);

        $user = new \app\common\model\User();
        // 专辑详情
        $info['info'] = $this->where('id',$id)->find();
        if($info['info']){
            if(getSetting('language') == 'en'){
                $info['info']['type_name'] = $info['info']['type_name_eng'];
                $info['info']['desc'] = $info['info']['desc_eng'];
            }

            $info['info']['cover'] = _sImage($info['info']['cover']);
            if(!empty($info['info']['user_id'])){
                $userInfo = $user->where('id',$info['info']['user_id'])->field('username,avatar,id')->find();
            }
            
            $info['info']['user_id'] = isset($userInfo['id']) && !empty($userInfo['id']) ? $userInfo['id'] : 0;
            $info['info']['username'] = isset($userInfo['username']) && !empty($userInfo['id']) ? $userInfo['username'] : '平台';
            $info['info']['headimg'] = _sImage($info['info']['headimg']);
            $info['info']['back_url'] = $info['info']['back_url'];
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
        $info['info']['user_count'] = $this->where('user_id','>',0)->count();

        // 交易量
        $info['info']['buy_count'] = $orderItemsModel->where('cate_id',$id)->count();

        if($info['info']['type'] == 1){
            // 文章
            if(!empty($keyword)){
                if(getSetting('language') == 'en'){
                    $map[] = ['title_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['title', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['type_id','eq',$id];
            $article_list = $article->where($map)->select();

            if($article_list){
                foreach ($article_list as $key => $value) {
                    $article_list[$key]['cover'] = _sImage($value['cover']);
                    $article_label = $value['labels'] ? unserialize($value['labels']) : [];

                    if(!empty($article_label)){
                        $article_list[$key]['labels'] = $label->whereIn('id',$article_label)->select();
                    }else{
                        $article_list[$key]['labels'] = [];
                    }

                    if(getSetting('language') == 'en'){
                        $article_list[$key]['title'] = $value['title_eng'];
                        $article_list[$key]['brief'] = $value['brief_eng'];
                        $article_list[$key]['auother'] = $value['auother_eng'];
                    }       
                }

            }

            $list = $article_list;

        }elseif($info['info']['type'] == 2){
            // 音频
            if(!empty($keyword)){
                if(getSetting('language') == 'en'){
                    $map[] = ['name_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['name', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['cat_id','eq',$id];

            $audio_list = $files->where($map)->select();
            if($audio_list){
                foreach ($audio_list as $k => $v) {
                    $audio_list[$k]['path'] = _sImage($v['path']);
                    if(getSetting('language') == 'en'){
                        $audio_list[$k]['name'] = $v['name_eng'];
                        $audio_list[$k]['desc'] = $v['desc_eng'];
                    }
                }

            }

            $list = $audio_list;

        }elseif($info['info']['type'] == 3){
            // 游戏
            if(!empty($keyword)){
                if(getSetting('language') == 'en'){
                    $map[] = ['game_name_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['game_name', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['cate_id','eq',$id];

            $game_list = $game->where($map)->select();
            if($game_list){
                foreach ($game_list as $ky => $val) {
                    $game_list[$ky]['cover'] = _sImage($val['cover']);
                    $game_label = $val['labels'] ? unserialize($val['labels']) : [];
                    if(!empty($game_label)){
                        $game_list[$ky]['labels'] = $label->whereIn('id',$game_label)->select();
                    }else{
                        $game_list[$ky]['labels'] = [];
                    }

                    if(getSetting('language') == 'en'){
                        $game_list[$ky]['game_name'] = $val['game_name_eng'];
                        $game_list[$ky]['game_type'] = $val['game_type_eng'];
                        $game_list[$ky]['game_content'] = $val['game_content_eng'];
                    }
                }
            }

            $list = $game_list;
        }else{
            // 插画
            if(!empty($keyword)){
                if(getSetting('language') == 'en'){
                    $map[] = ['name_eng', 'like', '%'.$keyword.'%'];
                }else{
                    $map[] = ['name', 'like', '%'.$keyword.'%'];
                }
                
            }

            $map[] = ['cate_id','eq',$id];

            $picture_list = $pictures->where($map)->select();
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

                    if(getSetting('language') == 'en'){
                        $picture_list[$ke]['name'] = $va['name_eng'];
                        $picture_list[$ke]['desc'] = $va['desc_eng'];
                    }
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
        
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $info
        ];
        return $result;
    }
}
