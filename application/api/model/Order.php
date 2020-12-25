<?php
namespace app\api\model;

use think\Model;

class Order extends BaseModel
{   
    protected $hidden = ['user_id', 'delete_time', 'update_time'];  	
    protected $autoWriteTimestamp = true;
    // protected $createTime = 'create_timestamp'; 修改字段自己更新时间
    
    public function getSnapItemsAttr($value)
    {
    	if (empty($value)) {
    		return null;
    	}
    	return json_decode($value);
    }

     public function getSnapAddressAttr($value)
    {
        if (empty($value)) {
            return null;
        }
        return json_decode($value);
    }
    
    public static function getSSummaryList($uid, $page, $size)
    {
        return self::where('user_id', '=', $uid)->order('create_time desc')->paginate($size, true, ['page' => $page]);
    }

    public static function getSummaryByPage($page=1, $size=20)
    {
        $pagingData = self::order('create_time desc')->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

}