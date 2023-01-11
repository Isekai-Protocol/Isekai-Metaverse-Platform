<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class GameVersion extends Common
{
    
	/**
	 * @param $post
	 *
	 * @return mixed
	 * @throws \think\exception\DbException
	 */
	public function tableData($post)
	{

	    if(isset($post['limit'])){
	        $limit = $post['limit'];
	    }else{
	        $limit = config('paginate.list_rows');
	    }
	    $tableWhere = $this->tableWhere($post);
	    $list = $this->where($tableWhere['where'])->paginate($limit);

	    $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
	    $re['code'] = 0;
	    $re['msg'] = '';
	    $re['count'] = $list->total();
	    $re['data'] = $data;
	    return $re;
	}


	/**
	 *  添加文章数据   方法
	 * User:tianyu
	 * @param $data
	 * @return array
	 */
	public function addData($data)
	{
	    $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
	   
	    if(!empty($data['labels'])){
	        $data['labels'] = serialize($data['labels']);
	    }
	    
	    $data['code'] = makeCode();
	    if (!$this->allowField(true)->save($data))
	    {
	        $result['status'] = false;
	        $result['msg'] = error_code(10004,true);
	    }
	    

	    return $result;
	}


	/**
	 *  文章编辑更新
	 * User:tianyu
	 * @param $data
	 * @return array
	 */
	public function saveData($data)
	{
	    $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
	    
	    if(!$this->allowField(true)->save($data,['g_id'=>$data['g_id']]))
	    {
	        $result['status'] = false;
	        $result['msg'] = error_code(10004,true);
	    }
	   
	    return $result;
	}


	/**
	 * where 搜索条件
	 * author: tianyu
	 * @param $post
	 *
	 * @return mixed
	 */
	protected function tableWhere($post)
	{
	    $where = [];
	    if(isset($post['version']) && $post['version'] != ""){
	        $where[] = ['version', 'like', '%'.$post['version'].'%'];
	    }
	    
	    if(isset($post['g_id']) && $post['g_id'] != ""){
	        $where[] = ['g_id', 'eq', $post['g_id']];
	    }
	    
	    $result['where'] = $where;
	    $result['field'] = "*";
	    return $result;
	}
}
