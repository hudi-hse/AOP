<?php
namespace app\api\validate;

use app\api\validate\PublicValidate;
use app\lib\exception\ParameterException;



class OrderValidata extends PublicValidate
{
	protected $rule = [
        'products' => 'checkProducts'
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger',
    ];

    protected function checkProducts($values)
    {    
        if (!is_array($values)) {
            throw new ParameterException([
            	'msg' => '商品参数不对'
            ]); 
        }
        if (empty($values)) {
            throw new ParameterException([
            	'msg' => '商品列表不能为空'
            ]); 
        }
        
        foreach ($values as $value) {
            $this->checkProduct($value);
        }
    }

    protected function checkProduct($value)
    {
    	$validate = new PublicValidate($this->singleRule);
    	$result = $validate->check($value);
    	if (!$result) {
            throw new ParameterException([
            	'msg' => '商品列参数错误'
            ]); 
        }
    }
}