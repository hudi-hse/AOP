<?php
namespace app\api\model;

class Category extends BaseModel
{   
	protected $hidden = ['delete_time','description','update_time'];
    public function img()
    {
    	return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

     public static function getCategory()
    {
    	return self::with('img')->select(); //第一种
    	// return self::all([],'img'); //第二种
    }
}