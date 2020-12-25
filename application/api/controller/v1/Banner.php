<?php
namespace app\api\controller\v1;

use think\Exception;
use app\api\validate\IdMustBePositiveIntegerValidate;
use app\api\model\Banner as BannerModel;

use app\lib\exception\BannerMissException;

class Banner
{   
    /**
     * [getBanner 获取指定in的banner信息 轮播图]
     * @Author xioahu
     * @url    /banner/:id
     * @http   GET
     * @param  [type] $id [banner的id]
     * @return [type]     [description]
     */
	public function getBanner($id)
	{
        (new IdMustBePositiveIntegerValidate())->getCheck();
      
        $bannerm = BannerModel::getBannerByID($id);
        if(!$bannerm){
            throw new BannerMissException();
        }
        return json($bannerm);
	}
}