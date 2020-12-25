<?php
namespace app\api\model;

class Product extends BaseModel
{   
    protected $hidden = ['delete_time','pivot','from','update_time','create_time'];

    public function getMainImgUrlAttr($value, $data)
    {
    	return $this->prefixImgUrl($value, $data);
    }

    /**
     * [imgs 商品详情]
     * @Author xioahu
     * @return [type] [description]
     */
    public function imgs()
    {
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }

     /**
     * [imgs 商品参数]
     * @Author xioahu
     * @return [type] [description]
     */
    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }


    public static function getMostRecent($count)
    { 
    	return self::limit($count)->order('create_time desc')->select();
    }


    public static function getProductCategory($id)
    {
        return self::where('category_id', '=', $id)->select();
    }

    public static function getProductDetail($id)
    {
        // return self::with('imgs.imgUrl,properties')->find($id);
        return self::with([
            'imgs' => function($query){
                $query->with(['imgUrl'])
                      ->order('order', 'asc');
            }])
            ->with(['properties'])
            ->find($id);
    }
}