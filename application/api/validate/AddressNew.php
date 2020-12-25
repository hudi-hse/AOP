<?php
namespace app\api\validate;

use app\api\validate\PublicValidate;



class AddressNew extends PublicValidate
{
	protected $rule = [
        'name' => 'require|isNotEmpty',
        'mobile' => 'require|isMobile',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'country' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty'
    ];
    
}