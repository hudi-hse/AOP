<?php
namespace app\api\service;

use think\Cache;
use think\Exception;
use think\Request;
use app\lib\exception\TokenException;
use app\lib\exception\ForbittenException;
use app\lib\enum\ScopeEnum;


class Token
{   
	//生成令牌
	public static function generateToken()
	{
		//32位字符组成一组随机字符串
		$randChars = getRandChar(32);
		//用三组字符串，进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = config('secure.token_salt');
        return md5($randChars . $timestamp . $tokenSalt);	
	}
    
	public static function getCurrentTokenVal($key)
	{
        //令牌防在http请求的head传Token        
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
        	throw new TokenException();
        } else {
        	if (!is_array($vars)) {
        		$vars = json_decode($vars, true);
        	}
        	if (array_key_exists($key, $vars)) {
        		return $vars[$key];
        	} else {
        		throw new Exception("尝试获取的Token变量并不存在");
        	}
        }
        
	}
    
    /**
     * [getCurrentUid 获取档期用户的ID号]
     * @Author xioahu
     * @return [type] [description]
     */
	public static function getCurrentUid()
	{
         $uid = self::getCurrentTokenVal('uid');
         return $uid;
	}
    /**
     * 用户和CMS管理员都可以访问的权限
     */
    public static function needPrimaryScpe()
    {
        $scope = self::getCurrentTokenVal('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            } else {
                throw new ForbittenException(); 
            }
        } else {
            throw new TokenException(); 
        }    
    }
    /**
     * 只有用户可以访问的权限
     */
    public static function needExclusiveScpe()
    {
        $scope = self::getCurrentTokenVal('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbittenException(); 
            }
        } else {
            throw new TokenException();
        }    
    }


    public  static function isValidOperate($chechedUId)
    {
        if (!$chechedUId) {
            throw new Exception("检测UID时必须传人一个被检测的UID");
        }
        $currentOperateUID = self::getCurrentUid();
    
        if ($currentOperateUID == $chechedUId) {
            return true;
        }
    }


    /**
     * [verifyToken 检测token是否有用]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if ($exist) {
            return true;
        } else {
            return false;
        }
    }

}