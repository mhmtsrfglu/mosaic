<html>
    <head>
        <title>Presstagram Panel</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="assets/css/custom.css" type="text/css" />
    </head>

    <body>
        <div class="main container">
            <h1 class="text-center">Presstagram</h1>
            <div class="inline_container">
                <form id="createpano" action="api.php" class="form-inline" method="post" enctype="multipart/form-data">


                    <div class="form-group">
                        <label for="hashtag">Hashtag</label>
                        <input type="text" name="hashtag" class="form-control" id="hashtag" placeholder="hashtag">
                    </div>
                    <hr>
                    <h4>Panonun Fiziksel Boyutu</h4>
                    <hr>
                    <div class="form-group">

                        <label for="physwidth">Width(cm) </label>
                        <input type="text" name="width" class="form-control" id="physwidth" placeholder="width">
                    </div>
                    <div class="form-group">
                        <label for="physheight">Height(cm) </label>
                        <input type="text" name="height" class="form-control" id="physheight" placeholder="height">
                    </div>
                    <hr>
                    <div class="filearea">
                        <div class="form-group">
                            <label for="opacity">Opacity </label>
                            <input type="text" name="opacity" class="form-control" id="opacity" placeholder="Opacity">
                        </div>
                        <div class="form-group">
                            <label for="imginput">Ana Görsel</label>
                            <input class="form-control" name="mainphoto" id="imginput" type="file"/>
                        </div>
                        <br>
                        <div style="margin-top: 5px" id="image_preview">
                            <img class="img-responsive" src="#" id="photo" alt="">
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-default" onclick="sbmt();" type="button">Oluştur</button>
                </form>
            </div>
        </div>



        <script
            src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous">
        </script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script>
            function readUrl(input) {

                if(input.files && input.files[0]){
                    var reader = new FileReader();

                    reader.onload = function (e) {

                        $("#photo").attr('src',e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#imginput").change(function () {
                readUrl(this);
            });

            function sbmt() {
                var hash = $("#hashtag").val().trim();
                var width = $("#physwidth").val().trim();
                var height = $("#physheight").val().trim();
                var opacity = $("#opacity").val().trim();
                var imginput = $("#imginput").val();
                var imageh = $("#photo").height();
                var imagew = $("#photo").width();
                var physdimension = width / height;
                var filedimension = imagew / imageh;

                if(hash != "" && width != "" && height != "" && imginput != "" && opacity != ""){

                    if(physdimension == filedimension){
                        $("#createpano").submit();
                    }else{
                        alert("Resim oranları uyuşmuyor. Lütfen farklı bir resim seçin yada seçili olan resmi değiştirin");
                    }

                }else{
                    alert("Lütfen tüm alanları doldurun");
                }
            }

        </script>
    </body>
</html>