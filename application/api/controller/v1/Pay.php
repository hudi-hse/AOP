<?php
namespace app\api\controller\v1;
use app\api\Controller\BaseController;

use app\api\validate\IdMustBePositiveIntegerValidate;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify;



class Pay extends BaseController
{   
	protected $beforeActionList = [
        'chechExclusiveScope' => ['only' => 'getPreOrder']
	];
	/**
	 * [getPreOrder 调用微信接口，微信支付生成的预订单，即账单详情]
	 * 应用场景
     *商户系统先调用该接口在微信支付服务后台生成预支付交易单，返回正确的预支付交易会话标识后再在APP里面调起支付。
     *接口链 URL地址：https://api.mch.weixin.qq.com/pay/unifiedorder
     *是否需要证书不需要
	 * @Author xioahu
	 * @url    /
	 * @http   /
	 * @param  [type] $id [订单的ID]
	 * @return [type] [description]
	 */
    public function getPreOrder($id = '')
    {
    	(new IdMustBePositiveIntegerValidate())->getCheck();

    	$pay = new PayService($id);
    	return show(1, 'success', $pay->pay());    
    }

        
    /**
     * [receiveNotify 微信调用自己服务器的API，回调通知]
     * @Author xioahu
     * @url    /  redi_notify
     * @http   /
     * @return [type] [description]
     */
    public function redirectNotify()
    { 
        //通知频率为15/15/30180/1800/1800/3600
        //
        //1、检测库存量 超卖
        //2、更新status状态
        //3、减库存
        //如果成功处理，我们返回微信成功处理，否则我们需要返回没有成功处理
        //POST请求  xmlg格式发送数据 不会携带参数
        $notify = new WxNotify();
        $notify->Handle();
    }

    /**
     * [receiveNotify 测试断点调式]
     * @Author xioahu
     * @url    / api
     * @http   /
     * @return [type] [description]
     */
    public function receiveNotify()
    {
        //通知频率为15/15/30180/1800/1800/3600
        //
        //1、检测库存量 超卖
        //2、更新status状态
        //3、减库存
        //如果成功处理，我们返回微信成功处理，否则我们需要返回没有成功处理
        //POST请求  xmlg格式发送数据 不会携带参数
        // $notify = new WxNotify();
        // $notify->Handle();

        $xmlData = file_get_contents('php://input');
        $result = curl_post_raw('http://www.cshx-23.com/api/v1/pay/redi_notify?XDEBUG_SESSION_START=13133',
           $xmlData);
    }
}