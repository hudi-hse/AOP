<?php
namespace app\lib\exception;


use app\lib\exception\BaseException;
/**
 * 参数验证的异常PublicValidate
 */
class OrderException extends BaseException
{
	public $code = 404;
	public $msg = '订单不存在，请检查ID';
	public $errorCode = 80000;

}