<?php
namespace app\api\controller\v1;
use app\api\Controller\BaseController;
use app\api\validate\OrderValidata;
use app\api\validate\PageValidata;
use app\api\validate\PagingParameter;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderMOdel;
use app\lib\exception\OrderException;
use app\api\validate\IdMustBePositiveIntegerValidate;


class Order extends BaseController
{   
    protected $beforeActionList = [
        'chechExclusiveScope' => ['only' => 'placeOrder'],
        'chechPrimaryScope' => ['only' => 'getSummaryByUser,getDateList'],
    ];
    
    //用户选择要购买的商品，向服务端提交选择的商品相关信息
    //服务端接受到信息后，需要检测商品的库存量  
    //有库存，把订单数据存入数据库 下单成功 返回客户端消息 告诉客户端可以支付
    //调用服务器支付接口进行支付
    //还需再次检测库存量
    //服务器调用微信支付接口进行支付
    //小程序根据服务器返回的结果拉起微信支付
    //不管是支付成功还是支付失败，微信会返回我们一个支付的结果(支付结果是异步调用)
    //成功：也需要进行库存量检测
    //支付成功：进行库存量的扣除
    
    public function placeOrder()
    {   
        // (new OrderValidata())->getCheck();
        $products = input('post.products/a');
        $uid = TokenService::getCurrentUid();
        $order = new OrderService();
        $status = $order->place($uid, $products);
        return show(1, 'success', $status);
    }
    
    /**
     * [getDateList 用户订单详情信息]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getDateList($id)
    {
        (new IdMustBePositiveIntegerValidate())->getCheck(); 
           
        $listdata = OrderMOdel::get($id);

        if (!$listdata) {
            throw new OrderException();
        }

        $listdata = $listdata->hidden(['prepay_id']);

        return show(1, 'success', $listdata);
    }
    
    /**
     * [getSummaryByUser 用户订单信息列表分页]
     * @Author xioahu  think\db\Query.php
     * @url    /
     * @http   /
     * @param  integer $page [description]
     * @param  integer $size [description]
     * @return [type]        [description]
     */
    public function getSummaryByUser($page=1, $size=15)
    {
        (new PageValidata())->getCheck();
        $uid = TokenService::getCurrentUid();

        $listdata = OrderMOdel::getSSummaryList($uid, $page, $size);

        $listdata = $listdata->hidden(['prepay_id', 'snap_items', 'total_count','snap_address']);

        if ($listdata->isEmpty()) {
            // $listdata->getCurrenPage();获取当前分页的页码
            return show(1, 'success',[]);
        } else {
             return show(1, 'success', $listdata);
        }  
    }


      /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page=1, $size = 20)
    {
        (new PagingParameter())->getCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty()) {
            // return [
            //     'current_page' => $pagingOrders->currentPage(),
            //     'data' => []
            // ];

            return show(1, 'success', [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ]);
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        // return [
        //     'current_page' => $pagingOrders->currentPage(),
        //     'data' => $data
        // ];

        return show(1, 'success', [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ]);
    }


}