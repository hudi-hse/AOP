<?php
namespace app\api\service;

use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Loader;
use think\Log;
use think\Db;
use think\Exception;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');



class WxNotify extends \WxPayNotify
{   
    /**
     * [NotifyProcess 覆盖微信NotifyProcess方法 微信返回支付是否成功结果]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $data [微信回调支付结果]
     * @param  [type] &$msg [description]
     * @return [ture 或者 false]
     */
    public function NotifyProcess($data, &$msg)
    {    //判断支付成功  事务
        if ($data['result_code'] == 'SUCCESS') {
            // 微信返回的订单号
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try {
                //  order_no::订单号
                //  
                //   OrderModel::where('order_no', '=', $orderNo)
                //   ->lock(true)     锁住
                //   ->find()
                //  
                $order = OrderModel::where('order_no', '=', $orderNo)->find();
                if ($order->status ==1) {
                    $orderService = new OrderService();
                    $stockStatus = $orderService->checkOrderStock($order->id);
                    //判断库存量检测成功  并发操作 库存量方法操作两次 事务解决
                    if ($stockStatus['pass']) {
                        //更新状态
                        $this->updateOrderstatus($order->id, true);
                        //更新库存量减少
                        $this->reduceStokc($stockStatus);
                    } else {
                        //更新状态
                        $this->updateOrderstatus($order->id, false);
                    }

                }
                Db::commit();
                return true;
                
            } catch (Exception $e) {
                Db::rollback();
                Log::error($ex);
                return false;
            }
        } else{
            return false;
        }
    }

    private function reduceStokc($stockStatus)
    {
        foreach ($$stockStatus as $singlePStatus) {
            Product::where('id', '=', $singlePStatus['id'])->setDec('stock', $singlePStatus['count']);
        }
    }

    private function updateOrderstatus($orderID, $success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OF;
        OrderModel::where('id', '=', $orderID)->update(['status' => $status]);
    }
}