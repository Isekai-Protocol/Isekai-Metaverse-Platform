<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class Assembly extends Common
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
	 * where 搜索条件
	 * author: tianyu
	 * @param $post
	 *
	 * @return mixed
	 */
	protected function tableWhere($post)
	{
	    $where = [];
	    if(isset($post['title']) && $post['title'] != ""){
	        $where[] = ['part_name', 'like', '%'.$post['title'].'%'];
	    }
	    
	    // $where[] = ['goods_id','eq',$post['id']];
	    $result['where'] = $where;
	    $result['field'] = "*";
	    return $result;
	}

	//执行导入任务
	public function exec($params)
	{
	    $message = [];
	    try{
	        $file = json_decode($params, true);
	        $csv = new \org\Csv();
	        $resCsv = $csv->import($file['file_path']);
	        if($resCsv['status']){
	            $header = $resCsv['data'][0];
	            // dump($header);die;
	            unset($resCsv['data'][0]);
	            // $title=$userModel->csvHeader();
	            // dump($title);die;
	           
	            $iData = [];
	            // if($fields){
	                // $i = 0;
	                foreach ($resCsv['data'] as $key=>$val)
	                {
	                	$id = $val[0];
	                	for ($i = 1; $i < count($val); $i++) { 
	                		if(!empty($val[$i])){
	                			$cate_arr = explode('-', $header[$i]);
	                			$data = [
	                				'part_name' => $val[$i],
	                				'part_id' => $id,
	                				'part_cate' => $cate_arr[0],
	                				'part_type' => isset($cate_arr[1]) ? $cate_arr[1] : $cate_arr[0]
	                			];

	                			$iData[] = $data;
	                		}
	                	}
	                	
	                	// if(!empty($val[1])){
	                	// 	$temp_data = [
	                	// 		'part_id' => $val[0],//ID
	                	// 		'ears' => $val[1],//耳朵
	                	// 		'breastplate' => $val[2],//胸甲
	                	// 		'arms' => $val[3],//武器
	                	// 		'cloak_after' => $val[4],//披风后
	                	// 		'wing_right' => $val[5],//翅膀右
	                	// 		'lining' => $val[6],//内衬
	                	// 		'underwear' => $val[7],//内衣
	                	// 		'silk_stockings' => $val[8],//丝袜
	                	// 		'special_effect' => $val[9],//特效
	                	// 	];
	                	// }
	                	
	                    // foreach($fields as $fkey=>$fval){
	                    //     $iData[$i][$fval['value']]=$val[$fval['index']];
	                    // }
	                    // $i++;
	                }
	                $this->insertAll($iData);
	                // dump($iData);
	                // die;
	            // }
	           
	            // $uData['status'] = $ietaskModle::IMPORT_SUCCESS_STATUS;
	            // $uData['message'] = '导入成功';
	            // if($message){
	            //     $uData['message'] .= json_encode($message);
	            // }
	            // $uData['utime'] = time();
	            // $ietaskModle->update($uData, ['id' => $params['task_id']]);
	        	return ['status' => true,'msg' => '导入成功'];
	        }else{
	            return error_code(10041);
	        }
	    }catch (Exception $e){
	        return ['status' => false,'msg' => $e->getMessage()];
	    }
	    
	}
}
