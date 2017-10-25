<?php
/**
 * Created by PhpStorm.
 * User: mehmetserefoglu
 * Date: 24.10.2017
 * Time: 14:26
 * github: github.com/mhmtsrfglu
 */

require "database.php";
require "lib/Functions.php";

$func = new Functions;
$token_main = "67bd8dbe2a288fcd6781d5c866a91104";
if(isset($_POST) && (isset($_POST["data"]))){
    $vla = false;
    $data = json_decode(urldecode($_POST["data"]));
    $num = $func->xssClear(intval($data->num));
    $token = $func->xssClear($data->token);
    $tagid = $func->xssClear(intval($data->tag));

    if(!empty($num) && (!empty($token) && $token_main == $token) && !empty($tagid)){
    //null grids
        $nullgrids = $db->prepare("select tag_id,photo_name,photo_id from images where tag_id={$tagid} order by RAND() limit {$num}");
        $nullgrids->execute();
        $getDatas = $nullgrids->fetchAll(PDO::FETCH_ASSOC);
        if(count($getDatas)>0){
            foreach ($getDatas as $key => $val) {
                $func->actionNewmosaicfromfile($tagid,$val["photo_name"],$val["photo_id"]);
                $vla = true;
            }
        }else{
            $vla = false;
        }
    }
    if($vla === true){
        echo json_encode(array("status" => true));
    }else{
        echo json_encode(array("status" => false));
    }
}else{
    die("Access denied");
}