<?php
namespace app\api\service;

use think\Db;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\api\model\OrderProduct;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;

class Order
{   
	//接受客户端传来的购买的商品列表
	protected $oProducts;
	//数据库数据的商品列表
	protected $Products;
    //用户下单的ID(用户的ID)
	protected $uid;


	public function place($uid, $oProducts)
	{
      // oProducs和Producs 作对比
      // oProducs从数据库查询出来的
      $this->oProducts = $oProducts;
      $this->Products = $this->getProductsByOrder($oProducts);
      $this->uid = $uid;
      $status = $this->getOrderStatus();

      if (!$status['pass']) {
      	$status['order_id'] = -1;
      	return $status;
      } 

      //开始创建订单
      $orderSnap =  $this->snapOrder($status);
      //写入订单
      $order = $this->createOrder($orderSnap);
      
      $order['pass'] = true;
 
      return $order;

	}

    /**
     * [createOrder 生成订单，写入数据库]
     * @Author xioahu
     * @url    /
     * @http   /
     * @return [type] [description]
     */
    private function createOrder($snap)
    {   
        // Db::startTrans(); //开启事务
        try {
            $orderNo = $this->makeOrderNo();

            $order = new \app\api\model\Order();
           
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count =$snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();

           
            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            $list = [
                ['name'=>'thinkphp','email'=>'thinkphp@qq.com'],
                ['name'=>'onethink','email'=>'onethink@qq.com']
            ];
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);

            // Db:commit(); // 提交事务

            $orderDate = [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];

            return $orderDate;
            
        } catch(Execption $e) {
            // Db::rollback(); //回滚事务
            throw $e;
        }


    }
    
    /**
     * [makeOrderNo 随机生成订单号]
     * @Author xioahu
     * @url    /
     * @http   /
     * @return [type] [description]
     */
    public static function makeOrderNo()
    {

        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $orderSn = $yCode[intval(date('Y')) - 2020] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) .substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }
    
    /**
     * [snapOrder 生成订单快照]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    private function snapOrder($status)
    {

        $snap = [
            'orderPrice' => 0,  //订单的总价格
            'totalCount' => 0,  //订单商品的总数量
            'pStatus' => [],    //订单下所有商品的状态
            'snapAddress' => null, //快照的地址
            'snapName' => '',  //
            'snapImg' => ''
        ];
        
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->Products[0]['name'];
        $snap['snapImg'] = $this->Products[0]['main_img_url'];
        if(count($this->Products) > 1) {
            $snap['snapName'] .= '等';
        }

        return $snap;

    }
    
    /**
     * [getUserAddress 或取地址信息]
     * @Author xioahu
     * @url    /
     * @http   /
     * @return [type] [地址信息]
     */
    private function getUserAddress()
    {   

        $userAddress = UserAddress::where('user_id', '=', $this->uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001,
            ]);
            
        }
        return $userAddress->toArray();
    }
    
    /**
     * [checkOrderStock 支付调用]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $orderID [订单ID]
     * @return [type]          [description]
     */
    public function checkOrderStock($orderID)
    {

        $oProducts = OrderProduct::where('order_id', '=', $orderID)->select();
        $this->oProducts = $oProducts;
        $this->Products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }
    
    /**
     * [getOrderStatus 库存量检测]
     * @Author xioahu
     * @url    /
     * @http   /
     * @return [type] [description]
     */
	private function getOrderStatus()
	{

		$status = [
		    'pass' => true,           //检测库存是否通过
		    'orderPrice' => 0,        //所有购买商品列表的总和价格
		    'totalCount' => 0,
            'pStatusArray' => []      //订单商品的详情信息
		];
		foreach ($this->oProducts as $oProducs) {
			$pStatus = $this->getProductsStatus(
				$oProducs['product_id'], $oProducs['count'], $this->Products
	        );

	        if (!$pStatus['haveStock']) {
	        	$status['pass'] = false;
	        }

	        $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['counts']; //#
	        array_push($status['pStatusArray'], $pStatus);
		}

		return $status;
	}
    /**
     * 实现对比 
     */
	private function getProductsStatus($oPIDs, $oCount, $products)
	{   

		//xxs序号
		$pIndex = -1;
		//$pstatus保存订单某一商品的详情信息
        $pStatus = [
            'id' => null,             //商品ID
            'haveStock' => false,    //商品是否有库存量
            'counts' => 0,            // #当前订单所请求的商品数量
            'price' => 0,
            'name' => '',            //商品的名称
            'totalPrice' => 0,       //当前购买某商品商品单价购买商品的数量的价格 
            "main_img_url" => null         
        ];

        for ($i = 0; $i < count($products); $i++) {
        	if ($oPIDs == $products[$i]['id']) {
        		$pIndex = $i;
        	}
        }

        if ($pIndex == -1) {
        	//客户端传递的product_id有可能不存在
        	throw new OrderException(
                [
                    'msg' => 'id为'.$oPIDs.'商品不存在，创建订单失败',
                ]
            );
        } else {
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['counts'] = $oCount;
            $pStatus['price'] = $product['price'];
            $pStatus['main_img_url'] = $product['main_img_url'];
            $pStatus['totalPrice'] = $product['price']*$oCount;
            if ($product['stock'] - $oCount >=0) {
            	$pStatus['haveStock'] = true;
            }
        }

        return $pStatus; 
	}
    
    /**
     * [getProductsByOrder 根据订单信息查找真实的商品信息]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $oProducs [客户端传来的商品列表参数]
     * @return [type]           [description]
     */
	private function getProductsByOrder($oProducs)
	{    

         // 为了避免循环查询数据库
         $oPIDs = [];
         foreach ($oProducs as $item) {
         	array_push($oPIDs, $item['product_id']);
         }
         // 根据多个find查询
         $products = Product::all($oPIDs)
             ->visible(['id', 'price', 'stock', 'name', "main_img_url"])
             ->toArray();
         return $products;
	}

}