<?php
namespace app\api\validate;

use app\api\validate\PublicValidate;



class TokenGet extends PublicValidate
{
	protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => 'count参数必须有值'
    ];
    


   
}