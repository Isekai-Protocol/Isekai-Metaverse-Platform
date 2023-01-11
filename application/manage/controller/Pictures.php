<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Pictures as PicturesModel;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class Pictures extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $picturesModel = new PicturesModel();
            return $picturesModel->tableData(input('param.'));
        }
        $articleTypeModel = new articleTypeModel();
        $list             = $articleTypeModel->where('type',4)->select();

        return $this->fetch('', ['list' => $list,'sort' => getSetting('sort_type_4')]);
    }


    /**
     *  插画添加
     * @return array|mixed
     */
    public function add()
    {
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            $picturesModel = new PicturesModel();
            return $picturesModel->addData(input('param.'));
        }

        $articleTypeModel = new articleTypeModel();
        $list             = $articleTypeModel->where('type',4)->select();

        return [
            'status' => true,
            'msg' => '获取成功',
            'data' => $this->fetch('',['cate' => $list])
        ];
    }


    /**
     * 插画修改
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $picturesModel = new PicturesModel();
        if(Request::isPost())
        {
            return $picturesModel->saveData(input('param.'));
        }
        $data = $picturesModel->where('id',input('param.id/d'))->find();
        if (!$data) {
            return error_code(10002);
        }

        $articleTypeModel = new articleTypeModel();
        $list             = $articleTypeModel->where('type',4)->select();

        return [
            'status' => true,
            'msg' => '获取成功',
            'data' => $this->fetch('edit',['data' => $data,'cate' => $list])
        ];
    }


    /**
     *  总后台品牌 软删除
     * User:tianyu
     * @return array
     */
    public function del()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10023,true),
            'data' => []
        ];
        $picturesModel = new PicturesModel();
        if ($picturesModel->remove(input('id/d'))) {
            $result['status'] = true;
            $result['msg'] = '删除成功';
        }
        return $result;
    }

    /**
     * 获取所有品牌
     */
    public function getAll()
    {
        $result     = [
            'status' => false,
            'msg'    => error_code(10025,true),
            'data'   => [],
        ];
        $brandModel = new BrandsModel();
        $brandList  = $brandModel->field('id,name,sort')->where([])->order('sort asc')->select();
        if (!$brandList->isEmpty()) {
            $result['data']   = $brandList->toArray();
            $result['status'] = true;
            $result['msg']    = '获取成功';
        }
        return $result;
    }

    // 审核
    public function auditPictures()
    {
        $workModel = new \app\common\model\UserWorks();

        $info = $workModel->field('is_draft as status,obj_id as id')->where('obj_id',input('id'))->where('type',4)->find();
        if (Request::isPost()) {
            // 审核通过的时候-寄售-固定价格
            $res = aduitNft(input('id'),4,input('status'));
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10004);
            // return $workModel->where('obj_id',input('id'))->where('type',4)->update(['is_draft' => input('status')]) ? ['status' => true,'msg' => '成功'] : error_code(10004);
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
}