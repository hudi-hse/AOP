<?php
namespace app\api\controller\v1;

use app\api\validate\Count;
use app\api\validate\IdMustBePositiveIntegerValidate;

use app\api\model\Product as ProductModel;

use app\lib\exception\ProductException;



class Product 
{  
	/**
	 * [getRecent 获取全部商品]
	 * @Author xioahu
	 * @url    /
	 * @http   /
	 * @param  integer $count [description]
	 * @return [type]         [description]
	 */
	public function getRecent($count = 15)
	{   
		(new Count())->getCheck();
		$product = ProductModel::getMostRecent($count);

		if ($product->isEmpty()) {
            throw new ProductException();
        }
        $product = $product->hidden(['summary']);
        
		return json($product);
	}

	public function getProductCategoryAll($id)
	{   
		(new IdMustBePositiveIntegerValidate())->getCheck();
		$product = ProductModel::getProductCategory($id);
		if ($product->isEmpty()) {
            throw new ProductException();
        }
        return $product;
	}
    
    /**
     * [getOne 商品詳情]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
	public function getOne($id)
	{
	    (new IdMustBePositiveIntegerValidate())->getCheck();
	    $product = ProductModel::getProductDetail($id);
	    if (!$product) {
            throw new ProductException();
        }
        return show(5, 'success', $product);
	}


	public function deleteOne($id)
	{

	}
}
