<?php
namespace app\api\controller\v1;

use app\api\validate\TokenGet;

use app\api\service\UserToken;

use app\api\service\Token as TokenService;

use app\api\service\AppToken;

use app\lib\exception\TokenException;

use app\lib\exception\ParameterException;

use app\api\validate\AppTokenGet;


class Token 
{   
    /**
     * [getToken toen的获取]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  string $code [description]
     * @return [type]       [description]
     */
    public function getToken($code = '')
    {
        (new TokenGet())->getCheck();
        $ut = new UserToken($code);
        $token = $ut->get();
        return show(1, 'success', $token, 'token');
    }
    
    /**
     * [verifyToken 判断token是否有效]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  string $token [description]
     * @return [type]        [description]
     */
    public function verifyToken($token='')
    {
    	if (!$token) {
    		throw new ParameterException([
    			 'token不许为空'
    		]);
    		
    	}

    	$valid = TokenService::verifyToken($token);

    	// return [
     //       'isValid' => $valid
    	// ];

    	 return show(5, 'success', $valid);
    } 

        /**
     * 第三方应用获取令牌
     * @url /app_token?
     * @POST ac=:ac se=:secret
     */
    public function getAppToken($ac='', $se='')
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET');
        (new AppTokenGet())->getCheck();
        $app = new AppToken();
        $token = $app->get($ac, $se);
        // return [
        //     'token' => $token
        // ];
         return show(5, 'success', $token);
    }


}
