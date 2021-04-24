<?php

namespace App\Http\Controllers\apiController;
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use DB;

class categoryController extends Controller{
private $categoryArray = array();

public function getCategories(Request $request){

//    $pl = json_decode(request()->getContent(), true);
//    echo $pl;

//    return response()->json("{'success'200,'data':$pl,'ErrorMessage':''}");
//    return response()->json("{'success':600,'data':'','ErrorMessage':'Server Not Responding '}");

    // return response()->json([ 'message' => 'Page Not Fond','state' => '501']);
//     // // dd( $request->all());
//    $w = $this->createWhereFromArray($pl);
//     echo "SELECT parent_id ,c.categories_id id, categories_name `name` ,categories_status is_active,categories_image,categories_icon
//     FROM
//     ecommerce_cms.categories c
//     INNER JOIN
//     ecommerce_cms.categories_description cd ON c.categories_id = cd.categories_id WHERE $w ORDER BY c.categories_id";
// //    // echo $w;
// return ;
$items = DB::select("SELECT
parent_id ,c.categories_id id, categories_name `name` ,categories_status is_active,categories_image,categories_icon
FROM
ecommerce_cms.categories c
INNER JOIN
ecommerce_cms.categories_description cd ON c.categories_id = cd.categories_id ORDER BY c.categories_id;");


$categoryObject = array();
$categoryArray = array();
$c=0;
foreach($items as $item){

    if(count($categoryArray) == 0 && $item->parent_id != 0 ){
    // print_r($categoryArray);
        array_push($categoryArray,$this->prepareCategoryObject($item));
    }   
    //print_r($categoryArray);

    if($item->parent_id != 0){
    //echo var_dump($categoryArray);
        $categoryArray = $this->locateAndAddchildren_datas($categoryArray , $item , $item->parent_id);
    //dd($categoryArray);
    //$item['children_data'] = array();
    }
    else{
        array_push($categoryArray,$this->prepareCategoryObject($item));

    }
//echo "<br>". json_encode($this->categoryArray) ."<br><br >";
$c++;

}

echo json_encode($categoryArray);


}
private function locateAndAddchildren_datas(&$array,$item,$parentIdtoLocate){

// $cat_array = ($index == 0)? $this->categoryArray : $this->categoryArray[$index]
// $counter = isset($counter)?$counter:0
    foreach( $array as $index => &$object){

        if(isset($object['children_data']) && is_array($object['children_data']) && count($object['children_data'])!= 0){
            $this->locateAndAddchildren_datas($object['children_data'],$item,$parentIdtoLocate);
        }

        if($object['id'] == $parentIdtoLocate){

            $category_object= $this->prepareCategoryObject($item,$object['name']);
            array_push($object['children_data'],$category_object);
        }

    }
    return $array;

}

private function prepareCategoryObject($record,$parentName=""){
    // dd($record);
    $items = ['name' ,'id','slug','parent_id','is_active','categories_image','categories_icon','url_path','url_key' ];
    
    $CO = array();
    foreach($items as $item){
        if ($item == 'slug' ) $categoryObject[$item] = strtolower($record->name)."-".$record->id;
        if($item == 'url_key' ) $categoryObject[$item] = strtolower($record->name)."-".$record->id;
        if($item == 'url_path' ){ 
           // $categoryObject[$item] = "";
            // if record is parent
            if($record->parent_id == 0){
                $categoryObject[$item] = strtolower($record->name."/". $record->name."-".$record->id); 
            }
            // if record is c   hild
            else{
                
                $categoryObject[$item] = strtolower($parentName."/".$record->name."/". $record->name."-".$record->id);
            }
        // women/tops-women/hodies-women.html
        } 
        if( $item != 'url_path' && $item != 'url_key' && $item != 'slug' )
        $categoryObject[$item] = $record->$item;
    }

    $categoryObject['children_data'] = array();
        return $categoryObject;
    }
}
    // public function createWhereFromArray($array,$seperator="AND"){

	// 	$where="";
	// 	foreach ($array as $key => $value) {
			
	// 		$where.=" ".$key."='".$value."'"." ".$seperator;

	// 	}

	// 	$sep=$seperator." ";
	// 	return rtrim($where,$sep);

	// }  

// }


?>