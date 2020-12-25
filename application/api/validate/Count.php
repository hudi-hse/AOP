<?php
namespace app\api\validate;

use app\api\validate\PublicValidate;



class Count extends PublicValidate
{
	protected $rule = [
        'count' => 'isValidateIn|between:1,15'
    ];

    protected $message = [
        'count' => 'count参数必须为正整数，且1到15之间'
    ];
    
}