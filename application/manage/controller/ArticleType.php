<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\Manage\controller;

use  app\common\controller\Manage;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class  ArticleType extends Manage
{

    /**
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $articleTypeModel = new ArticleTypeModel();
        if (Request::isAjax()) {
            return $articleTypeModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /**
     *
     *  分类添加
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        if (Request::isPost()) {
            $articleType = new ArticleTypeModel();
            return $articleType->addData(input('param.','','safe_filter'));
        }
       
        return $this->fetch('add');
    }


    /**
     *
     *  添加子分类
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addSon()
    {
        $articleTypeModel = new ArticleTypeModel();
        $this->view->engine->layout(false);
        $typeInfo = $articleTypeModel->where('id', input('param.id/d'))->find();
        if (!$typeInfo) return error_code(10002);
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('addson', ['typeInfo' => $typeInfo])
        ];
    }


    /**
     *
     *  文章分类编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $articleTypeModel = new ArticleTypeModel();
        if (Request::isPost()) {
            $data = [
                'id'      => input('post.id'),
                'type_name' => input('post.type_name'),
                'type_name_eng'   => input('post.type_name_eng'),
                'desc'   => input('post.desc'),
                'desc_eng'   => input('post.desc_eng'),
                'cover'   => input('post.cover'),
                'sort'    => input('post.sort'),
                'type'    => input('post.type'),
                'is_market'    => input('post.is_market'),
                'back_url'    => input('post.back_url'),
            ];
            return $articleTypeModel->editData($data);
        }
        $info = $articleTypeModel->where('id', input('param.id/d'))->find();
        if (!$info) {
            return error_code(10002);
        }

        return $this->fetch('edit', ['info' => $info]);
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del()
    {
        $articleTypeModel = new ArticleTypeModel();
        $result           = ['status' => true, 'msg' => '删除成功', 'data' => ''];
        //判断该分类下是否有子分类
       
        if (!$articleTypeModel->where('id', input('param.id/d'))->delete()) {
                //$result['status'] = false;
               // $result['msg']    = error_code(10023, true);
		  return error_code(10023);
        }

        return $result;
    }

    // 审核
    public function auditType()
    {
        $articleType = new ArticleTypeModel();

        $type_info = $articleType->where('id',input('id'))->find();
        if (Request::isPost()) {
            return $articleType->where('id',input('id'))->update(['status' => input('status')]) ? ['status' => true,'msg' => '成功'] : error_code(10004);
        }
        
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('audit', ['info' => $type_info])
        ];
    }
}
