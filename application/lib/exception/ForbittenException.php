<?php
namespace app\lib\exception;


use app\lib\exception\BaseException;
/**
 * 参数验证的异常PublicValidate
 */
class ForbittenException extends BaseException
{
	public $code = 403;
	public $msg = '权限不够';
	public $errorCode = 10000;

}