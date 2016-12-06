<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <title>Trunited</title>
    <script type="text/javascript" src="./libs/jquery-1.5.2.min.js"></script>


    <script src="./libs/jquery-1.12.4.js"></script>
    <script src="./libs/jquery-ui.js"></script>

    <link rel="stylesheet" type="text/css" href="./libs/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="./libs/bootstrap/css/bootstrap.min.css"/>

    <script type="text/javascript">

        $( function() {

            $( "#people" ).autocomplete({
                source: "chart_ws.php?type=people",

                minLength: 2,
                select: function( event, ui ) {
                    event.preventDefault();
                    $(".switch").removeClass("disabled")
                    $('#people').val(ui.item.label)
                    $('#PersonID').val(ui.item.value)
                }
            } );

            $(".switch").click(function(){
                console.log($(this).attr("url"));
                $("#display").attr("src", $(this).attr("url")+"?id="+$("#PersonID").val());

            })
        } );

    </script>


</head>
<body>


<div class="embed-responsive " style="height:850px">
    <iframe class="embed-responsive-item" id="display"></iframe>
</div>


<div class="form-group col-md-3" style="position:absolute;z-index: 100;top:0; background-color: white">
    <label for="email">Person to display :</label>
    <input type="text" class="form-control" id="people">
    <input type="hidden" id="PersonID">

    <a class="btn btn-lg btn-primary switch disabled" url="bubble.php" >Bubble  Chart</a>
    <a class="btn btn-lg btn-primary switch disabled" url="big.php">Big Chart</a>
</div>

<!-- Placed at the end of the document so the pages load faster -->
<script src="./libs/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>