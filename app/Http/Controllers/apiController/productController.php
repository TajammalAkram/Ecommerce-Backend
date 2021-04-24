<?php
namespace App\Http\Controllers\apiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class productController extends Controller{
    private $productArray = array();

    public function getproduct(Request $request){
        $CID= $request->input('CID');
        $items = DB::select("SELECT 
        p.products_id,
       pd.products_name,
       p.products_image,
       p.products_price,
       p.products_slug,
       pd.products_description
        From 
        ecommerce_cms.products p
                INNER JOIN
        ecommerce_cms.products_description pd ON pd.products_id = p.products_id
        where   p.products_id = '.$CID.'
        ");
        
        $productArray = array(); //object
        $final_product_array = array();
        foreach($items as $item){

            $productArray['id'] = $item->products_id;
            $productArray['name'] = $item->products_name;
            $productArray['image'] = $item->products_image;
            $productArray['price'] = $item->products_price;
            $productArray['sku'] = $item->products_name;
            $productArray['description'] = $item->products_description;
            array_push($final_product_array,$productArray);
            
        }

        
        echo json_encode($final_product_array);
    
    }
}