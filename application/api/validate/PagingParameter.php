<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/20
 * Time: 0:10
 */

namespace app\api\validate;


class PagingParameter extends PublicValidate
{
    protected $rule = [
        'page' => 'isValidateIn',
        'size' => 'isValidateIn'
    ];

    protected $message = [
        'page' => '1111分page页参数必须是正整数',
        'size' => '11111分size页参数必须是正整数'
    ];
}