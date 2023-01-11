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
use app\common\model\Notice as noticeModel;
use app\common\model\ArticleType as articleTypeModel;
use app\common\model\HelpCate;
use think\facade\Request;

class Notice extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $noticeModel = new noticeModel();
        if (Request::isAjax()) {
            return $noticeModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /*
     * 添加公告
     * */
    public function add()
    {
        if (Request::isPost()) {
            $noticeModel = new noticeModel();
            $data = [
                'title' => input('title'),
                'title_eng' => input('title_eng'),
                'sort' => input('sort'),
                'content' => $_POST['content'],
                'cate_id' => input('cate_id'),
                'is_hot' => input('is_hot')
            ];
            return $noticeModel->addData($data);
        }

        $cateModel = new HelpCate();
        
        return $this->fetch('add', ['list' => $cateModel->select()]);
    }


    /**
     *
     *  公告编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $noticeModel = new noticeModel();
        if (Request::isPost()) {
            $data = [
                'title' => input('title'),
                'title_eng' => input('title_eng'),
                'sort' => input('sort'),
                'content' => $_POST['content'],
                'cate_id' => input('cate_id'),
                'is_hot' => input('is_hot')
            ];
          
            $res = $noticeModel->where('id',input('id'))->update($data);
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10021);
        }

        $cateModel = new HelpCate();
        $info = $noticeModel->where('id', input('param.id/d'))->find();

        return $this->fetch('edit', ['list' => $cateModel->select(),'info' => $info]);
    }

    /**
     *  公告软删除
     * User:tianyu
     *
     * @return array
     */
    public function del()
    {
        $result      = ['status' => true, 'msg' => '删除成功', 'data' => ''];
        $noticeModel = new noticeModel();
        if (!$noticeModel->destroy(input('post.id/d'))) {
            return error_code(10023);
        }

        return $result;
    }

    /**
     * 标签列表
     */

    public function labellist()
    {
        $label = new \app\common\model\Label();
        if (Request::isAjax()) {
            return $label->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /**
     * 标签删除
     */
    public function  labeldel(){
        $label = new \app\common\model\Label();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$label->del(input('param.id/d'))) {
            return error_code(10023);
        }
        return $result;
    }

    // 添加标签
    public function addLabel()
    {
       $this->view->engine->layout(false);
       $labelModel = new \app\common\model\Label();
       if (Request::isPost()) {
           // validateJshopToken();
           $add_data = [
            'name' => input('name'),
            'bak_color' => input('bak_color'),
            'type' => input('type')
           ];
           return $labelModel->insert($add_data) ? ['status' => true,'msg' => '添加成功','data' => ''] : ['status' => false,'msg' => '添加失败'];
       }
       return [
           'status' => true,
           'msg'    => '获取成功',
           'data'   => $this->fetch()
       ]; 
    }

    // 编辑标签
    public function editLabel()
    {
        $this->view->engine->layout(false);
        $labelModel = new \app\common\model\Label();
        if (Request::isPost()) {
            $id = input('param.id/d');
            $saveData = [
                'name' => input('name'),
                'bak_color' => input('bak_color'),
                'type' => input('type')
            ];
            return $labelModel->where('id',$id)->update($saveData) ? ['status' => true,'msg' => '修改成功','data' => ''] : ['status' => false,'msg' => '修改失败'];
        }
        $labelInfo = $labelModel->where('id', input('param.id/d'))->find();
        if (!$labelInfo) {
            return error_code(10002);
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('edit_label', ['labelInfo' => $labelInfo])
        ];
    }

    /**
     * 音频列表
     */
    public function music()
    {
        $files = new \app\common\model\Files();
        if (Request::isAjax()) {
            return $files->tableData(input('param.'));
        }

        $articleTypeModel = new articleTypeModel();

        return $this->fetch('',['cate' => $articleTypeModel->where('type',2)->select(),'sort' => getSetting('sort_type_2')]);
    }

    /*
     * 音频添加
     * */
    public function musicAdd(){
        $this->view->engine->layout(false);
        $files = new \app\common\model\Files();

        if (Request::isPost()) {
            $addData = [
                'name' => input('name'),
                'url' => input('video_url'),
                'path' => input('path'),
                'desc' => input('desc'),
                'likes' => input('likes'),
                'type' => input('type'),
                'file_type' => 'music',
                'ctime' => time(),
                'cat_id' => input('cat_id'),
                'name_eng' => input('name_eng'),
                'desc_eng' => input('desc_eng'),
                'price' => input('price'),
                'code' => makeCode(),
                'sort' => input('sort'),
                'is_market' => input('is_market'),
                'is_new' => input('is_new'),
                'links' => input('links'),
            ];

            $id = $files->insertGetId($addData);
            addPlatformDraft($id,2);

            return  $id ? ['status' => true,'msg' => '添加成功','data' => ''] : ['status' => false,'msg' => '添加失败'];
            // return $files->addData(input('param.'));
        }

        // 查询分类
        $articleTypeModel = new articleTypeModel();

        return $this->fetch('music_add',['cate' => $articleTypeModel->where('type',2)->select()]);
    }

    /*
     * 音频编辑
     * */
    public function musicEdit(){
        $this->view->engine->layout(false);
        $files = new \app\common\model\Files();
        $articleTypeModel = new articleTypeModel();

        if (Request::isPost()) {

            $addData = [
                'name' => input('name'),
                'url' => input('video_url'),
                'path' => input('path'),
                'desc' => input('desc'),
                'likes' => input('likes'),
                'cat_id' => input('cat_id'),
                'name_eng' => input('name_eng'),
                'desc_eng' => input('desc_eng'),
                'price' => input('price'),
                'type' => input('type'),
                'sort' => input('sort'),
                'is_market' => input('is_market'),
                'is_new' => input('is_new'),
                'links' => input('links'),
            ];

            return $files->where('id',input('id'))->update($addData) ? ['status' => true,'msg' => '修改成功','data' => ''] : ['status' => false,'msg' => '修改失败'];
            // return $files->addData(input('param.'));
        }

        $fileInfo = $files->where('id', input('param.id/d'))->find();

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('music_edit', ['fileInfo' => $fileInfo,'cate' => $articleTypeModel->where('type',2)->select()])
        ];

    }

    /**
     *  音频删除
     * User:tianyu
     *
     * @return array
     */
    public function musicDel()
    {
        $result      = ['status' => true, 'msg' => '删除成功', 'data' => ''];
        $files = new \app\common\model\Files();
        if (!$files->delMusic(input('id'))) {
            return error_code(10023);
        }

        return $result;
    }

    // 审核
    public function auditMusic()
    {
        $workModel = new \app\common\model\UserWorks();

        $info = $workModel->field('is_draft as status,obj_id as id')->where('obj_id',input('id'))->where('type',2)->find();
        if (Request::isPost()) {
            // 审核通过的时候-寄售-固定价格
            $res = aduitNft(input('id'),2,input('status'));
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10004);
            // return $workModel->where('obj_id',input('id'))->where('type',2)->update(['is_draft' => input('status')]) ? ['status' => true,'msg' => '成功'] : error_code(10004);
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
                            <option value="1" selected="selected">草稿</option>
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
     * 页脚-文字模块
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function linksList()
    {
        $linksModel = new \app\common\model\Links();
        if (Request::isAjax()) {
            return $linksModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /**
     * 页脚-图片模块
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function linksImgList()
    {
        $linksModel = new \app\common\model\Links();
        if (Request::isAjax()) {
            return $linksModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /*
     * 添加公告
     * */
    public function addLinks()
    {
        $this->view->engine->layout(false);
        $linksModel = new \app\common\model\Links();
        if (Request::isPost()) {
            if(input('?path')){
                $path = input('path');
            }else{
                $path = '';
            }
            $data = [
                'name' => input('name'),
                'name_eng' => input('name_eng'),
                'links' => input('links'),
                'sort' => input('sort'),
                'status' => input('status'),
                'type' => input('type'),
                'path' => $path,
                'ctime' => time(),
                'parent_id' => input('parent_id',0)
            ];
            
            $res = $linksModel->insert($data);
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10021);
        }
        
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('',['type' => input('type'),'parent' => $linksModel->where(['parent_id' => 0,'status' => 1,'type' => 1])->select()])
        ]; 
    }


    /**
     *
     *  公告编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editLinks()
    {
        $this->view->engine->layout(false);
        $linksModel = new \app\common\model\Links();

        if (Request::isPost()) {
            if(input('?path')){
                $path = input('path');
            }else{
                $path = '';
            }

            $data = [
                'name' => input('name'),
                'name_eng' => input('name_eng'),
                'links' => input('links'),
                'sort' => input('sort'),
                'status' => input('status'),
                'path' => $path,
                'parent_id' => input('parent_id',0)
            ];
            
            $res = $linksModel->where('id',input('id'))->update($data);
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10021);
        }
        
        $linksData = $linksModel->where('id',input('param.id/d'))->find();

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('',['type' => input('type'),'info' => $linksData,'parent' => $linksModel->where(['parent_id' => 0,'status' => 1,'type' => 1])->select()])
        ]; 
    }

    /**
     * 删除
     */
    public function  delLinks(){
        $links = new \app\common\model\Links();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$links->where('id',input('param.id/d'))->delete()) {
            return error_code(10023);
        }
        return $result;
    }

    // 文档列表
    public function documentList()
    {
        $linksModel = new \app\common\model\Document();
        if (Request::isAjax()) {
            return $linksModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    // 文档添加
    public function addDocument()
    {
        $linksModel = new \app\common\model\Document();
        if (Request::isPost()) {
            
            $data = [
                'title' => input('title'),
                'title_eng' => input('title_eng'),
                'content' => $_POST['content']
            ];
            
            $res = $linksModel->insert($data);
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10021);
        }
                
        return $this->fetch();
    }


    // 文档编辑
    public function editDocument()
    {
        $linksModel = new \app\common\model\Document();
        if (Request::isPost()) {
            $data = [
                'title'   => input('post.title'),
                'title_eng'   => input('post.title_eng'),
                'content' => $_POST['content'],
            ];
            $res = $linksModel->where('id',input('id'))->update($data);
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10021);
        }

        return $this->fetch('', ['info' => $linksModel->where('id',input('id'))->find()]);
    }

    // 文档删除
    public function delDocument()
    {
        $links = new \app\common\model\Document();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$links->where('id',input('param.id/d'))->delete()) {
            return error_code(10023);
        }
        return $result;
    }
}