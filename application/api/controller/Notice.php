<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\model\Notice as NoticeModel;
use app\common\controller\Api;
use think\facade\Cache;

/**
 * 公告
 * Class Notice
 * @package app\api\controller
 */
class Notice extends Api
{
    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeList()
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $noticeModel = new NoticeModel;

        //获取排序方法
        $order = input('param.order','id');
        //获取排序方式
        $orderType = input('param.orderType','desc');
        //每页显示多少，默认5条
        $pageSize = input('param.limit',10);
        //获取当前页
        $page = input('param.page',1);
        //获取公告类型
        $type = input('param.type', 1);
        $cate_id = input('param.cate_id',0);//分类id
        $keywords = input('param.keywords','');//关键词
        $is_recommend = input('param.is_recommend',-1);//是否推荐-1全部0否1是
        // if (!Cache::has("jshop_notice_noticelist" . '_' . $order . '_' . $orderType . '_' . $pageSize . '_' . $page)) {
            $data = $noticeModel->getNoticeList($type, $order, $orderType, $page, $pageSize,$cate_id,$keywords,$is_recommend);
            if ($data) {
                $result['data'] = $data;
            }
            // Cache::set("jshop_notice_noticelist" . '_' . $order . '_' . $orderType . '_' . $pageSize . '_' . $page, $result, 3600 * 5);
        // } else {
            // $result = Cache::get("jshop_notice_noticelist" . '_' . $order . '_' . $orderType . '_' . $pageSize . '_' . $page, $result);
        // }
        return $result;
    }


    /**
     *
     *  获取公告详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeInfo()
    {
        $result = error_code(10025);
        $noticeModel = new NoticeModel;
        $data = $noticeModel->getNoticeInfo(input('param.id/d'));

        if ($data) {
            $result['status'] = true;
            $result['msg'] = '获取成功';
            $result['data'] = $data;
        }

        return $result;
    }

    /**
     *获取音频
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function audioList()
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $files = new \app\common\model\Files();

        $token = input('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        //每页显示多少，默认5条
        $pageSize = input('param.limit',20);
        //获取当前页
        $page = input('param.page',1);
        // 分类
        $cat_id = input('cat_id',0);
        $type = input('type',-1);//音频类型:1配乐2配音-1全部
        $is_market = input('is_market',-1);//是否是市场:0否1是-1全部
        $min_price = input('min_price', 0);//最低价格
        $max_price = input('max_price', 0);//最高价格
        $status = input('status', -1);//1.立即解锁2.新品3.免费-1全部
        $sort = input('sort', -1);//-1默认1.最近上市2最近创建3.价格:从低到高4.价格:从高到底5.最久远的6.最近出售7.拍卖即将结束8.最高成交价
        // if (!Cache::has("jshop_audio_audiolist" . '_'.$cat_id.'_'. $pageSize . '_' . $page)) {
            $data = $files->getAudioList($cat_id,$page, $pageSize,$user_id,$type,$is_market,$min_price,$max_price,$status,$sort);
            if ($data) {
                $result = $data;
            }
            // Cache::set("jshop_audio_audiolist" . '_'.$cat_id.'_'. $pageSize . '_' . $page, $result, 3600 * 1);
        // } else {
        //     $result = Cache::get("jshop_audio_audiolist" . '_'.$cat_id.'_'. $pageSize . '_' . $page, $result);
        // }
        return $result;
    }

    // 音频详情
    public function audioInfo()
    {
        $result = error_code(10025);
        $files = new \app\common\model\Files();

        $token = input('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        $data = $files->getAudioInfo(input('id'),$user_id);

        // if ($data) {
        //     $result['status'] = true;
        //     $result['msg'] = '获取成功';
        //     $result['data'] = $data;
        // }

        return $data;
    }

    // 获取游戏
    public function getGameList()
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $files = new \app\common\model\Game();

        $token = input('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        //每页显示多少，默认5条
        $pageSize = input('param.limit',6);
        //获取当前页
        $page = input('param.page',1);
        // 分类
        $cat_id = input('cat_id',0);
        $type = input('type',-1);//是否是市场:0否1是-1全部
        $min_price = input('min_price', 0);//最低价格
        $max_price = input('max_price', 0);//最高价格
        $status = input('status', -1);//1.立即解锁2.新品3.免费-1全部
        $sort = input('sort', -1);//-1默认1.最近上市2最近创建3.价格:从低到高4.价格:从高到底5.最久远的6.最近出售7.拍卖即将结束8.最高成交价
        // if (!Cache::has("jshop_game_gamelist" . '_'.$cat_id.'_'. $pageSize . '_' . $page)) {
            $data = $files->getGameList($cat_id,$page, $pageSize,$user_id,$type,$min_price,$max_price,$status,$sort);
            if ($data) {
                $result['data'] = $data;
            }
            // Cache::set("jshop_game_gamelist" . '_'.$cat_id.'_'. $pageSize . '_' . $page, $result, 3600 * 1);
        // } else {
        //     $result = Cache::get("jshop_game_gamelist" . '_'.$cat_id.'_'. $pageSize . '_' . $page, $result);
        // }
        return $result;
    } 

    // 游戏详情
    public function gameInfo()
    {
        $result = error_code(10025);
        $game = new \app\common\model\Game();

        $token = input('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        $data = $game->getGameInfo(input('g_id'),$user_id);

        // if ($data) {
        //     $result['status'] = true;
        //     $result['msg'] = '获取成功';
        //     $result['data'] = $data;
        // }

        return $data;
    }

    // 添加藏品
    public function addIseKai()
    {
        $token = input('token');
        $user_id = getUserIdByToken($token);

        if(empty($user_id)){
            return error_code(14006);
        }

        $user = new \app\common\model\User();

        return $user->addIseKai($user_id,input('param.'));
    }

    // 藏品详情
    public function isekaiInfo()
    {
        $token = input('token');
        $user_id = getUserIdByToken($token);
        $work_id = input('work_id');
        $goods_id = input('id');

        $user = new \app\common\model\User();

        return $user->getIseKai($user_id,$work_id,$goods_id);
    }

    // 删除藏品
    public function delIseKai()
    {
        $token = input('token');
        $user_id = getUserIdByToken($token);
        $work_id = input('work_id');
        $goods_id = input('id');

        $user = new \app\common\model\User();

        return $user->removeIseKai($user_id,$work_id,$goods_id);
    }

    // 帮助分类列表
    public function helpCate()
    {
        $helpCate = new \app\common\model\HelpCate();

        $cate = $helpCate->order('sort asc')->select();

        if($cate){
            foreach ($cate as $key => $value) {
                if(getLng() == 'en'){
                    $cate[$key]['cate_name'] = $value['cate_name_eng'];
                }

                $cate[$key]['cate_img'] = _sImage($value['cate_img']);
            }
        }

        return ['status' => true,'msg'=> '','data' => $cate]; 
    }

    // 搜索
    public function searchAll()
    {
        $keywords = input('keywords','');
        //每页显示多少，默认5条
        $limit = input('limit',10);
        //获取当前页
        $page = input('page',1);
        $token = input('token');
        $user_id = getUserIdByToken($token);
        // 状态:1.立即解锁2.新品3.免费4.立即购买5.拍卖中-1全部
        $status = input('status',-1);
        $type = input('type',1);//1项目2专辑3用户
        // if(empty($keywords)){
        //     return error_code(10002);
        // }

        $list = [];
        $total = 0;
        $cate_obj = new \app\common\model\ArticleType();
        $user = new \app\common\model\User();
        $searchModel = new \app\common\model\Search();
        $search_ids = [];

        $article_obj = new \app\common\model\Article();
        $musicModel = new \app\common\model\Files();
        $gameModel = new \app\common\model\Game();
        $imgModel = new \app\common\model\Pictures();
        $workModel = new \app\common\model\UserWorks();

        if($type == 1){
            // 项目

            // 存储nft搜索记录
            // if(count($status) > 0){
                $ids_1 = nftStatus(-1,$status);
                
                // $where_1[] = ['s_id','IN',$ids_1];
                // $where_1[] = ['type','eq',1];
                // $where_1[] = ['keywords','eq',$keywords];

                // $where_2[] = ['s_id','IN',$ids_2];
                // $where_2[] = ['type','eq',2];
                // $where_2[] = ['keywords','eq',$keywords];

                // $where_3[] = ['s_id','IN',$ids_3];
                // $where_3[] = ['type','eq',3];
                // $where_3[] = ['keywords','eq',$keywords];

                // $where_4[] = ['s_id','IN',$ids_4];
                // $where_4[] = ['type','eq',4];
                // $where_4[] = ['keywords','eq',$keywords];

                // $data = $searchModel->where(function ($query) use ($where_1) {
                //     $query->where($where_1);
                // })->whereOr(function ($query) use ($where_2) {
                //     $query->where($where_2);
                // })->whereOr(function ($query) use ($where_3) {
                //     $query->where($where_3);
                // })->whereOr(function ($query) use ($where_4) {
                //     $query->where($where_4);
                // })->order('id desc')->page($page, $limit)->select();

                // $total = $searchModel->where(function ($query) use ($where_1) {
                //     $query->where($where_1);
                // })->whereOr(function ($query) use ($where_2) {
                //     $query->where($where_2);
                // })->whereOr(function ($query) use ($where_3) {
                //     $query->where($where_3);
                // })->whereOr(function ($query) use ($where_4) {
                //     $query->where($where_4);
                // })->count();
            
            // }else{
                $data = $searchModel->where('keywords',$keywords)->whereIn('s_id',$ids_1)->page($page,$limit)->select();
                $total = $searchModel->where('keywords',$keywords)->whereIn('s_id',$ids_1)->count();
            // }
            
            $is_add = $searchModel->where('keywords',$keywords)->count();

            if($is_add == 0){
                // 无记录-插入数据

                // 文章
                if(!empty($keywords)){
                //     $art_ids = $article_obj->column('id');
                //     // 音频
                //     $music_ids = $musicModel->column('id');
                //     // 游戏
                //     $game_ids = $gameModel->column('g_id');
                //     // 插画
                //     $img_ids = $imgModel->column('id');
                        $where1[] = ['title','like','%'.$keywords.'%'];
                        $where2[] = ['name','like','%'.$keywords.'%'];
                        $where3[] = ['game_name','like','%'.$keywords.'%'];
                        $where4[] = ['name','like','%'.$keywords.'%'];
                }

                // $ids_1 = nftStatus(-1,$status);
                // if(count($status) > 0 && in_array(2,$status)){
                //     $where1[] = ['is_new','eq',1];
                //     $where2[] = ['is_new','eq',1];
                //     $where3[] = ['is_new','eq',1];
                //     $where4[] = ['is_new','eq',1];
                // }
                // }else{
                    // 排除草稿与不通过的
                    // $no_art_ids = $workModel->where('type',1)->where('is_draft',1)->column('obj_id');
                    // if(!empty($no_art_ids)){
                        $where1[] = ['id','in',nftStatus(1,$status)];
                    // }

                    // $where1[] = ['status','eq',1];
                    
                    $art_ids = $article_obj->where($where1)->column('id');
                    
                    // 音频
                    // $no_music_ids = $workModel->where('type',2)->where('is_draft',1)->column('obj_id');
                    // if(!empty($no_music_ids)){
                        $where2[] = ['id','in',nftStatus(2,$status)];
                    // }
                    // $where2[] = ['status','eq',1];
                    
                    $music_ids = $musicModel->where($where2)->column('id');
                    
                    // 游戏
                    // $no_game_ids = $workModel->where('type',3)->where('is_draft',1)->column('obj_id');
                    // if(!empty($no_game_ids)){
                        $where3[] = ['g_id','in',nftStatus(3,$status)];
                    // }
                    // $where3[] = ['status','eq',1];
                   
                    $game_ids = $gameModel->where($where3)->column('g_id');
                    
                    // 插画
                    // $no_img_ids = $workModel->where('type',4)->where('is_draft',1)->column('obj_id');
                    // if(!empty($no_img_ids)){
                        $where4[] = ['id','in',nftStatus(4,$status)];
                    // }
                    // $where4[] = ['status','eq',1];
                    
                    $img_ids = $imgModel->where($where4)->column('id');
                // }

                $find_data = [];
                if(!empty($art_ids)){
                    for ($a=0; $a < count($art_ids); $a++) { 
                        $a_temp = [
                            's_id' => $art_ids[$a],
                            'type' => 1,
                            'keywords' => $keywords,
                            'ctime' => time()
                        ];

                        $find_data[] = $a_temp;
                    }
                }

                if(!empty($music_ids)){
                    for ($m=0; $m < count($music_ids); $m++) { 
                        $m_temp = [
                            's_id' => $music_ids[$m],
                            'type' => 2,
                            'keywords' => $keywords,
                            'ctime' => time()
                        ];

                        $find_data[] = $m_temp;
                    }
                }

                if(!empty($game_ids)){
                    for ($g=0; $g < count($game_ids); $g++) { 
                        $g_temp = [
                            's_id' => $game_ids[$g],
                            'type' => 3,
                            'keywords' => $keywords,
                            'ctime' => time()
                        ];

                        $find_data[] = $g_temp;
                    }
                }

                if(!empty($img_ids)){
                    for ($im=0; $im < count($img_ids); $im++) { 
                        $im_temp = [
                            's_id' => $img_ids[$im],
                            'type' => 4,
                            'keywords' => $keywords,
                            'ctime' => time()
                        ];

                        $find_data[] = $im_temp;
                    }
                }

                foreach ($find_data as $fkey => $fvalue) {
                    $search_ids[] = $searchModel->insertGetId($fvalue);
                }

                if($search_ids){
                    $data = $searchModel->whereIn('id',$search_ids)->page($page,$limit)->select();
                    $total = $searchModel->whereIn('id',$search_ids)->count();
                }else{
                    return ['status' => true,'msg' => '暂无记录','data' => []];
                }
            }

            if($data){
                $cate_key = 'type_name';
                if(getLng() == 'en'){
                    $cate_key = 'type_name_eng';
                }

                foreach ($data as $dkey => $dvalue) {
                    if($dvalue['type'] == 1){
                        // 文章
                        $art_info = $article_obj->where('id',$dvalue['s_id'])->find();
                        if(!empty($art_info)){
                            if(getLng() == 'en'){
                                $name_key = 'title_eng';
                                $content_key = 'brief_eng';
                            }else{
                                $name_key = 'title';
                                $content_key = 'brief';
                            }

                            $list[] = [
                                'id' => $art_info['id'],
                                'name' => $art_info[$name_key],
                                'cover' => _sImage($art_info['cover']).addImageSuffix(7),
                                'price' => $art_info['price'],
                                'code' => $art_info['code'],
                                'is_market' => $art_info['is_market'],
                                'content' => $art_info[$content_key],
                                'cate_name' => $cate_obj->where('id',$art_info['type_id'])->value($cate_key),
                                'file_url' => $art_info['file_url'],
                                'type' => 1
                            ];
                        }
                        

                    }elseif($dvalue['type'] == 2){
                        // 音频
                        $music_info = $musicModel->where('id',$dvalue['s_id'])->find();
                        if(!empty($music_info)){
                            if(getLng() == 'en'){
                                $mname_key = 'name_eng';
                                $mcontent_key = 'desc_eng';
                            }else{
                                $mname_key = 'name';
                                $mcontent_key = 'desc';
                            }

                            $list[] = [
                                'id' => $music_info['id'],
                                'name' => $music_info[$mname_key],
                                'cover' => _sImage($music_info['path']).addImageSuffix(6),
                                'price' => $music_info['price'],
                                'code' => $music_info['code'],
                                'is_market' => $music_info['is_market'],
                                'content' => $music_info[$mcontent_key],
                                'cate_name' => $cate_obj->where('id',$music_info['cat_id'])->value($cate_key),
                                'file_url' => $music_info['url'],
                                'type' => 2
                            ];
                        }

                    }elseif($dvalue['type'] == 3){
                        // 游戏
                        $game_info = $gameModel->where('g_id',$dvalue['s_id'])->find();
                        if(!empty($game_info)){
                            if(getLng() == 'en'){
                                $gname_key = 'game_name_eng';
                                $gcontent_key = 'game_content_eng';
                            }else{
                                $gname_key = 'game_name';
                                $gcontent_key = 'game_content';
                            }

                            $list[] = [
                                'id' => $game_info['g_id'],
                                'name' => $game_info[$gname_key],
                                'cover' => _sImage($game_info['cover']).addImageSuffix(8),
                                'price' => $game_info['price'],
                                'code' => $game_info['code'],
                                'is_market' => $game_info['is_market'],
                                'content' => $game_info[$gcontent_key],
                                'cate_name' => $cate_obj->where('id',$game_info['cate_id'])->value($cate_key),
                                'file_url' => '',
                                'type' => 3
                            ];
                        }

                    }else{
                        // 插画
                        $img_info = $imgModel->where('id',$dvalue['s_id'])->find();
                        if(!empty($img_info)){
                            if(getLng() == 'en'){
                                $pname_key = 'name';
                                $pcontent_key = 'desc';
                            }else{
                                $pname_key = 'name_eng';
                                $pcontent_key = 'desc_eng';
                            }

                            $list[] = [
                                'id' => $img_info['id'],
                                'name' => $img_info[$pname_key],
                                'cover' => _sImage($img_info['cover']).addImageSuffix(9),
                                'price' => $img_info['price'],
                                'code' => $img_info['code'],
                                'is_market' => $img_info['is_market'],
                                'content' => $img_info[$pcontent_key],
                                'cate_name' => $cate_obj->where('id',$img_info['cate_id'])->value($cate_key),
                                'file_url' => $img_info['path'],
                                'type' => 4
                            ];
                        }
                    }

                }
            }


        }elseif($type == 2){
            // 专辑
            $cate_map[] = ['type_name','like','%'.$keywords.'%'];
            $cate_map[] = ['is_draft','eq',2];

            $list = $cate_obj->where($cate_map)->page($page,$limit)->select();
            $total = $cate_obj->where($cate_map)->count();

            if($list){
                foreach ($list as $key => $value) {
                    if(getLng() == 'en'){
                        $list[$key]['type_name'] = $value['type_name_eng'];
                    }else{
                        $list[$key]['type_name'] = $value['type_name'];
                    }   

                    $list[$key]['cover'] = _sImage($value['cover']).addImageSuffix(2);
                    if(!empty($value['user_id'])){
                        $list[$key]['user_name'] = $user->where('id',$value['user_id'])->value('username');
                        if(empty($v['headimg'])){
                            $list[$key]['headimg'] = _sImage($user->where('id',$value['user_id'])->value('avatar'));
                        }else{
                            $list[$key]['headimg'] = _sImage($value['headimg']).addImageSuffix(3);
                        }
                        
                    }else{
                        $list[$key]['user_name'] = '平台';
                        $list[$key]['headimg'] = _sImage().addImageSuffix(3);
                    }

                    $list[$key]['back_url'] = _sImage($value['back_url']).'?x-oss-process=image/quality,Q_30';
                    
                    // 数据统计
                    $included_total = 0;
                    $min_price = 0;

                    if($value['type'] == 1){
                        // 文章
                        $included_total = $article_obj->where('type_id',$value['id'])->count();
                        $min_price = $article_obj->where('type_id',$value['id'])->order('price asc')->value('price');
                    }elseif($value['type'] == 2){
                        // 音频
                        $included_total = $musicModel->where('cat_id',$value['id'])->count();
                        $min_price = $musicModel->where('cat_id',$value['id'])->order('price asc')->value('price');
                    }elseif($value['type'] == 3){
                        // 游戏
                        $included_total = $gameModel->where('cate_id',$value['id'])->count();
                        $min_price = $gameModel->where('cate_id',$value['id'])->order('price asc')->value('price');

                    }elseif($value['type'] == 4){
                        // 插画
                        $included_total = $imgModel->where('cate_id',$value['id'])->count();
                        $min_price = $imgModel->where('cate_id',$value['id'])->order('price asc')->value('price');
                    }else{
                        // 角色
                        // $included_total = $roleModel->where('cate_id',$v['id'])->count();
                        // $min_price = $roleModel->where('cate_id',$v['id'])->order('price asc')->value('price');

                    }

                    $list[$key]['included_total'] = empty($included_total) ? 0 : $included_total;//收录数
                    $list[$key]['min_price'] = empty($min_price) ? 0 : $min_price;//地板价
                }
            }
        }else{
            // 用户
            $love = new \app\common\model\UserFollow();
            $list = $user->field('id,username,avatar,back_img')->where('username','like','%'.$keywords.'%')->page($page,$limit)->select();
            $total = $user->where('username','like','%'.$keywords.'%')->count();

            foreach ($list as $key => $value) {
                $list[$key]['avatar'] = _sImage($value['avatar']).addImageSuffix(4);
                $list[$key]['back_img'] = _sImage($value['back_img']);
                $list[$key]['care_count'] = $love->where('follow_id',$value['id'])->count();
                if(!empty($user_id)){
                    $list[$key]['is_care'] = $love->where(['user_id' => $user_id,'follow_id' => $value['id']])->count() > 0 ? true : false;
                }else{
                    $list[$key]['is_care'] = false;
                }
            }

        }
       
       return ['status' => true,'msg' => '成功','data' => $list,'count' => $total]; 
    }

    // 清除搜索记录
    public function clearSearch()
    {
        $searchModel = new \app\common\model\Search();

        $res = $searchModel->where('ctime','>',0)->delete();
    }
}