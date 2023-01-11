<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Article as articleModel;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class Article extends Manage
{


    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $article = new articleModel();
        if (Request::isAjax()) {
            return $article->tableData(input('param.'));
        }
        $articleTypeModel = new articleTypeModel();
        $list             = $articleTypeModel->where('type',1)->select();
        return $this->fetch('', ['list' => $list,'sort' => getSetting('sort_type_1')]);
    }


    /**
     *  文章添加
     * User:tianyu
     *
     * @return array|mixed
     */
    public function add()
    {
        if (Request::isPost()) {
            $article = new articleModel();
            return $article->addData(input('param.','','safe_filter'));
        }
        $articleTypeModel = new articleTypeModel();
        // 获取标签
        $labelModel = new \app\common\model\Label();
        $labelList = $labelModel->where('type',1)->select();

        return $this->fetch('add', ['list' => $articleTypeModel->where('type',1)->select(),'label' => $labelList]);
    }


    /**
     *
     *  文章编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $articleModel = new articleModel();
        if (Request::isPost()) {
            $data = [
                'id'      => input('post.id'),
                'type_id' => input('post.type_id'),
                'title'   => input('post.title'),
                'brief'   => input('post.brief'),
                'brief_eng'   => input('post.brief_eng'),
                'title_eng'   => input('post.title_eng'),
                // 'auother'   => input('post.auother'),
                // 'auother_eng'   => input('post.auother_eng'),
                'chapter'   => input('post.chapter'),
                'words'   => input('post.words'),
                'likes'   => input('post.likes'),
                'labels'   => empty(input('post.labels')) ? '' : serialize(input('post.labels')),
                'cover'   => input('post.cover'),
                'content' => input('post.content', '', 'safe_filter'),
                'is_pub'  => input('post.is_pub'),
                'sort'    => input('post.sort'),
                'price'    => input('post.price'),
                'is_market'    => input('post.is_market'),
                'is_new'    => input('post.is_new')
            ];
            return $articleModel->saveData($data);
        }
        $info = $articleModel->with('articleType')->where('id', input('param.id/d'))->find();
        if (!$info) {
            return error_code(10002);
        }

        if(!empty($info['labels'])){
            $info['labels'] = unserialize($info['labels']);
        }

        // 获取标签
        $labelModel = new \app\common\model\Label();
        $labelList = $labelModel->where('type',1)->select();

        $articleTypeModel = new articleTypeModel();
        return $this->fetch('edit', ['info' => $info, 'list' => $articleTypeModel->where('type',1)->select(),'label' => $labelList]);
    }


    /**
     *
     * User:tianyu
     *
     * @return array
     */
    public function del()
    {
        $article = new articleModel();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$article->destroy(input('param.id/d'))) {
            return error_code(10023);
        }
        return $result;
    }

    // 审核
    public function auditArticle()
    {
        $workModel = new \app\common\model\UserWorks();

        $info = $workModel->field('is_draft as status,obj_id as id')->where('obj_id',input('id'))->where('type',1)->find();
        if (Request::isPost()) {
            // 审核通过的时候-寄售-固定价格
            $res = aduitNft(input('id'),1,input('status'));
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10004);
        }
        
        $html = '
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">状态：</label>

                    <div class="layui-input-inline">
                        <select name="status" id="status" style="display:block;">';
                        if($info['status'] == 0){
                            $html .= '<option value="0" selected="selected">待审核</option>
                            <option value="2" >通过</option>
                            
                            <option value="3" >关闭</option>';
                        }elseif($info['status'] == 2){
                            $html .= '<option value="0" >待审核</option>
                            <option value="2" selected="selected">通过</option>
                            
                            <option value="3" >关闭</option>';
                        }elseif($info['status'] == 1){
                            $html .= '<option value="0" >待审核</option>
                            <option value="2" >通过</option>
                            
                            <option value="3" >关闭</option>';
                        }elseif($info['status'] == 3){
                            $html .= '<option value="0" >待审核</option>
                            <option value="2" >通过</option>
                            
                            <option value="3" selected="selected">关闭</option>';
                        }else{
                            $html .= '<option value="0" >待审核</option>
                            <option value="2" >通过</option>
                            
                            <option value="3" >关闭</option>';
                        }
                            
                    $html .= '</select></div>
                </div>';

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $html
        ];
    }

    /**
     * 小说章节列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function chapterIndex()
    {

        $chapter = new \app\common\model\ArticleChapter();
        if (Request::isAjax()) {
            return $chapter->tableData(input('param.'));
        }
        
        return $this->fetch('',['id' => input('id')]);
    }

    /*
     * 添加章节
     * */
    public function chapterAdd(){
        $this->view->engine->layout(false);
        $chapterModel = new \app\common\model\ArticleChapter();
        if (Request::isPost()) {
            $addData = [
                'article_id' => input('id'),
                'url' => input('chapter_url'),
                'name' => input('name'),
                'index' => input('index')
            ];
            return $chapterModel->insert($addData) ? ['status' => true,'msg' => '添加成功','data' => ''] : ['status' => false,'msg' => '添加失败'];
            // return $files->addData(input('param.'));
        }

        return $this->fetch('chapter_add',['id' => input('id')]);
    }

    /*
     * 章节编辑
     * */
    public function chapterEdit(){
       $this->view->engine->layout(false);
       $chapterModel = new \app\common\model\ArticleChapter();

       if (Request::isPost()) {

           $addData = [
                'article_id' => input('article_id'),
                'url' => input('chapter_url'),
                'name' => input('name'),
                'index' => input('index')
           ];

           return $chapterModel->where('id',input('id'))->update($addData) ? ['status' => true,'msg' => '修改成功','data' => ''] : ['status' => false,'msg' => '修改失败'];
           // return $files->addData(input('param.'));
       }

       $info = $chapterModel->where('id', input('param.id/d'))->find();

       return [
           'status' => true,
           'msg'    => '获取成功',
           'data'   => $this->fetch('chapter_edit', ['info' => $info])
       ];
    }

    //章节删除
    public function chapterDel()
    {
        $chapterModel = new \app\common\model\ArticleChapter();
        return $chapterModel->where('id',input('id'))->delete() ? ['status' => true,'msg' => '删除成功','data' => ''] : error_code(10018);
    }   
}
