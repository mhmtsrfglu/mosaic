<?php
/**
 * Created by PhpStorm.
 * User: mehmetserefoglu
 * Date: 21.10.2017
 * Time: 15:40
 * github: github.com/mhmtsrfglu
 */
require "control.php";
//$jsondata = array();
$jsondata = $func->getContent($data[0]["tagname"]);
?>
<html>
<head>
    <title>Live Page</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="assets/css/custom.css" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
</head>
<body>
        <?php

        foreach($jsondata->tag->media->nodes as $value) :

            if($func->check_photo($value->id) === false){
                $imagename =$value->id.".jpg";

                $insert = $db->prepare("insert into images set photo_id =:pid,photo_name=:pname,tag_id=:tagid");
                $insert->bindParam(":pid",$value->id,PDO::PARAM_INT);
                $insert->bindParam(":pname",$imagename,PDO::PARAM_STR);
                $insert->bindParam(":tagid",$data[0]["tag_id"],PDO::PARAM_INT);
                if($insert->execute()){
                    $img = __DIR__."/photos/".$imagename;
                    file_put_contents($img,file_get_contents($value->thumbnail_src));
                    $func->resizeImage($imagename,$data[0]["smallpht_w"],$data[0]["smallpht_h"]);
                    $func->actionNewmosaic($data[0]["tag_id"],$imagename,$value->id);
                }
            }
            ?>

        <?php endforeach; ?>

        <div class="container">


            <hr>
            <div class="col-md-6">
                <?php
                $added=$db->prepare("select count(filled) from grid where tag_id = :tg and filled=1");
                $added->bindParam(":tg",$data[0]["tag_id"],PDO::PARAM_INT);
                $added->execute();
                $cnt = $added->fetchColumn();
                ?>

                <h4>Toplam Fotoğraf Sayısı : <?=$data[0]["photo_count"]?></h4>
                <h4>Eklenen Fotoğraf Sayısı : <?=$cnt?></h4>
                <h4>Kalan Fotoğraf Satısı : <?=$data[0]["photo_count"] - $cnt?></h4>
            </div>
            <div class="clear-fix"></div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Eklemek istediğiniz resim adedi : </label>
                    <input style="margin-bottom: 3px" class="form-control num" type="text" name="countpht" max="<?=$data[0]["photo_count"] - $cnt?>">
                    <button type="button" class="btn btn-primary mybutton" onclick="fillgrid(<?=$data[0]["tag_id"]?>)">Ekle</button>
                </div>
            </div>
            <hr>

            <div class="col-md-12">
                <div class="loader center-block text-center">
                    <img style="position: absolute; top: 50%; align-self: center" src="assets/icons/ajax-loader.gif" alt="">
                </div>
                <img class="img-responsive" src="photos/mainphoto/<?=$data[0]["process_photo"]?>" alt="">
            </div>

        </div>


        <script
                src="https://code.jquery.com/jquery-3.2.1.min.js"
                integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
                crossorigin="anonymous">
        </script>
        <script src="assets/js/bootstrap.min.js"></script>
        <?php if($data[0]["photo_count"] != $cnt): ?>
            <script>
                var time = null;
                $(".loader").hide();
                function fillgrid(tagid) {
                    clearTimeout(time);
                    $(".loader").show();
                    $('.mybutton').attr("disabled", true);
                    var num = $(".num").val();
                    var max = <?=$data[0]["photo_count"] - $cnt?>;
                    if(num <= max){
                        var data={
                            tag:tagid,
                            token:"67bd8dbe2a288fcd6781d5c866a91104",
                            num: num,
                            max:<?=$data[0]["photo_count"] - $cnt?>
                        };
                        var dataString = 'data='+encodeURIComponent(JSON.stringify(data));

                        $.ajax({
                            type: "POST",
                            dataType:'json',
                            url: "fill.php",
                            data: dataString,
                            success: function(data)
                            {
                                if(data.status === true){
                                    $(".loader").hide();
                                    $('.mybutton').attr("disabled", false);
                                    window.location.reload();
                                }else{
                                    $(".loader").hide();
                                    $('.mybutton').attr("disabled", false);
                                }
                            }
                        });
                    }else{
                        alert("Kalan fotoğraf sayısından büyük bir değer giremezsiniz");
                    }

                }

              $(document).ready(function(){

                    time = setTimeout(function(){
                        window.location.reload();
                    },15000);
                });
            </script>
        <?php endif; ?>
</body>
</html>

