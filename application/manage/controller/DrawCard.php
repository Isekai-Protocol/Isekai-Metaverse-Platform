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
use think\facade\Request;

class DrawCard extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $cardModel = new \app\common\model\DrawCard();
        $roleModel = new \app\common\model\Goods();
        $this->assign('version_list',array_filter(array_unique($roleModel->column('post_version'))));

        if (Request::isAjax()) {
            return $cardModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    // 卡池角色表
    public function roleList()
    {
        $roleModel = new \app\common\model\CardRole();
        if (Request::isAjax()) {
            return $roleModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    // 添加卡池
    public function addRole()
    {
        $cardModel = new \app\common\model\DrawCard();
        $roleModel = new \app\common\model\Goods();

        if (Request::isPost()) {
            $data = input('param.');

            return $cardModel->addData($data);

        }

        return $this->fetch('',['version_list'=>array_filter(array_unique($roleModel->column('post_version')))]);
    }

    // 编辑卡池角色
    public function editRole()
    {
        $cardModel = new \app\common\model\DrawCard();
        $roleModel = new \app\common\model\Goods();

        $info = $cardModel->where('id',input('id'))->find();
        $info['utime'] = date('Y-m-d H:i:s',$info['stime']).' 到 '.date('Y-m-d H:i:s',$info['etime']);
        if (Request::isPost()) {
            $data = input('param.');
            
            return $cardModel->saveData($data);

        }

        return $this->fetch('',['info' => $info,'version_list'=>array_filter(array_unique($roleModel->column('post_version')))]);
    }

    public function del()
    {
        $roleModel = new \app\common\model\DrawCard();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$roleModel->remove(input('param.id/d'))) {
            return error_code(10023);
        }
        return $result;
    }

    // 抽卡记录
    public function drawLogList()
    {
        $logModel = new \app\common\model\DrawLog();
        
        if (Request::isAjax()) {
            return $logModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    // 卡券设置
    public function cardConfig()
    {
        $setModel = new \app\common\model\Setting();
        if(Request::isAjax())
        {
            $request = input('param.');
            unset($request['__Jshop_Token__']);
            unset($request['validate_form']);

            foreach ($request as $key => $value) {
                $info = $setModel->where(array('skey'=>$key))->find();
                if($info){
                    $setModel->where(array('skey'=>$key))->update(['value' => $value]);
                }else{
                    $setModel->insert([
                        'skey' => $key,
                        'value' => $value
                    ]);
                }   
            }

            return ['status' => true,'msg' => '设置成功'];
        }
        
        $info = [
            'card_img' => $setModel->where('skey','card_img')->value('value'),
            'card_price' => $setModel->where('skey','card_price')->value('value')
        ];

        return $this->fetch('card',['info' => $info]);

    }
}
