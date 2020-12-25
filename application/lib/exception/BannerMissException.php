<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

/**
 * 控制器Banner异常
 */
class BannerMissException extends BaseException
{
	//HTTP 状态码 404,200
	public $code = 404;

	//错误具体信息
	public $msg = '请求的banner不存在';

	// 自定义错误码
	public $errorCode = 40000;
	

}