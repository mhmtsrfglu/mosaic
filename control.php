<?php
/**
 * User: mehmetserefoglu
 * Date: 21.10.2017
 * Time: 16:12
 * github: github.com/mhmtsrfglu
 */
require "database.php";
require "lib/Functions.php";

$func = new Functions;

if(isset($_GET) && (isset($_GET["token"]) && isset($_GET["tag"]))){

    $tagid = $func->xssClear(intval($_GET["tag"]));
    $token = $func->xssClear($_GET["token"]);

    $tag_table = $db->prepare("select * from tags inner join grid on tags.tag_id = grid.tag_id where tags.tag_id = :tagid and tags.token=:token");
    $tag_table->bindParam(":tagid",$tagid,PDO::PARAM_INT);
    $tag_table->bindParam(":token",$token,PDO::PARAM_STR);
    $chk = $tag_table->execute();

    if($chk === true){
        $data = $tag_table->fetchAll(PDO::FETCH_ASSOC);
        if(count($data) == 0){
            die("Access Denied");
        }

    }else{
        die("Query Error");
    }
}else{
    die("Access Denied");
}
?>