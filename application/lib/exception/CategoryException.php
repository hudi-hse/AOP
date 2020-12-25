<?php
namespace app\lib\exception;


use app\lib\exception\BaseException;
/**
 * 参数验证的异常Category
 */
class CategoryException extends BaseException
{
	public $code = 400;
	public $msg = '指定的类目不存在，请检查参数';
	public $errorCode = 50000;

}