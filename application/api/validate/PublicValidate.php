<?php
namespace app\api\validate;

use think\Validate;
use think\Request;
use think\Exception;

use app\lib\exception\ParameterException;


class PublicValidate extends Validate
{
    /**
     * [getCheck description]
     * @Author xioahu
     * @url    /
     * @http   /
     * @return [type] [description]
     *
     *         $request = Request::instance();
     *  $params = $request->param();
     *  $params['token'] = $request->header('token');
     *
     *  if (!$this->check($params)) {
     *      $exception = new ParameterException(
     *          [
     *              // $this->error有一个问题，并不是一定返回数组，需要判断
     *              'msg' => is_array($this->error) ? implode(
     *                   ';', $this->error) : $this->error,
     *           ]);
     *       throw $exception;
     *   }
     *   return true;
     */
    public function getCheck()
    {   

    	//获取全部参数  //必须设置contetn-type:application/json
    	$requset = Request::instance();
    	$params = $requset->param();
        //#
        $params['token'] = $requset->header('token');
    	//开始验证
    	// $result = $this->batch()->check($params);
        $result = $this->check($params);
 

    	if(!$result) {
            $herror = is_array($this->error) ? implode(';', $this->error) : $this->error;
            var_dump($herror);
           $e = new ParameterException([
                'msg' => $herror,
            ]);
    	    // $e->msg = $this->getError();
            //$e->errorCode = 10002;
    		throw $e;
        }else {
    		return true;
    	}


    }
      /**
     * [isValidateIn 自定义验证规则]
     * @Author xioahu
     * @param  [type]  $value [传入value的值来校验]
     * @param  string  $rule  [规则]
     * @param  string  $data  [description]
     * @param  string  $field [description]
     * @return boolean        [description]  is_numeric() 判断是否是数字 is_int()判断是否是整数
     */
    protected function  isValidateIn($value, $rule = '', $data = '', $field = '')
    {   
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            // return $field . '必须是正整数';
            return false;
        }
    }

    protected function isMobile($value)
    {
        $rule = '/^1(3|4|5|7|8)[0-9]\d{8}$/';  //18870582009
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    } 

    protected function isNotEmpty($value, $rule = '', $data = '', $field = '')
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * [getDataByRule 获取指定变量名参数值]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $arrays [获取客户端传来的变量名参数值]
     * @return [type]         [指定的变量名参数值]
     */
    public function getDataByRule($arrays)
    {
         if (array_key_exists('user_id', $arrays) | array_key_exists('uid', $arrays)) {
              // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
              throw new ParameterException([
                     'msg' => '参数中包含user_id或者uid是非法的参数'
                ]);
              
         }

         $newArray = [];
         foreach ($this->rule as $key => $value) {
           $newArray[$key] = $arrays[$key];
         }

         return $newArray;
    }

}