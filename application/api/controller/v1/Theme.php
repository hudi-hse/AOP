<?php
namespace app\api\controller\v1;

use app\api\validate\IdCollection;
use app\api\validate\IdMustBePositiveIntegerValidate;

use app\api\model\Theme as ThemeModel;

use app\lib\exception\ThemeException;

class Theme
{
   public function getSimpleList($ids = '')
   {   
       // (new IdCollection())->getCheck();
       $vali = new IdCollection();
       $vali->getCheck();

       $ids = explode(',', $ids);
       $result = ThemeModel::getThemeById($ids);
       if ($result->isEmpty()) {
           throw new ThemeException();
        }
        return show(5, 'success', $result);
   }

   public function getComplexOne($id)
   {

       (new IdMustBePositiveIntegerValidate())->getCheck();
       $theme = ThemeModel::getThemeWithProducts($id);
       if (!$theme) {
           throw new ThemeException();
       }
       return show(5, 'success', $theme);
   }
}
