<?php
namespace app\api\controller;

use think\Controller;
use app\api\service\Token as TokenService;


class BaseController extends Controller 
{
	public function chechPrimaryScope()
    {   
        TokenService::needPrimaryScpe();
    }
    
    public function chechExclusiveScope()
    {
        TokenService::needExclusiveScpe();
    }
    
}