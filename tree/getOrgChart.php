<?php
header('Expires: Sun, 01 Jan 2016 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

	$id = (isset($_GET['id']))?$_GET['id']:10;
	$source = file_get_contents("https://trunited.com/tree/chart_ws.php?type=getOrg&id=".$id."&".time(), true);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <meta http-Equiv="Cache-Control" Content="no-cache" />
    <meta http-Equiv="Pragma" Content="no-cache" />
    <meta http-Equiv="Expires" Content="0" />
    <title>Trunited | TreeView</title>
	
    <script src="libs/jquery-1.12.4.js"></script>	

    <script src="GetOrgChart/getorgchart.js"></script>
    <link href="GetOrgChart/getorgchart.css" rel="stylesheet" />
	
	<style type="text/css" id="myStylesheet">
        html, body {
            margin: 0px;
            padding: 0px;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        #people {
            width: 100%;
            height: 100%;
        }
		
		div.get-org-chart .get-text {
		  font-size: 28px;
		}
		
		.black-text {
		  fill:#000 !important;
		}
		
		.small-text {
			font-size: 24px !important;
			text-align:center;
		}
			
    </style>
</head>
<body>
	<div id="people"></div>
	
	<script type="text/javascript">
		var orgChart;
		var jsonData = <?php echo $source; ?>;
		getOrgChart.themes.myCustomTheme =
        {
            size: [170, 230],
            toolbarHeight: 46,
            textPoints: [
                { x: 60, y: 200, width: 170 }
            ],
            text: '<text text-anchor="middle" width="[width]" class="small-text black-text get-text get-text-[index]" x="[x]" y="[y]">[text]</text>',
            image: '<clipPath id="getMonicaClip"><circle cx="85" cy="85" r="85" /></clipPath><image preserveAspectRatio="xMidYMid slice" clip-path="url(#getMonicaClip)" xlink:href="[href]" x="0" y="0" height="170" width="170"/>'
        };
		
		getOrgChart.themes.noPointTheme =
        {
            size: [170, 230],
            toolbarHeight: 46,
            textPoints: [
                { x: 60, y: 200, width: 300 }
            ],
            text: '<text text-anchor="middle" width="[width]" class="large-text get-text get-text-[index]" x="[x]" y="[y]">[text]</text>',
            image: '<clipPath id="getMonicaClip"><circle cx="85" cy="85" r="85" /></clipPath><image preserveAspectRatio="xMidYMid slice" clip-path="url(#getMonicaClip)" xlink:href="[href]" x="0" y="0" height="170" width="170"/>'
        };
		var peopleElement = document.getElementById("people");
         
			console.log(peopleElement);
		    function createNodeEvent(sender, args) {
				
				var depth = args.node.data["DepthCredit"];
				var Points = args.node.data["Points"];
				
				var isSeed = args.node.data["isSeed"];
				
				if(isSeed) {
					
					themeObj = {};
					themeObj["theme"] = "myCustomTheme";
					sender.config.customize[args.node["id"]] = themeObj;
				}

            	var level =args.node.data["Level"];
				//var parent = args.node.data["parent"];
				//console.log(sender, args);
				
				if(args.node.data["Points"].indexOf("Points") == -1)
				{
					args.node.data["Points"] = "Points: "+args.node.data["Points"];
					args.node.data["StartDate"] = "Joined: "+args.node.data["StartDate"];
				}
			}

			orgChart = new getOrgChart(peopleElement, {
                createNodeEvent: createNodeEvent,
				theme: "eve",
				linkType: "B",
				idField: "key",
				parentIdField: "parent",
				primaryFields: ["Person", "PersonTitle", "Points","StartDate"],
				displayingFields: ["Person", "PersonTitle", "pointBalance","Level"],
				photoFields: ["image"],
                enableEdit: false,
				enableZoom: true,
				enableSearch: true,
				enableMove: true,
				enableGridView: true,
				enableDetailsView: false,
				enablePrint: false,
				expandToLevel: 8,
				scale: 0.2,
                dataSource: <?php echo $source; ?>,
				renderNodeEvent: renderNodeEventHandler
            });
			
			var counter = 0;
			function renderNodeEventHandler(sender, args) {
            	var depth= args.node.data["DepthCredit"];
            	var parentIdField =args.node.data["level"];
				
				var level =args.node.data["Level"];
				
				var isSeed = args.node.data["isSeed"];
				
				if(!isSeed) {
					var color = args.node.data["color"];
					var hex = colourNameToHex(color);
					args.content[1] = args.content[1].replace("class", "style='fill: " + hex + "; stroke: " + hex + ";' class");
				}
        	}
			
			//function to change color name to hex
			function colourNameToHex(colour)
			{
				var colours = {"blue":"#4d87a8","green":"#45b29d","orange":"#e27a3f","red":"#df5a49","yellow":"#efc94c","lightgrey":"#aaaaaa"};
			
				if (typeof colours[colour.toLowerCase()] != 'undefined')
					return colours[colour.toLowerCase()];
			
				return false;
			}
			
			var timer = window.setInterval(checkLoading,500);
			
			function checkLoading()
			{
				if(Object.keys(jsonData).length == Object.keys(orgChart.nodes).length)
				{
					orgChart.expand(0);
					window.clearInterval(timer);
				}
			}
    </script>
</body>
</html>
