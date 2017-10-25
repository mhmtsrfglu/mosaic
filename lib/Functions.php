<?php
/**
 * Created by PhpStorm.
 * User: mehmetserefoglu
 * Date: 17.10.2017
 * Time: 17:09
 */

require "ImageResize.php";
use \Eventviva\ImageResize;

class Functions
{

    public function getContent($tag){
        $data = array();
        if(!empty(trim($tag))){
            $json_output = file_get_contents("https://www.instagram.com/explore/tags/".$tag."/?__a=1");
            $data = json_decode($json_output);
        }
        return $data;
    }

    public function xssClear($value){
        return htmlspecialchars(strip_tags(trim($value)));
    }

    public function check_photo($photoid){
        global $db;
        $chk = $db->prepare("select photo_id from images where photo_id = :pid");
        $chk->bindParam(":pid",$photoid,PDO::PARAM_INT);
        $chk->execute();
        if($chk->rowCount()){
            return true;
        }else{
            return false;
        }
    }

    public function resizeImage($imagename,$w,$h){
        $image = new ImageResize(dirname(__DIR__) . "/photos/" .$imagename);
        $image->resizeToHeight($h);
        $image->resizeToWidth($w);
        $image->save(dirname(__DIR__). "/photos/".$imagename);
    }

    public function createArray($cx,$cy,$w,$h){
        $arr = array();
        $k = 0;
        $x = 0;
        $y = 0;
        for($i = 0;$i<$cy;$i++){
            for($j = 0;$j<$cx;$j++){
                $arr[$k] = array("x" => $x,"y" => $y);
                $x+=$w;
                $k++;
            }
            $x=0;
            $y+=$h;
        }
        return $arr;
    }

    public function convertTopx($cm,$dpi){
        $res = ($cm * $dpi) / 2.54;
        return ceil($res);
    }

    public function actionNewmosaic($tagid,$smallphoto,$photoid){

        global $db;
        ini_set('memory_limit', '-1');
        //null grids
        $nullgrids = $db->prepare("select * from grid where tag_id={$tagid} and filled=0 order by RAND() limit 1");
        $nullgrids->execute();
        $getDatas = $nullgrids->fetchAll(PDO::FETCH_ASSOC);

        if(count($getDatas) > 0){
            $tagtable = $db->prepare("select * from tags where tag_id = {$tagid}");
            $tagtable->execute();
            $tagdata = $tagtable->fetch(PDO::FETCH_ASSOC);

            $blackphoto = imagecreatetruecolor($tagdata["width"],$tagdata["height"]);
            imagecolorallocate($blackphoto,255,255,255);
            imagecolorallocate($blackphoto,0,0,0);

            //photos
            $bigimage = dirname(__DIR__)."/photos/mainphoto/".$tagdata["imagename"];
            $sbigimage = imagecreatefromjpeg($bigimage);

            //blackphoto
            $processphoto = dirname(__DIR__)."/photos/mainphoto/".$tagdata["process_photo"];
            $sprocessphoto = imagecreatefromjpeg($processphoto);

            //small photo
            $smallpht = dirname(__DIR__)."/photos/".$smallphoto;
            $ssmallpht = imagecreatefromjpeg($smallpht);



            imagecopymerge($blackphoto, $ssmallpht, $getDatas[0]["x"], $getDatas[0]["y"], 0, 0, $tagdata["smallpht_w"], $tagdata["smallpht_h"], 100);

            imagecopymerge($blackphoto, $sbigimage, 0, 0, 0, 0, $tagdata["width"], $tagdata["height"], $tagdata["opacity"]);

            $imglast = imagecrop($blackphoto,['x' =>  $getDatas[0]["x"], 'y' => $getDatas[0]["y"], 'width' => $tagdata["smallpht_w"], 'height' => $tagdata["smallpht_h"]]);

            if(!file_exists(dirname(__DIR__)."/photos/".$tagid)){
                mkdir(dirname(__DIR__)."/photos/".$tagid,0777);
            }

            imagecopymerge($sprocessphoto, $imglast, $getDatas[0]["x"], $getDatas[0]["y"], 0, 0, $tagdata["smallpht_w"], $tagdata["smallpht_h"], 100);
            unlink(dirname(__DIR__)."/photos/mainphoto/".$tagdata["process_photo"]);

            imagejpeg($imglast,dirname(__DIR__)."/photos/".$tagid."/".$smallphoto);
            imagejpeg($sprocessphoto,dirname(__DIR__)."/photos/mainphoto/".$tagdata["process_photo"]);
            chmod(dirname(__DIR__)."/photos/".$tagid."/".$smallphoto,0777);
            chmod(dirname(__DIR__)."/photos/mainphoto/".$tagdata["process_photo"],0777);
            $updategrid = $db->prepare("update grid set photo_id=?,filled=? where grid_id=?");
            $updategrid->execute(array($photoid,1,$getDatas[0]['grid_id']));
            $updateimg = $db->prepare("update images set grid_id=? where photo_id=?");
            $updateimg->execute(array($getDatas[0]['grid_id'],$photoid));

            imagedestroy($sbigimage);
            imagedestroy($sprocessphoto);
            imagedestroy($imglast);
            imagedestroy($blackphoto);
        }


    }

