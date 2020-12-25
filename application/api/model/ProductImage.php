<?php
namespace app\api\model;

class ProductImage extends BaseModel
{
    public function imgUrl()
    {
    	return $this->belongsTo('Image', 'img_id', 'id');
    }

}