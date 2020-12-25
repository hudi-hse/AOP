<?php
namespace app\lib\exception;


use app\lib\exception\BaseException;
/**
 * 微信服务接口调用的异常PublicValidate
 */
class WeChatException extends BaseException
{
	public $code = 4004;
	public $msg = '微信服务器接口调用失败';
	public $errorCode = 999;

}