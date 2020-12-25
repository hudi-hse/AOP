<?php
namespace app\lib\exception;


use app\lib\exception\BaseException;
/**
 * 参数验证的异常PublicValidate
 */
class UserException extends BaseException
{
	public $code = 404;
	public $msg = '用户不存在';
	public $errorCode = 6000;

}