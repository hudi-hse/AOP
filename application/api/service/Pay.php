<?php
namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\lib\enum\OrderStatusEnum;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');//引用WxPay.Api.php文件微信支付SDK
//  LOader::import('子文件夹.文件名'，  (EXTEND_PATH)根目录目录文件夹名,  'Api.php')

class Pay
{
	//订单号
	private $orderID;

	private $orderNO;
	
	function __construct($orderID)
	{
		if (!$orderID) {
            throw new Exception('订单号不许为空NULL');
		}
		$this->orderID = $orderID;
	}

	public function pay()
	{   
		//数据库性能检测原则：1、把最有可能发生的情况防在最前面可以节约服务器的性能
		// 2、对数据库性能消耗小的防在最前面
		
		//订单号可能根本不存在
		//订单号确实存在，但是订单号和当前的用户是不匹配
		//订单号有可能已经支付过
		//进行库存检测
		$this->checkOrderValid();
        

		$orderService = new OrderService();
		$status = $orderService->checkOrderStock($this->orderID);
		if (!$status['pass']) {
			return $status;
		}
		return $this->makeWxPreOrder($status['orderPrice']);

	}

	private function makeWxPreOrder($totalPrice)
	{
	    //获取openid
	    $openid = Token::getCurrentTokenVal('openid');
	    if (!$openid) {
	    	throw new TokenException();
	    }  
	    // 开始调用微信SDK生成预支付交易单 返回正确的预支付交易会话标识
	    $wxOrderData = new \WxPayUnifiedOrder();
	    $wxOrderData->SetOut_trade_no($this->orderNO);//订单号
	    $wxOrderData->SetTrade_type('JSAPI');//交易类型
	    $wxOrderData->SetTotal_fee($totalPrice * 100); //z支付的总金额
	    $wxOrderData->SetBody('集享街');//描述
	    $wxOrderData->SetOpenid($openid); //小程序的openid
	    $wxOrderData->SetNotify_url(config('secure.pay_back_url')); //接受微信支付返回的参数
	    return $this->getPaySignature($wxOrderData);

	}
   
    //调用微信预订单的接口
	private function getPaySignature($wxOrderData)
	{   
		$wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
		 // 失败时不会返回result_code    $wxOrder['prepay_id']发送微信通知收货消息
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            // Log::record($wxOrder,'error');
            // Log::record('获取预支付订单失败','error');
            throw new Exception('获取预支付订单失败');
        }
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);

        return $signature;
	}

	// 返回到客户端的预支付交易会话标识
	private function sign($wxOrder)
	{
	    $jsAipPayData = new \WxPayJsApiPay();
	    $jsAipPayData->SetAppid(config('wx.app_id')); //小程序的APPID
	    $jsAipPayData->SetTimeStamp((string)time());  //时间戳要加string
	    
        $rand = md5(time() . mt_rand(0, 1000));
	    $jsAipPayData->SetNonceStr($rand);           //随时字符串要加string
	    
	    $jsAipPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);//统一下单返回的prepay_id
	    $jsAipPayData->SetSignType('md5'); //签名算法
	    $sign = $jsAipPayData->MakeSign();

	    $rawValuesData = $jsAipPayData->GetValues();
        
        $rawValuesData['paySign'] = $sign;
        unset($rawValuesData['appId']);

        return $rawValuesData;   
	}
     
     /**
      * [recordPreOrder 以更新的形式保存订单表中的 prepay_id]
      * @Author xioahu
      * @url    /
      * @http   /
      * @param  [type] $wxOrder [description]
      * @return [type]          [description]
      */
	private function recordPreOrder($wxOrder)
	{
       OrderModel::where('id', '=', $this->orderID)->update(['prepay_id' => $wxOrder['prepay_id']]);

	}


	private function checkOrderValid()
	{
		$order = OrderModel::where('id', '=', $this->orderID)->find();
		if (!$order) {
		    throw new OrderException();  		
		}

		if (!Token::isValidOperate($order->user_id)) {
			throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
			]);
		}

		if ($order->status != OrderStatusEnum::UNPAID) {
            throw new TokenException([
                'msg' => '订单已支付过',
                'errorCode' => 80003,
                'code' => 400
			]);
		}
		$this->orderNO = $order->order_no;

		return true;

	}
}
