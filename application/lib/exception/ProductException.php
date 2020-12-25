<?php
namespace app\lib\exception;


use app\lib\exception\BaseException;
/**
 * 异常Product
 */
class ProductException extends BaseException
{
	public $code = 404;
	public $msg = '指定的商品不存在，请检查参数';
	public $errorCode = 20000;

}