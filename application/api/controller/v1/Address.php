<?php
namespace app\api\controller\v1;

use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\service\Token;
use app\lib\exception\UserException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\ForbittenException;
use app\lib\exception\TokenException;
use app\lib\enum\ScopeEnum;
use app\api\model\User as UserModel;
use app\api\model\UserAddress;

use app\api\Controller\BaseController;



class Address extends BaseController
{   
	protected $beforeActionList = [
        'chechPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
	];
	    /**
     * 获取用户地址信息
     * @return UserAddress
     * @throws UserException
     */
    public function getUserAddress(){
        $uid = Token::getCurrentUid();
        $userAddress = UserAddress::where('user_id', $uid)
            ->find();
        if(!$userAddress){
            throw new UserException([
               'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }
    
    /**
     * [createOrUpdateAddress 更新和修改地址]
     * @Author xioahu
     * @url    /
     * @http   /
     * @return [type] [description]
     */
	public function createOrUpdateAddress()
	{   
		
	    $validate = new AddressNew(); 
		$validate->getCheck();

		//根据Token来获取uid
		//根据uid来查询用户的数据，判断用户是否存在，如果不存在抛出异常
		//如果用户存在，则获取用户从客户端提交的地址信息
		//根据用户地址信息是否存在，从而判断是添加地址还是修改地址
		$uid = TokenService::getCurrentUid();
		$user = UserModel::get($uid);
		if (!$user) {
			throw new UserException([
					'code' => 404,
					'msg' => '用户收获地址不存在',
					'errorCode' => 60001
				]);
		}
		//获取用户从客户端提交的地址xinx
		$dataArray = $validate->getDataByRule(input('post.'));
		//判断存在更新，不存在则添加
		$useAddress = $user->address;
		if (!$useAddress) {
			$su = $user->address()->save($dataArray);
		} else {
			$su = $user->address->save($dataArray);
		}

		 // return show(1, 'success', $su);
		return json(new SuccessMessage(), 202);
		
	}
   
}
