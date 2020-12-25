<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

 use think\Route;
 
 Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner', [], ['id' => '\d+']); 

 Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList'); //版本兼容
 Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne', [], ['id' => '\d+']); //开启路由完全匹配

 Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
 Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[],['id' => '\d+']); //限定参数的类型
 Route::get('api/:version/product/all', 'api/:version.Product/getProductCategoryAll');
 // 路由分组
 // Route::group('api/:version/product', function(){
 // 	Route::get('/recent', 'api/:version.Product/getRecent');
 //    Route::get('/:id', 'api/:version.Product/getOne',['http' => true],['id' => '\d+']); //限定参数的类型
 //    Route::get('/all', 'api/:version.Product/getProductCategoryAll');
 // });




 Route::get('api/:version/category/all', 'api/:version.Category/getAllCategory');

 Route::post('api/:version/token/user', 'api/:version.Token/getToken');

 Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

 Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');



 Route::post('api/:version/suaddress', 'api/:version.Address/createOrUpdateAddress');
 
 Route::get('api/:version/address', 'api/:version.Address/getUserAddress');



 Route::post('api/:version/order', 'api/:version.Order/placeOrder');

 Route::get('api/:version/order/all', 'api/:version.Order/getSummaryByUser');

 Route::get('api/:version/order/list', 'api/:version.Order/getDateList');

 Route::get('api/:version/order/paginate', 'api/:version.Order/getSummary');

 Route::post('api/:version/pay', 'api/:version.Pay/getPreOrder');

 Route::post('api/:version/pay/api', 'api/:version.Pay/receiveNotify');

 Route::post('api/:version/pay/redi_notify', 'api/:version.Pay/redirectNotify'); //测试断点调式
