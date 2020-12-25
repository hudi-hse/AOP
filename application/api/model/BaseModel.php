<?php
namespace app\api\model;

use think\Model;

class BaseModel extends Model
{   
        /**
     * [getUrlAttr 读取器url字段数据]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $value [数据库字段名]
     * @return [type]        [URL路径]
     */
    // public function getUrlAttr($value, $data)
    
    /**
     * [prefixImgUrl description]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $value [数据库字段名URL]
     * @param  [type] $data  [img表所有字段数据]
     * @return [type]        [description]
     */
    protected function prefixImgUrl($value, $data)
    {    
    	$findUrl = $value;
        if($data['from'] == 1) {
            return config('setting.img_prefix').$findUrl;
        }
        return $findUrl;
    }

}