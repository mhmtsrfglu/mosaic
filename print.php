<?php
/**
 * Created by PhpStorm.
 * User: mehmetserefoglu
 * Date: 23.10.2017
 * Time: 15:14
 * github: github.com/mhmtsrfglu
 */

require "lib/Functions.php";
require "database.php";

$func = new Functions;

$get = "bd00068521d30f6e6a05672236769e12";
$print = "3474573b73baa6d25bf5fbd452a6ba44";

if(isset($_GET) && (isset($_GET["tag"]) && isset($_GET["token"]))){
    $token = $func->xssClear($_GET["token"]);
    $tag = intval($func->xssClear($_GET["tag"]));

    if($token == $get ){
        $arr = array();
        $find = $db->prepare("select id,photo_name,tag_id,printed from images where tag_id=:tag and printed=0");
        $find->bindParam(":tag",$tag,PDO::PARAM_INT);
        $find->execute();
        $fetch = $find->fetchAll(PDO::FETCH_ASSOC);
        $url = "/instagram_api/photos/".$tag."/";
        foreach ($fetch as $key => $val){
            $arr[] = array(
                "photo_id" => $val["id"],
                "url" => $url.$val["photo_name"],
            );
        }

        echo json_encode(array("photos" => $arr));


    }else if($token == $print && isset($_GET["photoid"])){
        $photoid = intval($func->xssClear($_GET["photoid"]));
        $update = $db->prepare("update images set printed=1 where id=:pid");
        $update->bindParam("pid",$photoid,PDO::PARAM_INT);
        $chk = $update->execute();
        if($chk === true){
            echo json_encode(array("query" => true));
        }else{
            echo json_encode(array("query" => false));
        }

    }else{
        die("Access Denied");
    }

}else{
    die("Access Denied");
}