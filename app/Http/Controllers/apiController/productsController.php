<?php
namespace App\Http\Controllers\apiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class productsController extends Controller{
private $productArray = array();

public function getproducts(Request $request){
    // dd($request);
    $where = '';
    $CID= $request->input('CID');
    if(null!==($request->input('CID'))){
        $where = ' WHERE pc.categories_id =  '.$CID.' ';
    }
  
    $items = DB::select("SELECT 
    p.products_id ,
    pd.products_name ,
    p.products_image , 
    p.products_slug ,
    cd.categories_name ,
    pc.categories_id ,
    (CASE  WHEN price_prefix = '+' THEN products_price + options_values_price WHEN price_prefix = '-' THEN products_price- options_values_price ELSE products_price END ) as PP ,
   ic.path,
   ic.height ,
   ic.width,
    group_concat(CONCAT(products_options_name,':',products_options_values_name)) attributes_metadata
FROM
    ecommerce_cms.products p
        
        INNER JOIN
    ecommerce_cms.products_to_categories pc ON pc.products_id = p.products_id
        INNER JOIN
    ecommerce_cms.products_description pd ON pd.products_id = p.products_id
    INNER JOIN 
    ecommerce_cms.categories c ON pc.categories_id = c.categories_id
    INNER JOIN 
    ecommerce_cms.categories_description cd ON cd.categories_id = c.categories_id
    left JOIN 
    ecommerce_cms.products_attributes PA ON PA.products_id = P.products_id
	LEFT JOIN 
	ecommerce_cms.products_options PO on PA.options_id = PO.products_options_id 
    left join
    ecommerce_cms.images i on i.id = p.products_image
    left join
    ecommerce_cms.image_categories ic  on i.id = ic.image_id
    LEFT JOIN 

	ecommerce_cms.products_options_values PV on PA.options_values_id= PV.products_options_values_id 
    $where
group by products_id 
"); 
// dd($items);
     $productObject = array();
     $productArray = array(); //object
     $final_product_array = array();
//     $p=0;
//    $items["products_id":47,"products_name":"M_WOOL_jacket","products_image":"551","PP":"1000.00",'name','attribute'];
        foreach($items as $item){
            $productArray['category'] = $item->categories_name;
            $productArray['category_ids'] = $item->categories_id;
            $productArray['id'] = $item->products_id;
            $productArray['name'] = $item->products_name;
            $productArray['image'] = $item->path;
            $productArray['height'] = $item->height;
            $productArray['width'] = $item->width;
            $productArray['regular_price'] = $item->PP;
            $productArray['sku'] = $item->products_slug;
            $productArray['slug'] = $item->products_slug;
            $productArray['url_path'] = strtolower($item->products_name)."-".$item->products_id;

            $productArray['attributes_metadata'] = $this->prepareAttributeObject($item->attributes_metadata);
           
            // array_push($final_product_array,$productArray,$this->prepareproductObject($item));
        array_push($final_product_array,$productArray);
        }   
         echo json_encode($final_product_array);
        //  dd($final_product_array);
    }
    

private function prepareAttributeObject($record){
    if ($record == null) return [];  
    $pa = (explode(',',$record));
    $f_a = array();

    foreach($pa as $v){
	    $o = explode(":",$v);
   
        $key = $o[0];
        $value = $o[1];
    
    if (isset($f_a[$key]) && is_array($f_a[$key]) ) {
    	
    	 array_push ($f_a[$key],$value);
        }
    else{

      $f_a[$key] = [];
      array_push ($f_a[$key],$value);
        }
    }
    return $f_a;
 // echo json_encode($f_a);



  }


    //  
}

