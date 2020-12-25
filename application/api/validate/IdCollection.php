<?php
namespace app\api\validate;

// use app\api\validate\PublicValidate;



class IdCollection extends PublicValidate
{
	protected $rule = [
        'ids' => 'require|chechIds'
    ];

    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的多个正整数'
    ];
    
    /**
     * [chechIds 自定义验证规则]
     * @Author xioahu
     * @param  [type]  $value [传入value的值来校验]
     * @return boolean        [description]  is_numeric() 判断是否是数字 is_int()判断是否是整数
     */
    protected function  chechIds($value)
    {
        $values = explode(',', $value);
        if (empty($values)) {
            return false;
        }
        foreach ($values as $id) {
            if (!$this->isValidateIn($id)){
                return false;
            }    
        }
        return true;   
    }
}