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

class Business extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $businessModel = new \app\common\model\Business();

        if (Request::isAjax()) {
            return $businessModel->tableData(input('param.'));
        }
        return $this->fetch();
    }
}
