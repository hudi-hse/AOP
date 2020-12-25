<?php
namespace app\api\validate;

use app\api\validate\PublicValidate;



class PageValidata extends PublicValidate
{
	protected $rule = [
        'page' => 'isValidateIn',
        'size' =>'isValidateIn'
    ];

    protected $message = [
        'page' => 'page55555参数必须为正整数',
        'size' => 'size参数必须为正整数'
    ];
    
}