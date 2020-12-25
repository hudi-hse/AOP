<?php
namespace app\api\model;

class Image extends BaseModel
{   
    protected $hidden = ['id','from','delete_time','update_time'];
    
    /**
     * [getUrlAttr 重新定义读取器名称定义是根据数据库的字段来定义读取器的名称的]
     * @Author xioahu
     * @url    /
     * @http   /
     * @param  [type] $value [URL数据]
     * @param  [type] $data  [整个表的数据]
     * @return [type]        [URL拼接路径]
     */
    public function getUrlAttr($value, $data)
    {
    	return $this->prefixImgUrl($value, $data);
    }
}