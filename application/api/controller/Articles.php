<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\ArticleType;
use app\common\model\Article as ArticleModel;
use app\common\model\WeixinMediaMessage;
use think\facade\Cache;
use think\facade\Request;


/**
 * 文章
 * Class Articles
 * @package app\api\controller
 */
class Articles extends Api
{
    /**
     * 获取专辑列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleType()
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $type = Request::param('type', 0);//是否是市场:0否1是-1全部
        $cate = Request::param('cate', 0);//专辑类型:1文章2音频3游戏4插画0全部
        $is_my = Request::param('is_my', 0);//专辑:1.个人0全部
        $status = Request::param('status', 1);//审核状态:0待审核1通过2.未通过-1全部
        $other_user_id = Request::param('other_user_id', 0);//其他人user_id
        $is_draft = Request::param('is_draft', -1);//是为草稿:0否1是-1全部
        $keywords = Request::param('keyword', '');//关键词

        $token = Request::param('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        $articleType = new ArticleType();
        // if (!Cache::has("jshop_article_getarticletype")) {
            // Cache::set("jshop_article_getarticletype", $articleType->getCateList($page,$limit), 3600 * 1);
           return $articleType->getCateList($page,$limit,$type,$cate,$user_id,$is_my,$other_user_id,$is_draft,$keywords,$status);
        // }
        // return Cache::get("jshop_article_getarticletype");
    }

    // 专辑详情
    public function getCateInfo()
    {
        $type_id = Request::param('type_id', 0);
        $keyword = Request::param('keyword', '');
        $token = Request::param('token');
        $user_id = getUserIdByToken($token);
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);

        $articleType = new ArticleType();

        return $articleType->getArticleCateInfo($type_id,$keyword,$user_id,$page,$limit);
    }

    /**
     * 获取文章列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleList()
    {
        $type_id = Request::param('type_id', 0);
        $type = Request::param('type', -1);//是否是市场:0否1是-1全部
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $min_price = Request::param('min_price', 0);//最低价格
        $max_price = Request::param('max_price', 0);//最高价格
        $status = Request::param('status', -1);//1.立即解锁2.新品3.免费-1全部
        $sort = Request::param('sort', -1);//-1默认1.最近上市2最近创建3.价格:从低到高4.价格:从高到底5.最久远的6.最近出售7.拍卖即将结束8.最高成交价
        $token = Request::param('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        // if (!Cache::has("jshop_article_getarticlelist" .'_'. $limit . "_" . $page . "_" . $type_id)) {
            $article = new ArticleModel();
            // Cache::set("jshop_article_getarticlelist". "_" . $limit . "_" . $page . "_" . $type_id, $article->articleList($type_id, $page, $limit,$user_id), 3600 * 1);
            return $article->articleList($type_id, $page, $limit,$user_id,$type,$min_price,$max_price,$status,$sort);
        // }
        // return Cache::get("jshop_article_getarticlelist" .'_'. $limit . "_" . $page . "_" . $type_id);
    }


    /**
     * 获取单个文章的详细信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleDetail()
    {
        $article_id = Request::param('article_id', 0);
        if (!$article_id) return error_code(10051);

        $token = Request::param('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        $article = new ArticleModel();
        return $article->articleDetail($article_id,$user_id);
        // if (!Cache::has("jshop_article_getarticledetail" . "_" . $article_id)) {
            // Cache::set("jshop_article_getarticledetail". "_" . $article_id, $article->articleDetail($article_id,$user_id), 3600 * 1);
        // }
        // return Cache::get("jshop_article_getarticledetail". "_" . $article_id);
    }


    /**
     * 微信信息
     * @return array|mixed
     */
    public function getWeixinMessage()
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => []
        ];
        $msg_id = Request::param('id', 0);
        if(!$msg_id) return error_code(10051);
        $messageModel = new WeixinMediaMessage();
        $result['data'] = $messageModel->getInfo($msg_id);
        $result['data']['content'] = clearHtml($result['data']['content'], ['width', 'height']);//清除文章中宽高
        $result['data']['content'] = str_replace("<img", "<img style='max-width: 100%'", $result['data']['content']);
        $result['data']['ctime'] = time_ago($result['data']['ctime']);
        return $result;
    }

    //pc端文章列表页左侧，取当前兄弟文章分类和子分类，加上热门推荐文章
    public function leftArticleType(){
        $articleType = new ArticleType();
        $type_id = $articleType::TOP_CLASS_PARENT_ID;

        if(!input('?param.type_id')){
            if(input('?param.article_id')){
                $article = new Article();
                $info = $article->where(['id'=>input('param.article_id')])->find();
                if($info){
                    $type_id = $info['type_id'];
                }
            }
        }else{
            $type_id = input('param.type_id');
        }

        return $articleType->leftInfo($type_id);

    }

    //文章搜索，只搜索标题和描述
    public function search(){
        $page        = input('page/d', 1);
        $limit       = input('limit/d',10);
        if(!input('?param.search_name')){
            return error_code('10000');
        }
        $article = new ArticleModel();
        return $article->search(input('param.search_name'),$page,$limit);
    }

    // 添加专辑
    public function addAlbum()
    {
        $token = Request::param('token');
        $user_id = getUserIdByToken($token);
        if(empty($user_id)){
            return error_code(14006);
        }
        $data = input('param.');

        $articleType = new ArticleType();
        return $articleType->addArticleType($user_id,$data);
    }

    // 删除专辑
    public function removeCate()
    {
        $cate_id = input('id');//专辑id 

        $articleType = new ArticleType();
        return $articleType->delCate($cate_id);
    }

    // 判断专辑内是否含有作品
    public function checkDelCate()
    {
        $cate_id = input('id');//专辑id 

        if(empty($cate_id)){
            return error_code(10003);
        }

        $article = new \app\common\model\Article();
        $files = new \app\common\model\Files();
        $pictures = new \app\common\model\Pictures();
        $game = new \app\common\model\Game();

        $articleType = new ArticleType();

        $cate = $articleType->where('id',$cate_id)->count();
        if($cate <= 0){
            return error_code(10002);
        }

        $a_num = $article->where('type_id',$cate_id)->count();
        $f_num = $files->where('cat_id',$cate_id)->count();
        $p_num = $pictures->where('cate_id',$cate_id)->count();
        $g_num = $game->where('cate_id',$cate_id)->count();

        $total = $a_num + $f_num + $p_num + $g_num;

        if($total > 0){
            return error_code(12024);
        }else{
            return ['status' => true,'msg' => ''];
        }
    }

    // 获取章节详情
    public function getChapterInfo()
    {
        $id = input('id');

        if(empty($id)){
            return error_code(10003);
        }

        $chapter = new \app\common\model\ArticleChapter();

        $info = $chapter->where('id',$id)->find();
        $content = '';

        if($info){
            $arrContextOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ];
            $content = file_get_contents($info['url'],false,stream_context_create($arrContextOptions));
            
        }else{
            return error_code(10002);
        }

        $name = explode('.', $info['name']);
        if(isset($name[0])){
            $title = $info['index'].' '.$name[0];
        }else{
            $title = $info['index'].' '.$name;
        }

        $data = [
            'title' => $title,
            'content' => mb_convert_encoding($content,'UTF-8','GBK'),
            'url' => $info['url']
        ];
        
        return ['status' => true,'msg' => '成功','data' => $data];
    }

}