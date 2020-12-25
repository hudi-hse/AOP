<?php
namespace app\lib\exception;

use think\exception\Handle;
use think\Request;
use think\Log;
use think\Exception;

/**
 * 设置中配置
 */
class ExceptionHandler extends Handle
{
	private $code;
	private $msg;
	private $errorCode;
	//需要返回客服端当前的URL路径
	
	public function render(\Exception $e)
	{   
        if ($e instanceof BaseException) {
        	//如果是自定义的异常
        	$this->code = $e->code;
        	$this->msg = $e->msg;
        	$this->errorCode = $e->errorCode;
        } else {
            // Config::get('app_debug');
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->code = 500;
                $this->msg = '服务器内部错误，不想告诉你';
                $this->errorCode = 999;
                $this->recordErrorlog($e);
            }
        }
        $request = Request::instance();
        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url()
        ];
        return json($result, $this->code);
	}
    
    /**
     * [recordErrorlog 写入日志文件]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  \Exception $e [异常参数]
     * @return [type]        [description]
     */
    private function recordErrorlog(\Exception $e)
    {   
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(),'error');
    }

}