    public function actionNewmosaicfromfile($tagid,$smallphoto,$photoid){

        global $db;
        ini_set('memory_limit', '-1');
        //null grids
        $nullgrids = $db->prepare("select * from grid where tag_id={$tagid} and filled=0 order by RAND() limit 1");
        $nullgrids->execute();
        $getDatas = $nullgrids->fetchAll(PDO::FETCH_ASSOC);

        if(count($getDatas) > 0){

            $tagtable = $db->prepare("select * from tags where tag_id = {$tagid}");
            $tagtable->execute();
            $tagdata = $tagtable->fetch(PDO::FETCH_ASSOC);

            $blackphoto = imagecreatetruecolor($tagdata["width"],$tagdata["height"]);
            imagecolorallocate($blackphoto,255,255,255);
            imagecolorallocate($blackphoto,0,0,0);

            //photos
            $bigimage = dirname(__DIR__)."/photos/mainphoto/".$tagdata["imagename"];
            $sbigimage = imagecreatefromjpeg($bigimage);

            //blackphoto
            $processphoto = dirname(__DIR__)."/photos/mainphoto/".$tagdata["process_photo"];
            $sprocessphoto = imagecreatefromjpeg($processphoto);

            //small photo
            $smallpht = dirname(__DIR__)."/photos/".$smallphoto;
            $ssmallpht = imagecreatefromjpeg($smallpht);



            imagecopymerge($blackphoto, $ssmallpht, $getDatas[0]["x"], $getDatas[0]["y"], 0, 0, $tagdata["smallpht_w"], $tagdata["smallpht_h"], 100);

            imagecopymerge($blackphoto, $sbigimage, 0, 0, 0, 0, $tagdata["width"], $tagdata["height"], $tagdata["opacity"]);

            $imglast = imagecrop($blackphoto,['x' =>  $getDatas[0]["x"], 'y' => $getDatas[0]["y"], 'width' => $tagdata["smallpht_w"], 'height' => $tagdata["smallpht_h"]]);

            if(!file_exists(dirname(__DIR__)."/photos/".$tagid)){
                mkdir(dirname(__DIR__)."/photos/".$tagid);
            }

            imagecopymerge($sprocessphoto, $imglast, $getDatas[0]["x"], $getDatas[0]["y"], 0, 0, $tagdata["smallpht_w"], $tagdata["smallpht_h"], 100);
            unlink(dirname(__DIR__)."/photos/mainphoto/".$tagdata["process_photo"]);
            imagejpeg($sprocessphoto,dirname(__DIR__)."/photos/mainphoto/".$tagdata["process_photo"]);

            $random = rand(1,400);
            $newname = "9".$random.$tagid.$smallphoto;
            $newid = "9".$random.$tagid.$photoid;

            imagejpeg($imglast,dirname(__DIR__)."/photos/".$tagid."/".$newname);

            $updategrid = $db->prepare("update grid set photo_id=?,filled=? where grid_id=?");
            $updategrid->execute(array($newid,1,$getDatas[0]['grid_id']));
            $updateimg = $db->prepare("insert into images set photo_name=?,photo_id=?,tag_id=?,grid_id=?");
            $updateimg->execute(array($newname,$newid,$tagid,$getDatas[0]['grid_id']));

            imagedestroy($sbigimage);
            imagedestroy($sprocessphoto);
            imagedestroy($imglast);
            imagedestroy($blackphoto);
        }


    }

}