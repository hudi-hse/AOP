<?php
namespace app\api\validate;

use app\api\validate\PublicValidate;


/**
 * 验证ID必须是正整数
 */
class IdMustBePositiveIntegerValidate extends PublicValidate
{
    protected $rule = [
        'id' => 'require|isValidateIn'
    ];

     protected $message = [
        'id' => 'id必须是正整数'
    ];

}