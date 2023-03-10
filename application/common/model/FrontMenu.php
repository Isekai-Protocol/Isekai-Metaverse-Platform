<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Validate;

class FrontMenu extends Common
{
    const TOP_CLASS_PARENT_ID = 0;          //顶级分类父类ID

    protected $rule = [
        'cate_name' => 'require|max:25',
        'sort'      => 'number',
    ];

    protected $msg = [
        'cate_name.require' => '分类名称必须',
        'cate_name.max'     => '名称最多不能超过25个字符',
        'sort'              => '排序必须是数字',
    ];


    /**
     * 后台分类 树形列表
     *
     * @param $post
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        // if (isset($post['limit'])) {
        //     $limit = $post['limit'];
        // } else {
        //     $limit = 99999999; // 后台列表分页数量默认50条
        // }
        // $tableWhere = $this->tableWhere($post);
        // $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->select();
        // $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        // dump($list);die;
        $data = $this->getMenuTree();
        dump($data);die;
        // if($data){
        //     foreach ($data as $key => $value) {
        //         $data[$key]['menu_img'] = _sImage($value['menu_img']);
        //     }
        // }

        // $re['code']  = 0;
        // $re['msg']   = '';
        // $re['count'] = $list->total();
        // $re['data']  = $data;
        // return $re;


    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['menu_name']) && $post['menu_name'] != ""){
            $where[] = ['menu_name', 'like', '%'.$post['menu_name'].'%'];
        }
        $where[] = ['pid','eq',0];
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }

    /**
     * 递归遍历表格输出
     * User:tianyu
     *
     * @param     $arr //要输出的数组
     * @param int $pid //父id
     * @param int $step //节点替换次数
     *
     * @return array
     */
    public function getMenuTree($arr = [], $pid = 0, $step = 0)
    {
        if (!$arr) {
            $arr = $this->order('sort asc')->select();
            if (!$arr->isEmpty()) {
                $arr = $arr->toArray();
            }
        }
        
        $tree = [];
        foreach ($arr as $key => $val) {
            if ($val['pid'] == $pid) {
                $flg              = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─', $step);
                $val['menu_name'] = $flg .$val['menu_name'];
                $tree[]           = $val;
                $tree = array_merge($tree, $this->getMenuTree($arr, $val['id'], $step + 1));
            }
        }
        return $tree;
    }

    /**
     *  分类添加
     * User:tianyu
     *
     * @param $data
     *
     * @return array
     */
    public function addData($data)
    {
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => []
        ];

        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data)) {
                $result['status'] = false;
                $result['msg']    = error_code(10004, true);
            }
        }
        return $result;
    }


    /**
     *  分类修改
     * User:tianyu
     *
     * @param $data
     *
     * @return array
     */
    public function editData($data)
    {
        $result = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => []
        ];
        $where = [
            'id' => $data['id']
        ];
        // if (!$this->checkDie($data['id'], $data['pid'])) {
            // return error_code(10802);
            // $result['msg']    = error_code(10802, true); //无法选择自己和自己的子级为父级
            // return $result;
        // }
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if ($this->allowField(true)->save($data, $where) === false) {
                // $result['status'] = false;
                // $result['msg']    = error_code(10004, true);
                return error_code(10004);
            }
        }
        return $result;
    }


    // 删除
    public function remove($id = '')
    {   
        if(empty($id)){
            return false;
        }

        return $this->where('id',$id)->delete();
    }


    /**
     * 获取文章分类列表
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleTypeList()
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => []
        ];

        $list                   = $this->field('id,pid,menu_name')->select();
        $tree                   = $this->getArticleTypeTree($list, 0);
        $result['data']['list'] = $tree;

        return $result;
    }


    /**
     * 树状图递归
     *
     * @param     $data
     * @param int $pid
     *
     * @return array
     */
    public function getArticleTypeTree($data, $pid = 0)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['child'] = $this->getArticleTypeTree($data, $v['id']);
                $tree[]     = $v;
            }
        }
        return $tree;
    }


    /**
     * 获取文章分类父级分类
     *
     * @param $type_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleTypeFather($type_id)
    {
        return $this->getArticleTypeFatherTree($type_id);
    }


    /**
     * 递归获取文章父类分类树状图
     *
     * @param $type_id
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArticleTypeFatherTree($type_id)
    {
        $tree    = [];
        $where[] = ['id', 'eq', $type_id];
        $info    = $this->where($where)->find();
        if ($info['pid'] != 0) {
            $info['father'] = $this->getArticleTypeFatherTree($info['pid']);
        } else {
            $info['father'] = [];
        }
        $tree[] = $info;

        return $tree;
    }


    /**
     * 文章分类 与 文章 一对多关联
     *
     * @return \think\model\relation\HasMany
     */
    public function comments()
    {
        return $this->hasMany('Article');
    }

}
