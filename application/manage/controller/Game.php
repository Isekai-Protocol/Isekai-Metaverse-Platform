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
use app\common\model\Game as GameModel;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class Game extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $game = new GameModel();
        if (Request::isAjax()) {
            return $game->tableData(input('param.'));
        }
        $cateModel = new articleTypeModel();
        $list             = $cateModel->where('type',3)->select();
        return $this->fetch('', ['list' => $list,'sort' => getSetting('sort_type_3')]);
    }


    /**
     *  游戏添加
     * User:tianyu
     *
     * @return array|mixed
     */
    public function add()
    {
        if (Request::isPost()) {
            $game = new GameModel();
            return $game->addData(input('param.'));
        }
        $cateModel = new articleTypeModel();
        // 获取标签
        $labelModel = new \app\common\model\Label();
        $labelList = $labelModel->where('type',2)->select();

        return $this->fetch('add', ['list' => $cateModel->where('type',3)->select(),'label' => $labelList]);
    }


    /**
     *
     *  游戏编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $gameModel = new GameModel();
        if (Request::isPost()) {
            $data = [
                'cate_id'      => input('cate_id'),
                'game_name' => input('game_name'),
                'game_name_eng'   => input('game_name_eng'),
                'price'   => input('price'),
                'post_time'   => input('post_time'),
                'post_addr'   => input('post_addr'),
                'game_type'   => input('game_type'),
                'game_cate'   => input('game_cate'),
                'game_type_eng'   => input('game_type_eng'),
                'make_company'   => input('make_company'),
                'post_compay'   => input('post_compay'),
                'language'   => input('language'),
                'cover'   => input('cover'),
                'game_content'   => input('game_content'),                
                'game_content_eng'   => input('game_content_eng'),                
                'labels'   => empty(input('post.labels')) ? '' : serialize(input('post.labels')),
                'g_id' => input('g_id'),
                'sort' => input('sort'),
                'is_market' => input('is_market'),
                'is_new' => input('is_new'),
                'script_id' => input('script_id'),
            ];
            return $gameModel->saveData($data);
        }
        $info = $gameModel->where('g_id', input('param.g_id/d'))->find();
        if (!$info) {
            return error_code(10002);
        }

        if(!empty($info['labels'])){
            $info['labels'] = unserialize($info['labels']);
        }

        // 获取标签
        $labelModel = new \app\common\model\Label();
        $labelList = $labelModel->where('type',2)->select();

        $gameCateModel = new articleTypeModel();
        return $this->fetch('edit', ['info' => $info, 'list' => $gameCateModel->where('type',3)->select(),'label' => $labelList]);
    }


    /**
     *
     * User:tianyu
     *
     * @return array
     */
    public function del()
    {
        $game = new GameModel();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$game->remove(input('param.g_id/d'))) {
            return error_code(10023);
        }
        return $result;
    }

    /**
     * 游戏版本
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function versionIndex()
    {

        $version = new \app\common\model\GameVersion();
        if (Request::isAjax()) {
            return $version->tableData(input('param.'));
        }
        
        return $this->fetch('',['g_id' => input('g_id')]);
    }

    /*
     * 添加版本
     * */
    public function versionAdd(){
        $this->view->engine->layout(false);
        $versionModel = new \app\common\model\GameVersion();
        if (Request::isPost()) {
            $addData = [
                'g_id' => input('g_id'),
                'version' => input('version'),
                'url' => input('url'),
                'sort' => input('sort')
            ];
            return $versionModel->insert($addData) ? ['status' => true,'msg' => '添加成功','data' => ''] : ['status' => false,'msg' => '添加失败'];
            // return $files->addData(input('param.'));
        }

        return $this->fetch('version_add',['g_id' => input('g_id')]);
    }

    /*
     * 版本编辑
     * */
    public function versionEdit(){
       $this->view->engine->layout(false);
       $versionModel = new \app\common\model\GameVersion();

       if (Request::isPost()) {

           $addData = [
                'g_id' => input('g_id'),
                'version' => input('version'),
                'url' => input('url'),
                'sort' => input('sort')
           ];

           return $versionModel->where('id',input('id'))->update($addData) ? ['status' => true,'msg' => '修改成功','data' => ''] : ['status' => false,'msg' => '修改失败'];
           // return $files->addData(input('param.'));
       }

       $info = $versionModel->where('id', input('param.id/d'))->find();

       return [
           'status' => true,
           'msg'    => '获取成功',
           'data'   => $this->fetch('version_edit', ['info' => $info])
       ];
    }

    //版本删除
    public function versionDel()
    {
        $versionModel = new \app\common\model\GameVersion();
        return $versionModel->where('id',input('id'))->delete() ? ['status' => true,'msg' => '删除成功','data' => ''] : error_code(10018);
    }

    // 审核
    public function auditGame()
    {
        $workModel = new \app\common\model\UserWorks();

        $info = $workModel->field('is_draft as status,obj_id as g_id')->where('obj_id',input('id'))->where('type',3)->find();
        if (Request::isPost()) {
            $res = aduitNft(input('id'),3,input('status'));
            return $res ? ['status' => true,'msg' => '成功'] : error_code(10004);
            // return $workModel->where('obj_id',input('id'))->where('type',3)->update(['is_draft' => input('status')]) ? ['status' => true,'msg' => '成功'] : error_code(10004);
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
