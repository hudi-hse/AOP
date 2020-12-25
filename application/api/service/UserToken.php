<?php
namespace app\api\service;

use think\Exception;
use app\lib\exception\WeChatException;
use app\lib\exception\TokenException;
use app\lib\enum\ScopeEnum;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecet;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecet = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url_xw'), $this->wxAppID,$this->wxAppSecet,$this->code);
    }

    public function get()
    {   
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true); //把字符串变成数组
        if (empty($wxResult)) {
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        } else{
            $loginFall = array_key_exists('errcode', $wxResult);
            if ($loginFall) {
                $this->processLoginError($wxResult);
            } else {
                return $this->grantToken($wxResult);
            }
        }
    }

    private function grantToken($wxResult)
    {
        // 拿到openid
        // 数据库看一下，这个openID是不是存在
        // 如果存在 则不处理，如果不存在那么新增一条user记录
        // 生成令牌，准备缓存数据，写入缓存
        // 把令牌返回到客户端
        // key:令牌
        // value;wxResult uid, scope
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        if ($user) {
            $uid = $user->id;   
        } else {
            $uid = $this->newUser($openid);
        }
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $token_expire_in = config('setting.token_expire_in');
        $requ = cache($key, $value, $token_expire_in);
        if (!$requ) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => '10005'
            ]);            
        }
        return $key;
    }

    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;
        // $cachedValue['scope'] = 15;
        return $cachedValue;
    }
    
    /**
     * [newUser 添加用户数据]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $openid [字符串]
     * @return [type]         [返回用户添加成功的id]
     */
    private function newUser($openid)
    {
        $user = UserModel::create([
            'openid' => $openid        
        ]);
        return $user->id;
    }

    private function processLoginError($wxResult)
    {
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errCode' => $wxResult['errcode']
            ]);
        
    }
}