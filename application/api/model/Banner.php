<?php
namespace app\api\model;

use think\Model;

class Banner extends Model
{   
	protected $hidden = ['id','update_time','delete_time','description','name']; //模型隐藏不要的字段
	/**
	 * [items 关联BannerItem表]
	 * @Author xioahu
	 * @url    /
	 * @http   /
	 * @return [type] [description]
	 */
	public function items()
	{
        return $this->hasMany('BannerItem', 'banner_id', 'id');
                                
	}

	public static function getBannerByID($id)
	{    
		$result = self::with(['items','items.img'])->find($id);
		// gg($result->getLastSql());
        return $result;      
	}
}