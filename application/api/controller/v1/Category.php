<?php
namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;




class Category
{
    public function getAllCategory()
    {
        $cate = CategoryModel::getCategory(); 
        if ($cate->isEmpty()) {
            throw new CategoryException();
        }
        return show(1, 'success', $cate);
    }
}
