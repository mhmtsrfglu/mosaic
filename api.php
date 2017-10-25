<?php
/**
 * Created by PhpStorm.
 * User: mehmetserefoglu
 * Date: 18.10.2017
 * Time: 17:40
 */

require "database.php";
require "lib/Functions.php";
require "lib/Upload.php";

$func = new Functions();


if(isset($_POST)){

    $hashtag = $func->xssClear($_POST["hashtag"]);
    $width = $func->xssClear(intval($_POST["width"]));
    $height= $func->xssClear(intval($_POST["height"]));
    $opacity= $func->xssClear(intval($_POST["opacity"]));

    list($w,$h,$t,$attr) = getimagesize($_FILES["mainphoto"]["tmp_name"]);

    if($width / $height == $w / $h){
        //fiziksel foto için her bir fotonun witdh ve hight değerleri
        echo $func->convertTopx(4,72); echo "<br>";

        echo $width." cm = ". ceil($func->convertTopx($width,72))." px"; echo "<br>";
        echo $height." cm = ". ceil($func->convertTopx($height,72))." px";echo "<br>";

        // x eksenine kaç fotoğrafın denk geldiği
        echo  "count x = ".$physw = ceil($func->convertTopx($width,72) / $func->convertTopx(4,72));echo "<br>";
        // y eksenine kaç fotoğrafın denk geldiği
        echo   "count y = ".$physh = ceil($func->convertTopx($height,72) / $func->convertTopx(4,72));echo "<br>";

        echo "Big photo width = ".$w; echo "<br>";
        echo "Big photo height = ".$h; echo "<br>";

        //oluşan son resimdeki küçük fotoğraf boyutları
        echo "Small photo width = ".$smallpht_w = ceil($w / ($width /4)); echo "<br>";
        echo "Small photo height = ".$smallpht_h = ceil($h / ($height /4)); echo "<br/>";

        echo "Total image count = ".$phtcount = $physw*$physh; echo "<br/>";

        $img = new Upload($_FILES["mainphoto"]);
        if($img->uploaded){
            $img->allowed = array ( 'image/*' );
            $img->file_new_name_body = substr(md5(microtime()),rand(1,5),rand(6,10)).$hashtag;
            $img->process(__DIR__."/photos/mainphoto/");
            if($img->processed){
                $token = md5(sha1(md5(uniqid(microtime()))));
                $im = imagecreatetruecolor($w,$h);
                imagecolorallocate($im,255,255,255);
                imagecolorallocate($im,0,0,0);
                $img_name_black = "black_".$img->file_dst_name;
                imagejpeg($im,__DIR__."/photos/mainphoto/".$img_name_black);
                $add_db = $db->prepare("insert into tags set 
                  tagname = :tag,
                  imagename = :imgname,
                  process_photo = :photo,
                  width = :w,
                  height = :h,
                  smallpht_w = :smlphtw,
                  smallpht_h = :smlphth,
                  count_x = :cx,
                  count_y = :cy,
                  photo_count = :phtcnt,
                  token = :token,
                  opacity =:opa
                ");
                $add_db->bindParam(":tag",$hashtag,PDO::PARAM_STR);
                $add_db->bindParam(":imgname",$img->file_dst_name,PDO::PARAM_STR);
                $add_db->bindParam(":photo",$img_name_black,PDO::PARAM_STR);
                $add_db->bindParam(":w",$w,PDO::PARAM_INT);
                $add_db->bindParam(":h",$h,PDO::PARAM_INT);
                $add_db->bindParam(":smlphtw",$smallpht_w,PDO::PARAM_INT);
                $add_db->bindParam(":smlphth",$smallpht_h,PDO::PARAM_INT);
                $add_db->bindParam(":cx",$physw,PDO::PARAM_INT);
                $add_db->bindParam(":cy",$physh,PDO::PARAM_INT);
                $add_db->bindParam(":phtcnt",$phtcount,PDO::PARAM_INT);
                $add_db->bindParam(":token",$token,PDO::PARAM_STR);
                $add_db->bindParam(":opa",$opacity,PDO::PARAM_INT);
                $chk = $add_db->execute();
                if($chk === true){

                    imagedestroy($im);

                    $find = $db->prepare("select tag_id,tagname,token from tags where tag_id=:tgname");
                    $find->bindParam(":tgname",$db->lastInsertId(),PDO::PARAM_INT);
                    $find->execute();
                    $tag_inf = $find->fetch(PDO::FETCH_ASSOC);

                    $array = $func->createArray($physw,$physh,$smallpht_w,$smallpht_h);
                    $c = 0;
                    foreach ($array as $key => $val){
                        $creategrid = $db->prepare("insert into grid set 
                          tag_id = :tagid,
                          indexnumber = :indexnumber,
                          x=:x,
                          y=:y
                        ");
                        $creategrid->bindParam(":tagid",$tag_inf["tag_id"],PDO::PARAM_STR);
                        $creategrid->bindParam(":indexnumber",$key,PDO::PARAM_INT);
                        $creategrid->bindParam(":x",$val["x"],PDO::PARAM_INT);
                        $creategrid->bindParam(":y",$val["y"],PDO::PARAM_INT);
                        $c = $creategrid->execute();
                    }
                    if($c === true){
                        header("Location: live.php?token=".$token."&tag=".$tag_inf["tag_id"]);
                    }
                }else{
                    echo "Error";
                }
            }
    }

    }else{
        header("Location : index.php?error");
    }
}