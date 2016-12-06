<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <title>Trunited Bubble</title>
    <script type="text/javascript" src="./libs/jquery-1.5.2.min.js"></script>
    <script type="text/javascript" src="./libs/jquery.history.js"></script>
    <script type="text/javascript" src="./libs/jquery.tooltip.min.js"></script>
    <script type="text/javascript" src="./libs/raphael.js"></script>
    <script type="text/javascript" src="./libs/vis4.js"></script>
    <script type="text/javascript" src="./libs/Tween.js"></script>
    <script type="text/javascript" src="./libs/bubbletree.js"></script>
    <script type="text/javascript" src="./libs/aggregator.js"></script>
    <link rel="stylesheet" type="text/css" href="./libs/bubbletree.css"/>


    <script type="text/javascript">

        $(function () {

        /**
         * Here I tried to manipulate the displayed text**/
            var onNodeClick = function(node) {
          /*  jQuery(".desc").each(function(i){
                txt = jQuery(this).html()
                elements = txt.split("<br>");
                jQuery(this).html("<b>"+elements[0]+"<b>")
            })
            jQuery("span").each(function(i){
                txt = jQuery(this).html()
                elements = txt.split("<br>");
                jQuery(this).html("<b>"+elements[0]+"<b>")
            })*/
            };


            id = "<?php echo (isset($_GET['id']))?$_GET['id']:10; ?>" ;
            $.ajax({
                type: 'GET',
                dataType: "JSON",
                url: "chart_ws.php?type=bubble&id="+id
            }).done(function (data) {
                console.log(data)
                 bbt = new BubbleTree({
                    data: data,
                    container: '.bubbletree',
                    bubbleType: ['plain'],
                     nodeClickCallback: onNodeClick,
                });


                    });



        });


    </script>
    <style type="text/css">
        .bubbletree-wrapper {
            width: 100%;
        }
            .amount{
                display: none;
            }
        .name{
            font-weight: bolder;
        }
    </style>

</head>
<body>
<div class="bubbletree-wrapper">
    <div class="bubbletree"></div>
</div>
</body>
</html>
