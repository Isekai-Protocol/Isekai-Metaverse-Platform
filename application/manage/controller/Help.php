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
use app\common\model\HelpCate;
use think\facade\Request;

class Help extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $noticeModel = new noticeModel();
        $helpCateModel = new HelpCate();

        if (Request::isAjax()) {
            return $noticeModel->tableData(input('param.'));
        }
        return $this->fetch('notice/index',['cateList' => $helpCateModel->select()]);
    }

    /**
     * 帮助分类首页
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function helpCate()
    {
        $helpCateModel = new HelpCate();
        if (Request::isAjax()) {
            return $helpCateModel->tableData(input('param.'));
        }
        return $this->fetch('help_cate/index');
    }

    /*
     * 添加帮助分类
     * */
    public function addCate()
    {
        $this->view->engine->layout(false);
        $helpCateModel = new HelpCate();
        if (Request::isPost()) {
            return $helpCateModel->addData(input('param.'));
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('help_cate/add')
        ];

    }

    /**
     *
     *  帮助分类编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editCate()
    {
        $this->view->engine->layout(false);
        $helpCateModel = new HelpCate();
        if (Request::isPost()) {
            return $helpCateModel->editData(input('param.'));
        }
        $cateInfo = $helpCateModel->where('id', input('param.id/d'))->find();
        if (!$cateInfo) {
            return error_code(10002);
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('help_cate/edit', ['cateInfo' => $cateInfo])
        ];
    }

    /**
     * 帮助分类删除
     */
    public function  delCate(){
        $helpCateModel = new HelpCate();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$helpCateModel->remove(input('param.id/d'))) {
            return error_code(10023);
        }
        return $result;
    }

    /*
     * 添加公告
     * */
    public function add()
    {
        $this->view->engine->layout(false);
        $noticeModel = new noticeModel();
        if (Request::isPost()) {
            validateJshopToken();
            return $noticeModel->addData(input('param.'));
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch()
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
    public function edit()
    {
        $this->view->engine->layout(false);
        $noticeModel = new noticeModel();
        if (Request::isPost()) {
            return $noticeModel->saveData(input('param.'));
        }
        $noticeInfo = $noticeModel->where('id', input('param.id/d'))->find();
        if (!$noticeInfo) {
            return error_code(10002);
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('edit', ['noticeInfo' => $noticeInfo])
        ];
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

        return $this->fetch('',['cate' => $articleTypeModel->where('type',2)->select()]);
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
                'is_market' => input('is_market')
            ];

            return $files->insert($addData) ? ['status' => true,'msg' => '添加成功','data' => ''] : ['status' => false,'msg' => '添加失败'];
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
                'is_market' => input('is_market')
                
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
}