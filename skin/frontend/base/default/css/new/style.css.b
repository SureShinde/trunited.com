*{margin:0; padding:0; box-sizing:border-box; }
ol,ul { list-style:none;}
h1,h2,h3,h4,h5,h6{font-weight:700; display:block; padding:0; margin:0;}
h1 {font-size:30px; }
h2 {font-size:24px; }
h3 {font-size:20px; }
h4 {font-size:18px; }
h5 {font-size:16px; }
h6 {font-size:14px; }
p{font-family: 'Lato', sans-serif; font-size:16px; line-height:24px; margin:0; padding:0;}
a           {transition: all 0.3s ease-in-out;-webkit-transition: all 0.3s ease-in-out;-moz-transition: all 0.3s ease-in-out;-ms-transition: all 0.3s ease-in-out;-o-transition: all 0.3s ease-in-out;}
a:hover, a:focus     {text-decoration: none;}
img         {max-width: 100%;border: none;}
/*---------------------------------------------------*/
.last{margin:0 !important;}
.pad_last{padding:0 !important;}
.no_bg {background:none !important;}
.no_bor{border:0 none !important; }

html {height:100%; }
body {font-family: 'Lato', sans-serif; color:#000; font-size:12px; padding:0; margin:0;}

.transition{-moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.color_white {color: #ffffff;}
.color_lightdark {color: #7c7c7c;}
.color_lightgold {color: #e1b974;}
.back_ground1{background: #00b5cb !important;}
.back_ground2{background: #f15b20 !important;} 
.back_ground3{background: rgba(21,182,203,0.9);}
.back_ground4{background: rgba(224,112,0,0.9);}
.back_ground5{background: rgba(227,211,10,0.9);}
.back_ground6{background: rgba(224,25,0,0.9);}
.back_ground7{background: rgba(86,86,86,0.9);}
.back_ground8{background: #eeeeee;}
.border_lightblack {border: 3px solid #393535;}
.border_lightorange{border: 3px solid #bd903d;}

/* paragraph dividing */
.two_clmn{-webkit-column-count: 2; /* Chrome, Safari, Opera */
    -moz-column-count: 2; /* Firefox */
    column-count: 2;
	
    -webkit-column-gap: 40px; /* Chrome, Safari, Opera */
    -moz-column-gap: 40px; /* Firefox */
    column-gap: 40px;
	
    -webkit-column-rule-style: solid; /* Chrome, Safari, Opera */
    -moz-column-rule-style: solid; /* Firefox */
    column-rule-style: solid;
	
    -webkit-column-rule-width: 1px; /* Chrome, Safari, Opera */
    -moz-column-rule-width: 1px; /* Firefox */
    column-rule-width: 1px;
	
    -webkit-column-rule-color: #ccc; /* Chrome, Safari, Opera */
    -moz-column-rule-color: #ccc; /* Firefox */
    column-rule-color: #ccc;
}

section{padding:25px 0;}

/* blockquote */
blockquote {position: relative; margin: 0 auto;  max-width:800px; text-align:center; padding:15px 55px; border:0 none;  }
blockquote h3:before {content: "\201C"; font-weight: bold; font-size:100px; color:#333; position: absolute; top:0;left:0px; } 
blockquote h3:after {content: "\201D"; font-weight: bold; font-size:100px; color:#333; position: absolute; top:50%;right:0px; }

/* Tabs */
.panel.with-nav-tabs .panel-heading {padding: 5px 5px 0 5px; border:0 none; }
.panel-title > a, .panel-title > small, .panel-title > .small, .panel-title > small > a, .panel-title > .small > a{display:block; padding:10px 25px;}
.panel-heading{padding:0; }
#myTab-accordion a{padding:10px 15px; display:block; }
.panel-title i{top:10px; left:5px; }

/* -------Don't Remove this---------------*/
input[type="text"], input[type="checkbox"], input[type="radio"], input[type="button"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], textarea, button, select {
    /*-webkit-appearance: none; !*Safari/Chrome*!*/
    -moz-appearance: none; /*Firefox*/
    -ms-appearance: none; /*IE*/
    -o-appearance: none; /*Opera*/
    -webkit-border-radius: 0;
	outline:none !important; 
}

/*-----------LayOut Start here-----------------------*/
@font-face {
  font-family: 'HelveticaRegular';
  src: url('../fonts/HelveticaRegular/HelveticaRegular.eot') format('embedded-opentype'),  
  	   url('../fonts/HelveticaRegular/HelveticaRegular.woff') format('woff'), 
	   url('../fonts/HelveticaRegular/HelveticaRegular.ttf')  format('truetype'), 
	   url('../fonts/HelveticaRegular/HelveticaRegular.svg#HelveticaRegular') format('svg');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'Helvetica75Bold';
  src: url('../fonts/Helvetica75Bold/Helvetica75Bold.eot') format('embedded-opentype'),  
  	   url('../fonts/Helvetica75Bold/Helvetica75Bold.woff') format('woff'), 
	   url('../fonts/Helvetica75Bold/Helvetica75Bold.ttf')  format('truetype'), 
	   url('../fonts/Helvetica75Bold/Helvetica75Bold.svg#Helvetica75Bold') format('svg');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'HelveticaOblique';
  src: url('../fonts/HelveticaOblique/HelveticaOblique.eot') format('embedded-opentype'),  
  	   url('../fonts/HelveticaOblique/HelveticaOblique.woff') format('woff'), 
	   url('../fonts/HelveticaOblique/HelveticaOblique.ttf')  format('truetype'), 
	   url('../fonts/HelveticaOblique/HelveticaOblique.svg#HelveticaOblique') format('svg');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'EraserRegular';
  src: url('../fonts/EraserRegular/EraserRegular.eot?#iefix') format('embedded-opentype'),
       url('../fonts/EraserRegular/EraserRegular.woff') format('woff'),
       url('../fonts/EraserRegular/EraserRegular.ttf')  format('truetype'),
       url('../fonts/EraserRegular/EraserRegular.svg#EraserRegular') format('svg');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'EraserDust';
  src: url('../fonts/EraserDust/EraserDust.eot') format('embedded-opentype'),  
       url('../fonts/EraserDust/EraserDust.woff') format('woff'), 
	   url('../fonts/EraserDust/EraserDust.ttf')  format('truetype'), 
	   url('../fonts/EraserDust/EraserDust.svg#EraserDust') format('svg');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'BebasNeue';
  src: url('../fonts/BebasNeue/BebasNeue.eot') format('embedded-opentype'),  
       url('../fonts/BebasNeue/BebasNeue.woff') format('woff'), 
	   url('../fonts/BebasNeue/BebasNeue.ttf')  format('truetype'), 
	   url('../fonts/BebasNeue/BebasNeue.svg#BebasNeue') format('svg');
  font-weight: normal;
  font-style: normal;
}

@font-face {
  font-family: 'BebasNeueBold';
  src: url('../fonts/BebasNeueBold/BebasNeueBold.eot?#iefix') format('embedded-opentype'),  
       url('../fonts/BebasNeueBold/BebasNeueBold.woff') format('woff'), 
       url('../fonts/BebasNeueBold/BebasNeueBold.ttf')  format('truetype'), 
       url('../fonts/BebasNeueBold/BebasNeueBold.svg#BebasNeueBold') format('svg');
  font-weight: normal;
  font-style: normal;
}

@font-face {
  font-family: 'BebasNeueRegular';
  src: url('../fonts/BebasNeueRegular/BebasNeueRegular.eot?#iefix') format('embedded-opentype'),  
       url('../fonts/BebasNeueRegular/BebasNeueRegular.woff') format('woff'), 
       url('../fonts/BebasNeueRegular/BebasNeueRegular.ttf')  format('truetype'), 
       url('../fonts/BebasNeueRegular/BebasNeueRegular.svg#BebasNeueRegular') format('svg');
  font-weight: normal;
  font-style: normal;
}

/*---------------Home Page Start--------------*/

.wrapper		{min-width:303px;}
/*.container		{width:1585px;}*/
header			{}
header img		{width:100%; height:auto;}

/*---------------Main Content Start--------------*/
.main_content	{background:#f4f4f4;}

.shop_now_blk	{padding:0; margin:0; background:#454543;}
.shop_now		{padding:18px 0; margin:0;}
.shop_now ul	{padding:0; margin:0;}
.shop_now ul li	{float:left; display:inline-block;}
.shop_now ul li a {display:block; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; line-height:25px; text-transform:capitalize;}
.shop_now ul li span {display:block; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; line-height:25px; padding:0 5px;}
.shop_now ul li a:hover		{text-decoration:none;}

.main_block		{padding:0 0 243px 0; margin:0; background:url(../../images/main_bg.jpg) no-repeat left top;}
.main_block.inn_pad {padding:0 0 30px 0 !important;}
.direct_comsumer	{padding:30px 0 39px 0; margin:0; width:77%; float:right; display:inline-block;}
.direct_comsumer h1		{font-family: 'Lato', sans-serif; font-size:50px; color:#000000; font-weight:700; line-height:45px; display:block; text-align:center; background:url(../../images/orenge_br.png) no-repeat center bottom; padding:0 0 8px 0; text-transform:uppercase;}
.direct_comsumer h1	span	{font-family: 'Lato', sans-serif; font-size:30px; display:block;}
.direct_comsumer h1.inner_pad {font-family: 'Lato', sans-serif; font-size: 42px;line-height: 44px;padding: 41px 0 15px 0;margin: 0 0 20px 0;}
.direct_comsumer h1.inner_pad1 {padding: 38px 0 9px 0;}

.left_block			{margin:0; border-radius:10px 10px;}
.sidebar		{ margin:0;}
.sidebar h6 {font-family: 'Lato', sans-serif; font-size:22px; line-height:50px; color:#ffffff; font-weight:400; background:#15b6cb; padding:0 0 0 30px; text-transform:uppercase; position:relative; margin:0; cursor:pointer;}
.sidebar h6:after	{content:''; position:absolute; z-index:9; right:15px; top:20px; background:url(../../images/down_arrow.png) no-repeat; width:22px; height:11px; transform:rotate(180deg);}
.sidebar.active h6:after	{transform:rotate(0deg);}
.sub_blk		{padding:0; margin:0; display:none;}
.sub_blk ul		{padding:3px 0 0 0; margin:0; background:#454543;}
.sub_blk ul li 	{display:block; border-bottom:1px solid #414140;}
.sub_blk ul li a {display:block; font-family: 'Lato', sans-serif; font-size:23px; color:#ffffff; font-weight:300; line-height:32px; padding:9px 0 8px 30px;}
.sub_blk ul li a span {padding:0 0 0 10px; margin:0; display:inline-block;} 
.sub_blk ul li a small	{padding:0; margin:0; display:inline-block;}
.sidebar:nth-child(1) h6  {border-radius:22px 22px 0 0;}
.sub_blk ul li a:hover	{background:#52524f;}

.right_block		{padding:0; margin:0;}
.right_block_in		{padding:0 0 41px 0; margin:0;}
.search_bar			{padding:10px 12px 10px 0; margin:0 0 13px 0; background:#454543; border-radius: 22px 22px 0 0; display:inline-block; width:100%;}
.search_bar h3      {font-family: 'Lato', sans-serif; font-size: 30px;line-height: 32px;font-weight: bold;padding: 0 0 0 24px;color: #ffffff;display: inline-block; text-transform:uppercase;}
.prices_blk			{float:right; display:inline-block;}
.prices	{float:right; display:inline-block; margin:0 20px 0 0;}
.prices label	{font-family: 'Lato', sans-serif; font-size:18px; color:#ffffff; font-weight:700; float:left; display:inline-block; line-height:31px; padding:0 14px 0 0; margin:0;}
.prices input	{width:235px; height:31px; border:1px solid #00b5cb; font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:20px; font-weight:400; float:left; display:inline-block; border-radius:5px; padding:0 28px 0 12px; background:#ffffff url(../../images/search_icon.png) no-repeat 95% center; outline:none;}

.prices .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn)	{float:left; display:inline-block; width:235px; border:1px solid #00b5cb; background:#ffffff; padding:0; margin:0; border-radius:5px;}
.prices .bootstrap-select.btn-group .dropdown-toggle .filter-option		{font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:20px; font-weight:400; padding:0 12px;}
.prices .btn-default	{position:relative;}
.prices .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:12px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.prices .caret		{border:0 none;}
.prices .btn	{padding:4px 0 4px 0; outline:none;}
.prices .btn-default:active:hover, .prices .btn-default.active:hover, .prices .open > .dropdown-toggle.btn-default:hover, .prices .btn-default:active:focus, .prices .btn-default.active:focus, .prices .open > .dropdown-toggle.btn-default:focus, .prices .btn-default:active.focus, .prices .btn-default.active.focus, .prices .open > .dropdown-toggle.btn-default.focus	{color:#00b5cb; background:#ffffff; border:1px solid #00b5cb;}
.prices .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {color:#00b5cb; background:#ffffff; border:1px solid #00b5cb;}
.prices .bootstrap-select.btn-group .dropdown-menu li a	{font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:25px; font-weight:400; outline:none; padding:3px 10px;}
.prices .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff;}

.product_blk	{padding:0; margin:0;}
.product_blk ul	{padding:0; margin:0 -0.5%;}	
.product_blk ul li {padding:2px 0 12px 0; margin:0 0.5% 20px 0.5%; width:19%; background:#ffffff; border:1px solid #b7b7b7; border-radius:5px; box-shadow:0 5px 3px #b7b7b7; display:none;}
.no_load_more.product_blk ul li{display: inline-block !important;}
.product_blk .see_more	{width:256px; height:65px; background:#f15b20; display:block; margin:43px auto 0 auto; font-family: 'Lato', sans-serif; font-size:37px; color:#ffffff; border:none; font-weight:400; border-radius:10px;}
.product_blk .see_more1{width:360px;height:65px;background:#f15b20;display:block;margin: 15px auto 0 auto; font-family: 'Lato', sans-serif; font-size:28px;color:#ffffff;border:none;font-weight:400;border-radius:10px;}
.product_blk .see_more:hover	{background:#e84c0f;}
.product_blk .see_more1:hover	{background:#e84c0f;}
.product_blk .show_less	{display:none;}
.product		{padding:0; margin:0;}
.product img	{display:block; margin:0 auto; width:100%; height:auto;}
.product h6     {font-family: 'HelveticaRegular';font-size: 17px;color:#ffffff;line-height: 19px;font-weight:normal;padding: 10px 10px 12px 10px;background: #454543;border-bottom-left-radius: 12px;border-bottom-right-radius: 12px;margin: 0 0 7px 0;text-align: center;}
.product h5 	{font-family: 'HelveticaRegular'; font-size:18px; color:#000000; line-height:20px; font-weight:normal; padding:0 12px 11px 12px;}
.product h5 span {font-size:26px; font-family: 'Helvetica75Bold'; display:block; padding:9px 0 0 0;}
.product em		{font-family: 'HelveticaOblique'; font-size:13px; color:#8b8888; line-height:16px; padding:0 12px 4px 12px; display:block;}
.product .view_details	{font-family: 'Helvetica75Bold'; font-size:19px; color:#000000; line-height:43px; border:1px solid #cdcdcd; background:#eeeeee; width:85%; margin:27px auto 0 auto; display:block; border-radius:5px;}
.product .view_details:hover	{background:#cdcdcd;}
.product small	{font-family: 'Helvetica75Bold'; font-size:17px; color:#6c6c6c; line-height:18px; float:right; display:inline-block;  padding:0 14px 0 0; position:relative;}
.product small:after	{content:''; position:absolute; z-index:9; left:-30px; top:-5px; background:url(../../images/like_icon.png) no-repeat; width:25px; height:24px;}
.product ul 	{ padding:0 0 0 12px; margin:0; float:left; display:inline-block;}
.product ul li	{float:left; display:inline-block; border:none; width:auto !important; border-radius:0; margin:0 4px 0 0; padding:0; box-shadow:inherit;}
.product ul ul li a {display:block; padding:0; margin:0;}

.product_save_in{padding: 8px 2px 11px 2px;background: #454543;border-bottom-left-radius: 12px;border-bottom-right-radius: 12px;margin: 0 0 7px 0;}
.product_save_in ul{padding: 0 !important;display: block;width: 100%;font-size: 0;} 
.product_save_in ul li{padding: 0 7px !important;vertical-align: top;margin: 0 !important;background: none;position: relative;} 
.product_save_in ul li:after{content: '';position: absolute;top: 0;width: 2px;height: 100%;background: #ffffff;right: 0;}
.product_save_in ul li:last-child:after{display: none;}
.product .view_details img {width: 32px;height: auto;display: inline-block;margin: 0 5px;}
.product_save_in h6{font-family: 'HelveticaRegular';font-size: 15px;color: #ffffff;line-height: 22px;font-weight:normal;border-radius: 0;background: none;padding: 0 5px;margin: 0;}
.product_save_in h6 span{font-size: 6px;line-height: 8px;font-weight: 400;transform: rotate(-90deg);color: #818181;display: inline-block;margin: 7px -5px 7px -2px;vertical-align: top;}

.latest_news_blk		{padding:0 0 0 0; margin:0; background:url(../../images/latest_news_img.jpg) no-repeat center top; background-size:cover;}
.latest_news			{padding:64px 0 70px 0; margin:0; position:relative;}
.latest_news_lft		{padding:0 30px 0 24px; margin:0;}
.yahoo_logo				{padding:0; margin:0;}
.yahoo_logo a			{display:block; margin:0 0 0 0;}
.yahoo_logo a img		{width:auto; height:auto;}

.news				{width:543px; padding:0; margin:0;}
.news h3			{font-family: 'Lato', sans-serif; font-size:36px; color:#ffffff; font-weight: 400; line-height:36px; padding:0 0 35px 0;}
.news p				{font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; font-weight: 400; line-height:26px; padding:0 0 38px 0; text-align:justify;}
.news a				{width:210px; display:block; border:4px solid #ffffff; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; font-weight: 400; line-height:41px; text-align:center; margin:0 0 0 3px;}

.latest_news_rgt		{padding:0 0 0 48px; margin:0;}
.latest_news_rgt h3		{font-family: 'Lato', sans-serif; font-size:36px; color:#ffffff; font-weight: 400; line-height:36px; padding:0 0 20px 0;}
.latest_news_rgt p		{background-size:cover; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; font-weight: 400; line-height:26px; position:relative; z-index:99; padding:17px 20px 64px 20px; width:700px; background:url(../../images/border.png) no-repeat; background-size:100% 100%; text-align:justify;}
.latest_news_rgt span		{font-size:25px; color:#00b5cb; line-height:25px;}
.latest_news_rgt span small {font-size:17px; font-style:italic;}

.divided		{position:absolute; z-index:99; left:12px; bottom:-26px;}

.BaclToTop                  {position:fixed; bottom:0; right:65px; display:none; z-index:999;}
.BaclToTop .btn-primary     {background: #1c1c1c;border: none;}               
.BaclToTop .btn-primary:hover,.BaclToTop .btn-primary:focus {background: #1c1c1c;opacity: 0.7;}
.BaclToTop .btn-lg, .btn-group-lg > .btn	{font-size:38px; border-radius:10px 10px 0 0;}
.BaclToTop .glyphicon-chevron-up:before	{color:#5ac5d3;}

/*---------------Main Content End--------------*/

footer			{}
footer img		{width:100%; height:100%;}

/*---------------Home Page End--------------*/

/*---------------Inner3 Page Start--------------*/

.purchase     {display: block;padding: 60px 0 34px 0;}
.save_earn    {display: inline-block;border-radius: 31px;padding: 150px 0 0 0;background-color: #ffffff;width: 31.7%;}
.save_earn img{display: block;margin: 0 auto;border-radius: 12px;}
.save_in      {display: block;margin: 34px 0 0 0;background: #454543 ;border-radius:0 0 40px 40px; padding: 32px 12px 31px 16px;}
.save_in ul   {width: 100%;display: block;font-size: 0;text-align: center;}
.save_in ul li{display: inline-block;vertical-align: top;padding: 0 12px;border-right: 4px solid #ffffff;}
.save_in ul li:last-child{border: none;}
.save_in ul li span{font-size: 43px;line-height: 32px;color: #ffffff;font-family: 'Lato', sans-serif;font-weight: 400;display: inline-block;vertical-align: top;}
.save_in ul li span small {font-size: 12px;line-height: 14px;color: #ffffff;font-family: 'Lato', sans-serif;font-weight: 400;font-style: normal;transform: rotate(-90deg);display: inline-block;vertical-align: middle;margin: -10px 0px 0 1px;}
.save_in ul li span b {color: #ffffff;font-family: 'Lato', sans-serif;font-weight: 700;display: inline-block;vertical-align: top;margin: 0 0 0 -8px;}
.why_putchase   {display: inline-block;border-top-left-radius: 31px;border-top-right-radius: 31px;background: url(../../images/why_putchase_img.jpg) no-repeat center;padding: 84px 10px 77px 0;margin: 0 0 0 3%;width: 65%;background-size: cover;}

.why_putchase h4 {font-size: 34px;line-height: 43px;color: #ffffff;font-family: 'EraserRegular';font-weight: normal;font-style: normal;text-align: center;padding: 0 0 28px 0;}
.why_putchase ul    {width: 100%;display: block;padding: 4px 0 0 108px;list-style-type: decimal;}
.why_putchase ul li {width: 100%;display: list-item;font-size: 30px;line-height: 32px;font-family: 'EraserRegular';font-weight: normal;font-style: normal;color: #ffffff;padding: 6px 0 7px 0;}
.why_putchase ul li h5{font-size: 30px;line-height: 32px;font-family: 'EraserRegular';font-weight: normal;font-style: normal;color: #ffffff;padding: 0 9px;}
.percentage{position: relative;padding: 0 8px;}
.percentage:after{content: '';position: absolute;top: 5px;left: 0;background: url(../../images/percentage.png) no-repeat center;width: 18px;height: 22px;}

.gift_cards    {display: block;padding: 0 0 34px 0;}
.card_shop     {}
.card_in       {}
.card_in img   {display: block;margin: 0 auto;}
.card_top      {background: #ffffff;border-top-left-radius: 21px;border-top-right-radius: 21px;padding: 31px 0 31px 0;}
.card_middle   {background: #f7f7f7;padding: 50px 0;}
.card_middle ul{list-style: decimal;padding: 0 0 0 60px;}
.card_middle ul li{display: list-item;font-family: 'Lato', sans-serif;font-weight: 700;font-size: 26px;line-height: 28px;padding: 0 0 8px 0;}
.card_middle ul li span{font-size: 26px;line-height: 28px;color: #000000;font-family: 'Lato', sans-serif;font-weight: bold;}
.card_bottom   {padding: 48px 0;}
.card_bottom a {width: 76%;font-family: 'Lato', sans-serif;font-weight: 400;color: #ffffff;background: #f15b20;display: inline-block;padding: 19px 0;font-size: 22px;line-height: 24px;border-radius: 10px;}
.card_bottom a:hover{background:#e84c0f;}
.card_top_pad {padding: 25px 0 !important;}
.card_top_pad1{padding: 50px 0 !important;}

.unused_gifts {}
.unused_gifts_in {padding: 58px 0 47px 0;border-top-left-radius: 26px;border-top-right-radius: 26px;background: url(../../images/unuseed_gift_background.jpg) no-repeat center;background-size: cover;}
.unused_gifts_in h2{font-size: 66px;line-height: 68px;font-family: 'EraserRegular';font-weight: normal;font-style: normal;color: #ffffff;padding: 0 0 14px 0;}
.unused_gifts_in small{font-size: 33px;line-height: 36px;font-family: 'EraserRegular';font-weight: normal;font-style: normal;color: #ffffff;}
.gift_cards1 {display: block;padding: 49px 0;}
.gift_cards1_in{max-width: 1330px;margin: 0 auto;}

/*---------------Inner3 Page End--------------*/

/*INNER PAGE NO 4 START HERE*/

.banner   {margin: 42px 0 47px 0;padding: 70px 0 20px 83px;background: #f9b224;min-height: 620px;position: relative;}
.banner h1{font-size: 148px;line-height: 58px;color: #ffffff;font-weight: 400;padding: 46px 0;}
.banner h1 span{font-size: 106px;line-height: 88px;color: #ffffff;font-weight: 400;}
.banner h1 sup{font-size: 30px;line-height: 32px;vertical-align: top;}
.banner_info {width: 47%;display: inline-block;}
.banner h2{font-size: 35px;line-height: 38px;color: #ffffff;font-weight: 500;padding: 32px 0 10px 0;}
.banner h2 sup{font-size: 16px;line-height: 18px;font-weight: 500;}
.banner p{font-size: 22px;line-height: 26px;color: #ffffff;font-weight: 400;padding: 0;letter-spacing: 0px;}
.banner_in{position: absolute;bottom: -1px;right: -1px;width: 450px;padding: 29px 20px 20px 0;text-align: right;background: url(../../images/banner_backgrond.png) no-repeat center;}
.banner_in span{font-size: 100px;line-height: 106px;color: #ffffff;font-weight: 700;display: block;}
.banner_in span small{font-size: 26px;line-height: 29px;color: #ffffff;font-weight: 500;transform: rotate(-90deg);display: inline-block;vertical-align: middle;margin: -20px -16px 0px 0px;}
.banner_in strong{font-size: 64px;line-height: 72px;color: #ffffff;font-weight: 700;display: block;}


/*---------------Inner7 Page Start--------------*/
.save_earn.wild	{width:100%; padding:0 0 0 0; border-radius:0 0 40px 40px;}
.save_earn.wild .save_in	{margin:0 0 0 0; padding:37px 12px 30px 16px;}
.save_earn.wild .save_in ul li span		{font-size:50px;}
.save_earn.wild .save_in ul li span small	{font-size:13px;}

.gift_card_blk	{width:95%; margin:0 auto; padding:60px 0 0 0;}
.gift_card_top	{padding:0 0 65px 0; margin:0;}
.gift_card_top_left	{padding:0 45px 0 0; margin:0;}
.gift_card_top_right {padding:0 0 0 0; margin:0;}
.buffalo		{padding:13px 0 0 0; margin:0;}
.buffalo h4		{font-family: 'Lato', sans-serif; font-size:38px; color:#000000; line-height:43px; font-weight:700; padding:0 0 11px 0;}
.buffalo ul		{padding:0 0 22px 0; margin:0; display:inline-block;}
.buffalo ul li 	{float:left; display:inline-block; margin:0 4px 0 0;}
.buffalo ul li a {display:block;}
.buffalo ul li span	{font-family: 'Lato', sans-serif; font-size:28px; color:#00b5cb; line-height:28px; display:block; font-weight:400; padding:3px 0 0 3px;}
.buffalo p	{font-size:24px; color:#000000; line-height:26px; font-weight:700; padding:0 0 10px 0;}
.buffalo small	{font-family: 'Lato', sans-serif; font-size:22px; color:#000000; line-height:24px; font-weight:400; display:block; padding:0 0 35px 0;}

.giftcard_shop	{padding:0; margin:0;}
.giftcard_shop_lft	{padding:3px 20px 0 0; margin:0;}
.giftcard_shop_lft p	{font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:40px; font-weight:700;}
.giftcard_shop_lft p small {font-family: 'Lato', sans-serif; font-size:22px; color:#000000; line-height:24px; font-weight:400; display:block;}
.giftcard_shop_lft small	{padding:0; margin:0;}
.small_block		{width:130px; background:#ffffff; padding:0; margin:18px 0 0 0;}
.small_block a		{display:block;}
.small_block img	{display:block; margin: auto;}

.value	{padding:10px 0 0 0; margin:0; display:inline-block; width:100%;}
.value label	{float:left; display:inline-block; font-family: 'Lato', sans-serif; font-size:39px; color:#000000; line-height:40px;}
.value .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn)	{float:right; display:inline-block; width:135px; border:2px solid #00b5cb; background:#ffffff; padding:0; margin:0; border-radius:0;}
.value .bootstrap-select.btn-group .dropdown-toggle .filter-option		{font-family: 'HelveticaRegular'; font-size:29px; color:#3b3b3b; line-height:30px; font-weight:400; padding:0 12px;}
.value .btn-default	{position:relative;}
.value .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:17px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.value .caret		{border:0 none;}
.value .btn	{padding:4px 0 4px 0; outline:none; border-radius:0;}
.value .btn-default:active:hover, .value .btn-default.active:hover, .value .open > .dropdown-toggle.btn-default:hover, .value .btn-default:active:focus, .value .btn-default.active:focus, .value .open > .dropdown-toggle.btn-default:focus, .value .btn-default:active.focus, .value .btn-default.active.focus, .value .open > .dropdown-toggle.btn-default.focus{color:#3b3b3b; background:#ffffff; border:1px solid #00b5cb;}
.value .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {font-family: 'HelveticaRegular'; color:#3b3b3b; background:#ffffff; border:1px solid #00b5cb;}
.value .bootstrap-select.btn-group .dropdown-menu li	{width:100%;}
.value .bootstrap-select.btn-group .dropdown-menu li a	{font-size:29px; color:#3b3b3b; line-height:35px; font-weight:400; outline:none; padding:3px 14px;}
.value .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff;}
.value .bootstrap-select.btn-group .dropdown-menu li a span.text	{font-size:29px; color:#3b3b3b; line-height:35px; font-weight:400; padding:0;}

.quantity	{width:63px !important;}
.add_chart	{width:100%; height:57px; display:block; text-align:center; font-size:32px; line-height:32px; color:#ffffff; background:#00b5cb; border:none; border-radius:22px; margin:19px 0 0 0;}
.add_chart:hover	{background:#03bdd4;}

.social_icons	{padding:21px 0 0 0; margin:0;}
.social_icons ul	{padding:0; margin:0;}
.social_icons ul li {float:left; display:inline-block; margin:0 20px 0 0;}
.social_icons ul li a {display:block; width:53px; height:49px; background:#4a8eff;}
.social_icons ul li a span	{font-size:28px; padding:0;}
.social_icons ul li a span .fa	{font-family:'FontAwesome'; color:#ffffff; vertical-align:middle; text-align:center; display:block; padding:11px 0 0 0;}
.social_icons ul li:nth-child(2) a	{background:#1349ab;}
.social_icons ul li:nth-child(3) a	{background:#e61515;}
.social_icons ul li:nth-child(4) a	{background:#1274ee;}
.social_icons ul li:nth-child(5) a	{background:#ff424b;}


.giftcard_shop_rgt	{padding:14px 10px 0 74px; margin:0;}
.giftcard_shop_rgt_in	{background:url(../../images/black_bord.jpg) no-repeat; padding:43px 0 55px 0; margin:0;  background-size:cover;}
.giftcard_shop_rgt_in a {display:block; width:201px; margin:0 auto;}
.giftcard_shop_rgt_in ul	{padding:30px 0 0 22%; margin:0;list-style: decimal;}
.giftcard_shop_rgt_in ul li {display:block; padding:0; margin:0;display: list-item;font-family: 'EraserDust'; font-size:20px; color:#ffffff; line-height:20px; letter-spacing:1px; float:none; padding:0 0 10px 0;}
.giftcard_shop_rgt_in ul li p {font-family: 'EraserDust'; font-size:20px; color:#ffffff; line-height:20px; letter-spacing:1px; font-weight:normal; padding:0;}
.giftcard_shop_rgt_in ul li p span {display:inline-block; padding:0 10px 0 0; font-family: 'EraserDust'; font-size:20px; color:#ffffff; line-height:20px;}

.gift_shop_bottom		{padding:0 0 73px 0; margin:0;}

.tabs_blk_main			{padding:0; margin:0;}
.tabbs					{background:none !important; box-shadow:none !important; border:none;} 
.tabs_blk				{padding:0 !important; margin:0; background:none !important;}
.tabs_blk  ul			{padding:0; margin:0; border:0 none;}
.tabs_blk ul li 		{float:left; display:inline-block; margin:0 4px 0 0;}
.tabs_blk ul li a		{display:block; font-family: 'Lato', sans-serif; font-size:24px; color:#00b5cb; line-height:46px; font-weight:400; background:#ffffff; text-align:center; width:194px; cursor:pointer; padding:0;}
.tabs_content			{padding:55px 30px 30px 30px; margin:0; background:#ffffff; min-height:840px;}
.tabs_content p			{font-family: 'Lato', sans-serif; font-size:18px; color:#343434; line-height:26px; font-weight:400; padding:0 0 35px 0;}

.giftcard_partners		{padding:0 0 0 65px; margin:0;}
.giftcard_partners_in	{padding:0 0 16px 0; margin:0; background:#ffffff; border-radius:10px 10px 0 0;}
.giftcard_partners_in h4   {font-family: 'Lato', sans-serif; font-size:22px; color:#ffffff; line-height:25px; font-weight:400; text-transform:uppercase; background:#454543; padding:8px 0; text-align:center; border-radius:10px 10px 0 0;}
.partners				{padding:30px 0; margin:0; border-bottom:1px solid #dbdbdb; background:#ffffff; width:95%; margin:0 auto;}
.partners_left			{padding:25px 0 0 0; margin:0;}
.partners_left img		{display:block; margin:0 auto;}
.partners_right			{padding:0; margin:0;}
.partners_right ul		{padding: 0 0 15px 18px;margin:0;list-style: decimal;}
.partners_right ul li  	{font-weight: 400;display: list-item;font-size: 14px;line-height: 16px;padding: 0 0 1px 0;}
.partners_right ul li span	{font-family: 'Lato', sans-serif; font-size:14px; color:#343434; font-weight:400; line-height:18px; width:93%;}
.partners_right a			{width:175px; height:36px; text-align:center; font-family: 'Lato', sans-serif; font-size:13px; color:#ffffff; display:block; background:#f15b20; border-radius:10px; line-height:36px; text-transform:uppercase; -moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.partners_right a:hover	{background: #e84c0f;}

.related_products	{padding:0; margin:0;}
.related_products .search_bar {text-align:center;}
/*---------------Inner7 Page End--------------*/
/*--------------Inner8 Page Starts------------*/
.save_earn.vista	{width:100%; padding:0 0 0 0; border-radius:0 0 40px 40px;}
.save_earn.vista .save_in	{margin:0 0 0 0; padding:37px 12px 30px 16px;}
.save_earn.vista .save_in ul li span		{font-size:60px;}
.save_earn.vista .save_in ul li span small	{font-size:20px; line-height:20px;}

.giftcard_shop_lft.fn_sz	{padding:0 0 0 0;}
.giftcard_shop_lft.fn_sz p	{font-size:32px;}
.giftcard_shop_rgt_in h4	{font-family: 'EraserRegular'; font-size:26px; color:#ffffff; line-height:26px; text-align:center; padding:0 10px; font-weight:normal;}
.giftcard_shop_rgt_in.print_bord	{padding:40px 0 0 0;}
.giftcard_shop_rgt_in.print_bord ul	{padding:20px 1% 25px 12%;}
.giftcard_shop_rgt_in.print_bord ul li	{font-size:16px;}
.giftcard_shop_rgt_in.print_bord ul li p	{font-size:16px;}
.tabs_content.print_cnt		{min-height:292px; margin:0 0 80px 0;}
/*---------------Inner8 Page End--------------*/

/*--------------Inner9 Page Starts------------*/
.vengo_energy		{padding:0 0 55px 0; margin:0;}
.vengo_energy_left	{padding:0; margin:0;}
.slide_blk		{padding:0 0 15px 0; margin:0;}
.slide_blk ul	{padding:0; margin:0;}
.slide_blk ul li  {padding:0; margin:0; outline:none;}

.thamb_blk		{padding:0 45px 0 0; margin:0;}
.thamb_blk ul	{padding:0; margin:0 -9px;}
.thamb_blk ul li {float:left; display:inline-block; margin:0 9px;}
.thamb_blk ul li span {display:block; cursor:pointer; position:relative; outline:none;}
.thamb_blk ul li span:after	{content:''; position:absolute; z-index:99; left:0; top:0; width:100%; height:100%; background:#aaa; opacity:0.4;}
.thamb_blk ul li.slick-current span:after	{display:none;}
.thamb_blk ul li:focus	{outline:none;}

.vengo_energy_right		{padding:0; margin:0;}
.vengo_right_top		{padding:0; margin:0;}
.vengo_right_top_lt		{padding:0; margin:0;}
.vengo_right_top_lt h4	{font-family: 'Lato', sans-serif; font-size:38px; color:#000000; line-height:43px; font-weight:700; padding:0 0 11px 0;}
.vengo_right_top_lt ul	{padding:0 0 22px 0; margin:0; display:inline-block;}
.vengo_right_top_lt ul li {float:left; display:inline-block; margin:0 4px 0 0;}
.vengo_right_top_lt ul li a {display:block;}
.vengo_right_top_lt ul li span {font-family: 'Lato', sans-serif; font-size:28px; color:#00b5cb; line-height:28px; display:block; font-weight:400; padding:3px 0 0 3px;}
.vengo_right_top_lt p {font-family: 'Lato', sans-serif; font-size:22px; color:#000000; line-height:24px; font-weight:400; display:block; padding:0 0 35px 0;}

.tru_box		{padding:0 0 0 0; margin:0;}
.tru_box_in		{width:281px; margin:0 auto;}
.tru_box_in label	{padding:0; margin:0 0 15px 0; font-family: 'Lato', sans-serif; font-size:18px; color:#000000; font-weight:700; line-height:25px; background:url(../../images/unchecked.png) no-repeat left 5px; position:relative; cursor:pointer; padding:0 0 0 44px;}
.tru_box_in input:checked + label:after {content:"\f00c"; font-family: FontAwesome; font-style: normal; font-weight: normal; text-decoration: inherit; position:absolute; z-index:99; left:4px; top:7px; font-size:18px; color:#00a651;}
.tru_box_in img	{padding:0; margin:0;}
.tru_box_in input	{display:none;}

.earn_points	{padding:0; margin:0;}
.earn_points_left	{padding:0 0 0 0; margin:0;}
.earn_points_left h3	{font-family: 'Lato', sans-serif; font-size:46px; color:#000; font-weight:900; line-height:50px; padding:0 0 20px 0;}
.earn_points_left em	{font-family: 'Lato', sans-serif; font-size:67px; color:#00b5cb; font-weight:900; line-height:70px; font-style:normal; padding:0 0 25px 0; display:block;}
.earn_points_left em small {font-size:36px; color:#000000; line-height:40px; position:relative; margin:0 10px 0 0;}
.earn_points_left em small:after	{content:''; position:absolute; z-index:9; left:0; top:50%; width:115px; height:4px; background:#f15b20;}
.earn_points_left .increse	{padding:0; margin:0; position:relative;}
.earn_points_left .increse input	{width:55px; height:54px; line-height:54px; border:2px solid #00b5cb; text-align:center; font-family: 'HelveticaRegular'; font-size:39px; color:#3b3b3b; padding:0 5px;}

.earn_points_left .increse .up	{position:absolute; z-index:9; right:-25px; top:10px; background:url(../../images/up_arrow.png) no-repeat; width:19px; height:10px; cursor:pointer;}
.earn_points_left .increse .down {position:absolute; z-index:9; right:-25px; bottom:10px; background:url(../../images/down_arrow2.png) no-repeat; width:19px; height:10px; cursor:pointer;}

.earn_points_left .add_cart	{width:255px; height:57px; text-align:center; background:#00b5cb; border:0 none; border-radius:10px; font-family: 'Lato', sans-serif; font-size:32px; color:#ffffff; display:block;}
.earn_points_left .trubox_btn	{width:100%; height:57px; text-align:center; font-family: 'Lato', sans-serif; font-size:32px; color:#ffffff; display:block; background:#00b5cb; border-radius:10px; line-height:57px; display:block; text-transform:uppercase; border:0; margin:25px 0 5px 0; -moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.earn_points_left .add_cart:hover	{background: #03bdd4;}
.earn_points_left .trubox_btn:hover	{background: #03bdd4;}

.giftcard_shop_rgt.top_pad	{padding:90px 10px 0 74px;}
.giftcard_shop_rgt.top_pad .giftcard_shop_rgt_in	{padding:30px 0 20px 0;}

/*---------------Inner9 Page End--------------*/

/*---------------Inner 10 Page Starts--------------*/
.search_club		{position:absolute; z-index:99; left:25px; top:10px;}
.search_club label	{font-family: 'Lato', sans-serif; font-size:18px; color:#ffffff; font-weight:700; float:left; display:inline-block; line-height:31px; padding:0 14px 0 0; margin:0;}
.search_club input	{width:235px; height:31px; border:1px solid #00b5cb; font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:20px; font-weight:400; float:left; display:inline-block; border-radius:5px; padding:0 28px 0 12px; background:#ffffff url(../../images/search_icon.png) no-repeat 95% center; outline:none;}
.food_beverages_main		{background:#fff; padding:50px 0 0 0;}
.food_products				{padding:0 0 0 0; margin:0 auto;}
.food_products_in			{padding:0; margin:0 0 12px 0;}
.food_products_left			{padding:0 0 0 42px; margin:0; float:left; display:inline-block;}
.food_products_left h6		{font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:27px; font-weight:700; padding:0 0 25px 135px;}
.food_products_left ul			{padding:0; margin:0;}
.food_products_left ul li 		{padding:0; margin:0 23px 0 0; float:left; display:inline-block; line-height:27px;}
.food_products_left ul li label	{display: inline-block;margin:5px 0 0 0;}
.food_products_left ul li label small	{background:url(../../images/unchecked2.png) no-repeat; width:19px; height:19px; display:block;}
.food_products_left ul li input[type=checkbox]{display: none;}
.food_products_left ul li input[type=checkbox]:checked + small{background: url(../../images/checked.png) no-repeat;}

.food_products_left ul li:nth-child(1) label small {background:url(../../images/unchecked3.png) no-repeat;}	
.food_products_left ul li:nth-child(1) input[type=checkbox]:checked + small{background: url(../../images/checked1.png) no-repeat;}
.food_products_left ul li:nth-child(2) label small {background:url(../../images/unchecked4.png) no-repeat;}	
.food_products_left ul li:nth-child(2) input[type=checkbox]:checked + small{background: url(../../images/checked2.png) no-repeat;}

.food_products_left ul li span	{font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:27px; font-weight:400; padding:0 0 0 8px;}
.food_products_left ul li span.qnty	{text-align:left;}

.food_products_right		{padding:0; margin:0; width:100px; float:right; display:inline-block;}
.food_products_right h6		{font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:27px; font-weight:700; padding:0 0 25px 0;}

.food_products_right .increse	{padding:0; margin:0; position:relative; width:52px;}
.food_products_right .increse input	{width:46px; height:26px; height:38px; line-height:38px; border:3px solid #acacac; text-align:center; font-family: 'HelveticaRegular'; font-size:20px; color:#8d8c8c; padding:0 2px; margin:0; border-radius:0;}

.food_products_right .increse .up	{position:absolute; z-index:9; right:-12px; top:8px; background:url(../../images/up_arrow.png) no-repeat; width:13px; height:6px; cursor:pointer; background-size:cover;}
.food_products_right .increse .down {position:absolute; z-index:9; right:-12px; bottom:8px; background:url(../../images/down_arrow2.png) no-repeat; width:13px; height:6px; cursor:pointer; background-size:cover;}

.onetime_sipping	{padding:14px 0 125px 0; margin:0; border-top:1px solid #acacac;}
.onetime_sipping ul	{padding:0; margin:0;}
.onetime_sipping ul li 	{float:left; display:inline-block; margin:0 0 0 40px;}
.onetime_sipping ul li label {display:inline-block; margin:0;}
.onetime_sipping ul li label span	{font-family: 'Lato', sans-serif; font-size:20px; color:#a6a5a5; line-height:19px; font-weight:400; background:url(../../images/unchecked2.png) no-repeat left center; padding:0 0 0 32px;}
.onetime_sipping ul li input[type=checkbox]{display: none;}
.onetime_sipping ul li input[type=checkbox]:checked + span{background: url(../../images/checked.png) no-repeat left center;}

.onetime_sipping ul li:nth-child(1) label span {background:url(../../images/unchecked3.png) no-repeat left center;}	
.onetime_sipping ul li:nth-child(1) input[type=checkbox]:checked + span{background: url(../../images/checked1.png) no-repeat left center;}
.onetime_sipping ul li:nth-child(2) label span {background:url(../../images/unchecked4.png) no-repeat left center;}	
.onetime_sipping ul li:nth-child(2) input[type=checkbox]:checked + span{background: url(../../images/checked2.png) no-repeat left center;}

.month_tabs		{padding:0 0 132px 0; margin:0 0 45px 0;}
.month_tabs	.brd_line	{width:100%; background:#acacac; height:3px;}
.receive_email	{padding:0; margin:-44px auto 0 auto; position:relative;}
.receive_email ul	{padding:0; margin:0;}
.receive_email ul li {float:left; display:inline-block; position:relative;}
.receive_email ul li span	{width:77px; height:77px; text-align:center; line-height:77px; background:#00b5cb; font-family: 'Lato', sans-serif; font-size:39px; color:#ffffff; font-weight:400; display:block; border-radius:100%; margin:0 auto;}
.receive_email ul li small	{font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:27px; font-weight:400; text-align:center; display:block; padding:7px 0 0 0;}
.receive_email .rgt_arrow	{position:absolute; z-index:99; right:-10px; top:33px;}
.receive_email .month_text	{position:absolute; z-index:99; left:0; top:12px; font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:27px; font-weight:400; padding:0; margin:0;}


.current_mnthly    {background: none;;padding:0 0 135px 0; overflow:hidden;}
.current_mnthly ul {margin:0 -30px; padding:0;}
.current_mnthly ul li{display: inline-block;text-align: center;background: none; padding:0 30px;}
.curnt_mnth         {background: #f7f7f7;padding: 66px 0 25px 0;}
.current_mnthly h4  {font-size: 29px;line-height: 31px;font-family: 'Lato', sans-serif;display: block;color: #ffffff;padding: 13px 0 13px 0;font-weight: bold;background: #454543;border-radius: 20px 20px 0 0;}
.curnt_mnth_in      {width: 70%;;margin: 0 auto 33px auto;background: #ffffff;border-radius: 12px 12px 0 0;}
.curnt_mnth_in strong   {font-size: 56px;line-height: 58px;font-family: 'BebasNeueRegular';display: block;color: #484342;padding: 23px 0 10px 0; font-weight:normal;}
.curnt_mnth_in small    {font-size: 23px;line-height: 25px;font-family: 'Lato', sans-serif;display: block;color: #ffffff;background: #454543;padding: 8px 10px 8px 10px;border-radius: 0 0 30px 30px;text-transform: uppercase;}
.current_mnthly ul li .btn{background: #f15b20;outline: none;border-radius: 9px;padding: 9px 0;width: 90%;margin: 57px 0 0  0;font-size: 24px;color: #ffffff;text-transform: uppercase;}
.current_mnthly ul li .btn:hover	{background:#e84c0f;}

/*---------------Inner 10 Page Starts--------------*/
/*---------------Inner11 Page Starts--------------*/
.never_forgot   {background: #ffffff;padding: 2.8% 8% 5.8% 8%;border-radius: 0 0 12px 12px;}
.never_forgot h4{font-size: 32px;line-height: 36px;display: block;text-align: center;color: #000000;font-family: 'Lato', sans-serif;font-weight: bold;padding: 0 0 0 0;}
.never_forgot_under{display: block;padding: 100px 0 0 0;background: url(../../images/flowers_img1.png) no-repeat right top;}
.never_forgot_under h5{font-size: 24px;line-height: 26px;display: block;color: #000000;font-family: 'Lato', sans-serif;font-weight: bold;padding: 1px 0 7px 48px;position: relative;cursor: pointer;}
.never_forgot_under h5:before{content: '\f067';position: absolute;left: 10px;top: 2px;color: #ffffff;background: #15b6cb;width: 28px;height: 28px;font-family: FontAwesome;font-size: 15px;line-height: 30px;text-align: center;}
.never_forgot_under span{font-size: 20px;line-height: 22px;display: block;color: #15b6cb;font-family: 'Lato', sans-serif;font-weight: 400;padding: 1px 0 0 49px;}

.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td{border-bottom: 1px solid #acacac;}
.table-striped > tbody > tr:nth-of-type(odd){border-bottom: 1px solid #acacac;background: #ffffff;}
.table-condensed > thead > tr > th, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td{font-size: 24px;line-height: 26px;color: #000000;font-family: 'Lato', sans-serif;padding: 6px 5px 12px 5px;border-bottom: 1px solid #ccc;}
.tb_th_width    {width: 22%;}
.tb_th_width1   {width: 25%;}
.tb_th_width2   {width: 12%;}
.tb_th_width3   {width: 41%;}
#no-more-tables{padding: 33px 0 30px 0;}
.never_forgot_under p{font-size: 20px;line-height: 22px;display: block;color: #a6a5a5;font-family: 'Lato', sans-serif;font-weight: 400;padding: 5px 0 7px 6px;position: relative;}
.never_forgot_under ul{display: inline-block;width: 100%;padding: 47px 0 0 0;}
.never_forgot_under ul li{display: inline-block;}
.never_forgot_under ul li .btn_blk{display: block;background: #f15b20;color: #ffffff;font-size: 24px;line-height: 26px;font-weight: 400;font-family: 'Lato', sans-serif;padding: 18px 0;width: 100%;border-radius: 13px;text-transform: uppercase; border:0 none;}
.margin_top{margin: 40px 0 0 0;}
.never_forgot_under ul li .btn_blk:hover	{background:#e84c0f;}

/*---------------Inner11 Page Ends--------------*/


/*---------------Inner12 Page Starts--------------*/

.never_forgot_under small{font-size: 20px;line-height: 22px;display: block;color: #a6a5a5;font-family: 'Lato', sans-serif;font-weight: 400;padding: 1px 0 0 49px;}

.occasion_form_sec	{padding:0; margin:0; border-bottom:1px solid #acacac;}
.occasion_form_sec ul	{margin:0 -18px; width:auto;}
.occasion_form_sec ul li {padding:0 18px; margin:0 0 25px 0;}
.occasion_form_sec ul li label	{padding:0; margin:0; font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:27px; font-weight:700; display:block; padding:0 0 5px 0;}
.occasion_form_sec ul li input, .select_gift select, .occasion_form_sec ul li select	{width:100%; height:40px; border:3px solid #acacac; font-size: 20px;display: block;color: #a6a5a5;font-family: 'Lato', sans-serif;font-weight: 400; padding:0 13px;}
.occasion_form_sec ul li textarea	{width:100%; height:142px; border:3px solid #acacac; font-size: 20px;display: block;color: #a6a5a5;font-family: 'Lato', sans-serif;font-weight: 400; padding:5px 13px; resize:none;}
.occasion_form_sec ul li input.city, .occasion_form_sec ul li select, .occasion_form_sec ul li .middle, .occasion_form_sec ul li input	{margin:0 0 30px 0;}

.occasion_form_sec .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {width: 100%; display: inline-block;margin: 0;}
.occasion_form_sec .bootstrap-select.btn-group .dropdown-toggle .filter-option	{font-family: 'Lato', sans-serif; font-size:20px; color:#8d8c8c; line-height:30px; font-weight:400; padding:0 12px;}
.occasion_form_sec .btn-default	{position:relative;}
.occasion_form_sec .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:15px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.occasion_form_sec .caret		{border:0 none;}
.occasion_form_sec .btn	{padding:2px 0; outline:none; border: 3px solid #acacac;}
.occasion_form_sec .btn-default:active:hover, .occasion_form_sec .btn-default.active:hover, .occasion_form_sec .open > .dropdown-toggle.btn-default:hover, .occasion_form_sec .btn-default:active:focus, .occasion_form_sec .btn-default.active:focus, .occasion_form_sec .open > .dropdown-toggle.btn-default:focus, .occasion_form_sec .btn-default:active.focus, .occasion_form_sec .btn-default.active.focus, .occasion_form_sec .open > .dropdown-toggle.btn-default.focus	{color:#00b5cb; background:#ffffff; border:3px solid #acacac;}
.occasion_form_sec .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {color:#00b5cb; background:#ffffff; border:2px solid #acacac;}
.occasion_form_sec .bootstrap-select.btn-group .dropdown-menu li	{padding:0; display:block; margin:0;}
.occasion_form_sec .bootstrap-select.btn-group .dropdown-menu li a	{font-size:20px; color:#8d8c8c; line-height:24px; font-weight:400; outline:none; padding:3px 14px; outline:none;}
.occasion_form_sec .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff;}
.occasion_form_sec .bootstrap-select.btn-group .dropdown-menu li a span.text	{font-size:20px; color:#8d8c8c; line-height:24px; font-weight:400; padding:0; background: none;}

.select_gift		{padding:50px 0 10px 0; margin:0;}
.select_gift_lft	{padding:0; margin:0; position:relative;}
.select_gift_lft label	{float:left; display:inline-block; margin:0; ont-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:27px; font-weight:700; padding:0 0 8px 0;}
.select_gift_lft .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {width: 100%; display: inline-block;margin: 0;}
.select_gift_lft .bootstrap-select.btn-group .dropdown-toggle .filter-option	{font-family: 'Lato', sans-serif; font-size:20px; color:#8d8c8c; line-height:30px; font-weight:400; padding:0 12px;}
.select_gift_lft .btn-default	{position:relative;}
.select_gift_lft .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:15px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.select_gift_lft .caret		{border:0 none;}
.select_gift_lft .btn	{padding:2px 0; outline:none; border: 3px solid #acacac;}
.select_gift_lft .btn-default:active:hover, .select_gift_lft .btn-default.active:hover, .select_gift_lft .open > .dropdown-toggle.btn-default:hover, .select_gift_lft .btn-default:active:focus, .select_gift_lft .btn-default.active:focus, .select_gift_lft .open > .dropdown-toggle.btn-default:focus, .select_gift_lft .btn-default:active.focus, .select_gift_lft .btn-default.active.focus, .select_gift_lft .open > .dropdown-toggle.btn-default.focus	{color:#00b5cb; background:#ffffff; border:3px solid #acacac;}
.select_gift_lft .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {color:#00b5cb; background:#ffffff; border:2px solid #acacac;}
.select_gift_lft .bootstrap-select.btn-group .dropdown-menu li	{padding:0; display:block; margin:0;}
.select_gift_lft .bootstrap-select.btn-group .dropdown-menu li a	{font-size:20px; color:#8d8c8c; line-height:24px; font-weight:400; outline:none; padding:3px 14px; outline:none;}
.select_gift_lft .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff; width:100%;}
.select_gift_lft .bootstrap-select.btn-group .dropdown-menu li a span.text	{font-size:20px; color:#8d8c8c; line-height:24px; font-weight:400; padding:0; background: none;}

/*.select_gift_lft:after	{content:''; position:absolute; z-index:99; right:-245px; top:-16px; background:url(../../images/flowers_img2.png) no-repeat; width:136px; height:122px;} */
.orange_btn_blk.pad12		{padding:15px 0 60px 0; margin:-30px 0 0 0;} 


/*---------------Inner12 Page Ends--------------*/

/*---------------Inner13 Page Starts--------------*/

.travel        {padding: 70px 0 70px 0;}
.travel_in     {display: block;}
.travel_in ul  {padding:0; margin:0 -15px;}
.travel_in ul li {display: inline-block; padding:0 15px;}
.order_progress  {background: #ffffff;border-radius: 20px 20px 0 0;padding: 0 0 35px 0;}
.odrer_top       {background: #454543;padding: 16px 0 13px 0;border-radius: 20px 20px 0 0;}
.odrer_top h5    {color: #ffffff;font-size: 22px;line-height: 24px;text-align: center;display: block;font-weight: bold;}
.order_middle    {padding: 50px 26px 36px 25px;}
.order_middle small{font-size: 20px;line-height: 22px;color: #454543;font-weight: 400;padding: 10px 0 13px 0;display: block;}
.order_middle address{font-size: 20px;line-height: 28px;color: #454543;font-weight: 400;padding: 0px 0 15px 0;display: block;margin: 0;}
.order_middle address img{display: inline-block;vertical-align: middle;margin: 0 10px 0 0;}
.order_middle h4 {font-size: 24px;line-height: 26px;color: #454543;font-weight: bold;padding: 2px 0 15px 0;display: block;}
.order_middle a  {font-size: 20px;line-height: 22px;color: #00b5cb;font-weight: 400;margin:0 0 12px 0;display: inline-block;}

.order_middle .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn)	{width: 100%; display: inline-block;margin: 0 0 15px 0;}
.order_middle .bootstrap-select.btn-group .dropdown-toggle .filter-option		{font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:30px; font-weight:400; padding:0 12px;}
.order_middle .btn-default	{position:relative;}
.order_middle .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:13px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.order_middle .caret		{border:0 none;}
.order_middle .btn	{padding:0; outline:none; border: 2px solid #acacac; height:36px;}
.order_middle .btn-default:active:hover, .order_middle .btn-default.active:hover, .order_middle .open > .dropdown-toggle.btn-default:hover, .order_middle .btn-default:active:focus, .order_middle .btn-default.active:focus, .order_middle .open > .dropdown-toggle.btn-default:focus, .order_middle .btn-default:active.focus, .order_middle .btn-default.active.focus, .order_middle .open > .dropdown-toggle.btn-default.focus {color:#00b5cb; background:#ffffff; border:2px solid #acacac;}
.order_middle .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {color:#00b5cb; background:#ffffff; border:2px solid #acacac;}
.order_middle .bootstrap-select.btn-group .dropdown-menu li	{padding:0; width:100%;}
.order_middle .bootstrap-select.btn-group .dropdown-menu li a	{font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:24px; font-weight:400; outline:none; padding:3px 12px; outline:none; margin:0;}
.order_middle .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff;}
.order_middle .bootstrap-select.btn-group .dropdown-menu li a span.text	{font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:24px; font-weight:400; padding:0; background: none;}



.chek_box       {display: block;}
.chek_box label {font-size: 18px;line-height: 20px;cursor: pointer;color: #454543;position: relative;display: inline-block;padding: 0 0 0 25px;}
.chek_box label:before{content: '';position: absolute;left: 0;top: 2px;height: 16px;width: 16px;background: url(../../images/cheak_box1.png) no-repeat;background-size: cover;}
.chek_box input[type=checkbox]{display:none;}
.chek_box input[type=checkbox]:checked + label:before {background:url(../../images/cheak_box_cheak.png) no-repeat;background-size: cover;}

.radio_btn{display: block;}
.radio_btn label {font-size: 20px;line-height: 20px;cursor: pointer;color: #454543;position: relative;display: inline-block;padding: 0 0 12px 25px;font-weight: 400;}
.radio_btn label:before{content: '';position: absolute;left: 0;top: 2px;height: 16px;width: 16px;background: url(../../images/radio1.png) no-repeat;}
.radio_btn input[type=radio]{display:none;}
.radio_btn input[type=radio]:checked + label:before {background:url(../../images/radio_cheaked.png) no-repeat;}


.order_bottom {display: block;padding: 0 0 20px 0;}
.order_bottom h5{color: #ffffff;font-size: 22px;line-height: 24px;text-align: center;display: block;font-weight: bold;background: #454543;padding: 15px 0;margin: 0 0 57px 0;}
.order_bottom h3{color: #454543;font-size: 30px;line-height: 32px;text-align: center;display: block;font-weight: bold;padding: 5px 0;text-transform: uppercase;}

.progress_in    {padding: 53px 10px 0 10px;}
.progress_in ul {width: 100%;display: inline-block; margin:0; padding:0;}
.progress_in ul li{display: block;border-bottom: 1px solid #e1dfdf;padding: 6px 10px 9px 10px;width: 100%;}
.progress_in ul li:last-child{border: none;}
.progress_in ul li h6{font-size: 20px;line-height: 22px;color: #454543;font-weight: bold;padding: 0;display: block;}
.progress_in ul li span{font-size: 20px;line-height: 22px;color: #454543;font-weight: 400;padding: 0;display: block;}
.order_product  {width: 64%;display: inline-block; padding:0 5px 0 0;}
.order_qty      {width: 18%;display: inline-block;}
.order_subtotal {width: 18%;display: table-cell;text-align: right; vertical-align:bottom;}
.shipping       {display: table;padding: 9px 0 11px 0; width:100%; height:100%;}
.shipping_left  {width: 82%;display: table-cell;text-align: right;padding: 0 17px 0 0; vertical-align:bottom;}
.grand_total    {padding: 14px 0 60px 0;}
.progress_in input{display: inline-block;width: 48.1%;height: 36px;border:2px solid #ababab;font-size: 18px;line-height: 20px;color: #c4c4c4;font-weight: 400;padding: 0 10px;margin: 10px 20px 0 0;border-radius: 6px;outline: none;}
.progress_in a{width: 25%;font-size: 18px;line-height: 20px;color: #ffffff;font-weight: 400;padding: 7px 0;border-radius: 10px;outline: none;display: inline-block;background: #00b5cb;vertical-align: top;text-align: center;margin: 10px 0 0 0; -moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.progress_in a:hover	{background:#03bdd4;}
.progress_in .btn {width: 90%;height: 55px;background: #f15b20;font-weight: bold;font-size: 20px;color: #ffffff;border-radius: 10px;margin: 20px 0 0 0;}
.progress_in .btn:hover{background: #e84c0f;}

.order_qty .increse	{padding:0; margin:0; position:relative; width:30px;}
.order_qty .increse input	{width:26px; height:26px; line-height:26px; border:2px solid #909090; text-align:center; font-family: 'HelveticaRegular'; font-size:18px; color:#3b3b3b; padding:0 5px; margin:0; border-radius:0;}

.order_qty .increse .up	{position:absolute; z-index:9; right:-12px; top:5px; background:url(../../images/up_arrow.png) no-repeat; width:13px; height:6px; cursor:pointer; background-size:cover;}
.order_qty .increse .down {position:absolute; z-index:9; right:-12px; bottom:5px; background:url(../../images/down_arrow2.png) no-repeat; width:13px; height:6px; cursor:pointer; background-size:cover;}


/*---------------Inner13 Page Ends--------------*/

/*---------------Inner14 Page Starts--------------*/
.head_txt    {display: inline-block;width: 62%;float: left;text-align: right;}
.width_full  {width: 100% !important;}
.margin_top  {margin: 80px 0 0 0 !important;}
.connection_map{min-height: 1815px;background: url(../../images/connection_map.png) no-repeat center;background-size: cover;display: block;width: 100%;margin: -5px 0 0 0;padding: 33px 0 0 0;}
.cmap_bar    {width: 335px;display: inline-block;margin: 0 0 0 33px;background: #454543;padding: 8px 0 0 0;}
.cmap_bar ul {width: 100%;display: inline-block;margin: 0;}
.cmap_bar ul li{display: block;width: 100%;border-bottom: 1px solid #414140;}
.cmap_bar ul li .chek_box1{display: inline-block;}
.cmap_bar ul li label{display: inline-block;margin: 0;}
.cmap_bar ul li span {font-size: 24px;line-height: 24px;color: #ffffff;font-weight: 300;padding: 12px 11px 17px 63px;position: relative;background: url(../../images/cheak_box1.png) no-repeat left top;background-position: 30px 13px;cursor: pointer;display: block;}
.cmap_bar ul li input[type=checkbox]{display: none;}
.cmap_bar ul li input[type=checkbox]:checked + span{background: url(../../images/cheak_box_cheak.png) no-repeat left top;background-position: 30px 13px;}
.cmap_bar ul li  .fa{display: inline-block;font-size: 32px;line-height: 34px;float: right;padding: 8px 14px 0 0;color: #15b6cb;cursor: pointer;}
.arrow_up.fa        {padding: 0 14px 0 0 !important;}
.arrow_up.fa:before{content: "\f0d8";}
.cmap_bar ul > li > ul {padding: 0 0px;border-top: 1px solid #414140;display: none;}
.cmap_bar ul > li > ul > li {padding: 0 0 0 30px;}
.filter_btn    {padding: 33px 0 40px 0;}
.filter_btn .btn{width: 156px;height: 50px;background:#f15b20;display:block;margin: 0 auto;font-family: 'Lato', sans-serif;font-size: 24px; color:#ffffff;border:none;font-weight: 300;border-radius:10px;}
.filter_btn .btn:hover	{background:#e84c0f;}

.cmap_right    {display: inline-block;width: 35.9%;padding: 76px 0 0 0;vertical-align: top;text-align: right;}
.count         {display: inline-block;width: 305px;height: 305px;border-radius: 100%;background: #15b6cb;border:7px solid #474343;position: relative;text-align: center;}
.count h2      {position: absolute;top: 50%;left: 50%;display: inline-block;font-size: 127px;line-height: 127px;transform: translate(-50%, -50%);-webkit-transform: translate(-50%, -50%);-moz-transform: translate(-50%, -50%);-ms-transform: translate(-50%, -50%);-o-transform: translate(-50%, -50%);font-family: 'BebasNeueBold';color: #ffffff;z-index: 1;}
.count:after   {content: '';position: absolute;top: 0;left: 0;width: 99.9%;height: 99.9%;border-radius: 100%;border: 15px solid #ffffff;}
.count:before  {content: '';position: absolute;top: -1px;left: -1px;width: 100.7%;height: 100.7%;border-radius: 100%;border: 6px solid #403a3a;z-index: 1;}

/*---------------Inner14 Page Ends--------------*/

/*---------------Inner15 Page Starts--------------*/
.connection_map_in    {text-align: center;}
.connection_map_in a  {display: inline-block;}
.connection_map_in ul {width: 100%;display: inline-block;padding: 28px 0 0 0;margin: 0;}
.connection_map_in ul li{display: block;padding: 0 0 2px 0;}
.left_no              {display: inline-block;width: 7.8%;padding: 20px 0 0 0;}
.left_no strong       {font-size: 53px;line-height: 55px;font-family: 'BebasNeueBold';}
.map_middle              {display: inline-block;width: 84.4%;text-align: left;padding: 0 2% 0 6%;}
.nicolas_in           {display: inline-block;width: 89px;height: 89px;position: relative;border-radius: 100%;text-align: center;padding: 0.4% 0;margin: 0 5px 10px 5px;vertical-align: top;cursor: pointer;}
.nicolas_in:after     {content: '';position: absolute;left: 3px;top: 3px;width: 93.2%;height: 93.2%;border: 3px solid #ffffff;border-radius: 100%;}
.nicolas_in:before    {content: '';position: absolute;left: 0;top: 0;width: 100%;height: 100%;border: 3px solid #393535;border-radius: 100%;z-index: 2;}
.nicolas_in h2        {font-size: 81px;line-height: 81px;display: inline-block;color: #ffffff;font-family: 'BebasNeueBold';}
.nicolas_up           {position: absolute;top: 0;left: 0;width: 100%;height: 100%;border-radius: 100%;padding: 0;}
.nicolas_up h4        {font-size: 37px;line-height: 37px;display: block;color: #ffffff;font-family: 'BebasNeueBold';position: relative;z-index: 9;padding: 28% 0;}
.nicolas_up h6        {font-size: 22px;line-height: 24px;display: block;color: #ffffff;font-family: 'BebasNeueBold';position: relative;z-index: 9;text-transform: uppercase;padding: 37% 0 37% 0;}
.nicolas_4line        {position: absolute;width: 100%;height: 100%;left: 0;top: 0;}
.nicolas_4line:after  {content: '';position: absolute;left: 50%;top: 0;width: 3px;height: 100%;background: #393535;z-index: 2;transform: translateX(-50%);-webkit-transform: translateX(-50%);-moz-transform: translateX(-50%);-ms-transform: translateX(-50%);-o-transform: translateX(-50%);}
.nicolas_4line:before {content: '';position: absolute;left: 0%;top: 50%;width: 100%;height: 3px;background: #393535;z-index: 2;transform: translateY(-50%);-webkit-transform: translateY(-50%);-moz-transform: translateY(-50%);-ms-transform: translateY(-50%);-o-transform: translateY(-50%);}

.nicolas_4line1        {position: absolute;width: 100%;height: 100%;left: 0;top: 0;}
.nicolas_4line1:after  {content: '';position: absolute;left: 50%;top: 0;width: 3px;height: 100%;background: #393535;z-index: 2;transform: translateX(-50%) rotate(45deg);-webkit-transform: translateX(-50%) rotate(45deg);-moz-transform: translateX(-50%) rotate(45deg);-ms-transform: translateX(-50%) rotate(45deg);-o-transform: translateX(-50%) rotate(45deg);}
.nicolas_4line1:before {content: '';position: absolute;left: 0%;top: 50%;width: 100%;height: 3px;background: #393535;z-index: 2;transform: translateY(-50%) rotate(45deg);-webkit-transform: translateY(-50%) rotate(45deg);-moz-transform: translateY(-50%) rotate(45deg);-ms-transform: translateY(-50%) rotate(45deg);-o-transform: translateY(-50%) rotate(45deg);}
.for_after             {position: relative;}
.for_after:after       {content: '';position: absolute;right: -45%;width: 73%;height: 2px;background: #ffffff;top: 58%;}

.for_before             {position: relative;}
.for_before:before      {content: '';position: absolute;left: -45%;width: 73%;height: 2px;background: #ffffff;top: 58%;}
.for_border             {width: 65px;height: 65px;border: 2px solid;border-radius: 100%;display: inline-block;padding: 3% 5% 3% 0%;text-align: center;}

.pop_up_trained         {width: 650px;position: absolute;top: 63px;left: 0;z-index: 99;display: none;}
.pop_up_trained:after   {content: "\f0d8";position: absolute;top: -31px;left: 32px;font-family: FontAwesome;color: #494543;font-size: 36px;}
.trained_head           {background: #494543;border-radius: 20px 20px 0 0;position: relative;text-align: center;padding: 6px 0;}
.trained_head h5        {font-size: 20px;line-height: 22px;color: #ffffff;display: block;}
.trained_head .btn      {position: absolute;right: 10px;top: 4px;border-radius: 100%;width: 25px;height: 25px;text-align: center;padding: 0;font-size: 16px;font-weight: bold;background: #989592;color: #fff;outline: none;}
.trained_middle         {display: block;}
.trained_middle h6      {font-size: 10px;line-height: 12px;font-weight: bold;position: relative;text-align: left;}
.trained_middle h6:after{content: '';position: absolute;right: 0;top: 50%; width: 69%;height: 1px;background: #ffffff;}
.trained_middle_left    {display: inline-block;width: 54.7%;border-right: 3px solid #ffffff;}
.trained_middle_top     {padding: 8px 25px;}
.trained_middle_top_left{display: inline-block;width: 70%;padding: 3px 0 0px 0;}
.trained_middle_top_left h3{font-size: 30px;color:#ffffff;font-weight:400;line-height: 32px;padding: 0 0 1px 0;}
.trained_middle_top_left a{font-size: 12px;color:#ffffff;font-weight: bold;line-height: 14px;padding: 2px 10px 3px 10px;display: inline-block;background: #494543;border-radius: 5px;}
.trained_middle_top_left small{font-size: 12px;color:#ffffff;font-weight: 400;line-height: 14px;padding: 0px 10px 0px 4px;display: block;}
.trained_middle_top_bottom {display: block;padding: 5px 0 5px 0;}
.trained_middle_top_bottom ul{width: 100%;display: inline-block;margin: 0;}
.trained_middle_top_bottom ul li{display: inline-block;width: 20%;float: left;padding: 0 3px;}
.trained_middle_top_bottom ul li a{display:block; padding:0; margin:0; font-family: 'Lato', sans-serif; font-size:7px; color:#ffffff; font-weight:700; line-height:20px; background:#484342; text-align:center; border-radius:5px; text-transform:uppercase;}
.trained_middle_bottom  {padding: 8px 28px 17px 28px;border-top: 3px solid #ffffff;}
.trained_middle_bottom h6{margin: 0 0 4px 0;}
.trained_middle_bottom h6:after{background: #7c7c7c;}
.trained_middle_bottom ul{width: 100%;display: inline-block;padding: 10px 0 18px 0;margin: 0;}
.trained_middle_bottom ul li{width: 20%;display: inline-block;float: left;padding: 0 3px;}
.trained_middle_bottom p{font-size: 8px;line-height: 10px;font-weight: 400;color:#484342;display: block;text-align: center;}

.trained_middle_top_right{display: inline-block;width: 30%;}
.trained_middle_top_right img{width: 100%;height: auto;}

.trained_middle_right   {display: inline-block;width: 45.3%;padding: 8px 15px 36px 15px;background: url(../../images/background1.png) no-repeat center;background-size: cover;}
.trained_middle_right h6:after   {background: #7c7c7c;width: 48%;}
.trained_middle_right ul       {display: inline-block;width: 100%;padding: 7px 0 0 0;margin: 0;}
.trained_middle_right ul li    {display: inline-block;padding: 0 7px 5px 7px;}
.monthly_stats                 {display: block;background: #ffffff;border-radius: 8px 8px;}
.monthly_stats span            {font-family: 'BebasNeueBold';font-size: 16px;line-height: 18px;color: #494543;display: block;text-align: center;padding: 8px 0 2px 0;}
.monthly_stats small           {font-size: 7px;line-height: 9px;color: #ffffff;display: block;text-align: center;padding: 0;background: #494543;text-transform: uppercase;border-radius: 0 0 8px 8px;padding: 3px 0 2px 0;}
.balance_member                 {}
.balance_member h6:after        {width: 60%;}
.balance_member ul       {display: inline-block;width: 100%;padding: 4px 0 0 0;margin: 0;}
.balance_member ul li    {display: inline-block;padding: 0 7px 5px 7px;}
.balance_member_in       {background: #ffffff;padding: 7px 0 10px 0;border-radius: 8px;}
.balance_member_in img   {width: 45%;display: block;margin: 0 auto;}
.balance_member_in span  {display: block;font-family: 'BebasNeueBold';font-size:12px;color:#484342;font-weight:normal;text-align:center;display:block;text-transform:uppercase;padding: 5px 0 3px;}
.balance_member_in small  {display: block;font-size: 7px;color:#484342;font-weight:normal;text-align:center;display:block;text-transform:uppercase;padding: 0px 0 3px;line-height: 8px;}
.balance_member_in .btn  {background:#4567e3;width:80%;height: 15px;margin: 1px auto 3px auto;font-family: 'Lato', sans-serif;font-size: 6px;color:#ffffff;font-weight:700;border:0 none;border-radius:10px;display:block;text-transform:uppercase;padding: 0;}
.mrgn                    {margin: 15px auto 3px auto !important;}
.in_after                {position: relative;}
.in_after:after          {content: '';position: absolute;left: -3px;top: -3px;width: 107%;height: 107%;border: 3px solid #bd8f3c;}

.red_slice               {display: inline-block;width: 100%;height: 100%;background: url(../../images/shape_red.png) no-repeat rgba(21,182,203,0.9);background-position: 3px 46px;}
/*---------------Inner15 Page Ends--------------*/


/*---------------Inner16 Page Starts--------------*/
.main_bg2				{background:#e5e4e4 url(../../images/main_bg2.jpg) no-repeat left top; padding:0 0 50px 0;}
.dashboard_block		{padding:0 0 50px 0; margin:0;}
.dashboard				{padding:75px 0 90px 0; margin:0;}
.dashboard h2			{font-family: 'Lato', sans-serif; font-size:50px; color:#010101; font-weight:900; line-height:55px; text-align:center; text-transform:uppercase; margin:0 0 62px 0; background:url(../../images/orenge_br.png) no-repeat bottom center; padding:0 0 15px 0;}
.dashboard ul			{padding:0; width:65%; margin:0 auto;}
.dashboard ul li 		{float:left; display:inline-block; width:422px; background:#fff; border:1px solid #b7b7b7; border-radius:15px; padding:45px 0;}
.dashboard ul li span	{display:block; font-family: 'Lato', sans-serif; font-size:24px; color:#000000; font-weight:700; line-height:30px; padding:0 0 0 170px; position:relative; text-transform:uppercase;}
.dashboard ul li span:after	{content:''; position:absolute; z-index:9; left:60px; top:-20px; background:url(../../images/icon_img1.png) no-repeat; width:86px; height:86px;}
.dashboard ul li:nth-child(2) span:after	{background:url(../../images/icon_img2.png) no-repeat; width:96px; height:90px;}


.nation_passport		{padding:0; margin:0;}
.top_bar				{background:#484342; position:relative; border-radius:30px 30px 0 0;}
.top_bar h4				{font-family: 'Lato', sans-serif; font-size:29px; color:#ffffff; font-weight:700; line-height:30px; text-align:center; line-height:51px; text-transform:uppercase;}
.setting				{position:absolute; z-index:9; left:42px; top:6px;}
.setting a				{display:inline-block; font-family: 'Lato', sans-serif; font-size:26px; color:#ffffff; font-weight:400; position:relative; padding:0 0 0 38px;} 	
.setting a:after		{content:''; position:absolute; z-index:9; left:0; top:1px; background:url(../../images/icon_img3.png) no-repeat; width:32px; height:32px;}

.nation_passport_bgr		{padding:0; margin:0; background:url(../../images/bg_img1.jpg) no-repeat;}
.nation_passport_left		{padding:0; margin:0;}
.nicolas					{width:100%; background:#4567e3; padding:35px 60px 30px 70px;}
.nicolas label				{font-family: 'Lato', sans-serif; font-size:22px; color:#ffffff; font-weight:700; line-height:30px; float:left; text-transform:uppercase;}
.nicolas input				{background:none; border:0 none; border-bottom:2px solid #dae1f9; width:74%; height:35px; float:right; font-family: 'Lato', sans-serif; font-size:29px; color:#ffffff; padding:0 10px; margin:-17px 0 0 0;}
.nicolas_left				{padding:0; margin:0; float:left; display:inline-block;}
.nicolas_left h3			{font-family: 'Lato', sans-serif; font-size:72px; color:#ffffff; font-weight:400; line-height:78px;} 
.nicolas_left .pacesetter	{width:210px; height:45px; background:#484342; font-family: 'Lato', sans-serif; font-size:29px; color:#ffffff; font-weight:700; border:0 none; border-radius:10px; text-transform:uppercase; -moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.nicolas_left .pacesetter:hover	{background:#343130;}
.nicolas_left p				{font-family: 'Lato', sans-serif; font-size:29px; color:#ffffff; font-weight:400; line-height:35px;}
.nicolas_right				{float:right; display:inline-block; padding:0; margin:0;}
.nicolas_right img			{display:block;}
.nicolas_right span			{display:block; font-family: 'Lato', sans-serif; font-size:29px; color:#ffffff; font-weight:400; padding:10px 0 0 0; text-align:center;}
.nicolas_btns				{padding:12px 0 0 0; margin:0;}
.nicolas_btns ul			{padding:0; margin:0 -5px; width:100%; display:inline-block;}
.nicolas_btns ul li 		{float:left; display:inline-block; width:20%; padding:0 5px;}
.nicolas_btns ul li a		{display:block; padding:0; margin:0; font-family: 'Lato', sans-serif; font-size:17px; color:#ffffff; font-weight:700; line-height:47px; background:#484342; text-align:center; border-radius:10px; text-transform:uppercase; -moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.nicolas_btns ul li a:hover	{background:#343130;}

.members_badges				{width:100%; background:#eeeeee; padding:25px 60px 32px 70px; border-top:10px solid #fff;}
.members_badges	label		{font-family: 'Lato', sans-serif; font-size:22px; color:#484342; font-weight:700; line-height:30px; float:left; text-transform:uppercase;}
.members_badges input 		{background:none; border:0 none; border-bottom:2px solid #484342; width:70%; height:35px; float:right; font-family: 'Lato', sans-serif; font-size:29px; color:#484342; padding:0 10px; margin:-17px 0 0 0;}	
.members_badges ul			{padding:30px 0 0 0; margin:0 -1.5%;}	
.members_badges	ul li 		{float:left; display:inline-block; margin:0 1.5%;}
.members_badges	ul li a		{display:block;}
.members_badges	p			{font-family: 'Lato', sans-serif; font-size:15px; color:#282828; font-weight:700; line-height:20px; text-align:justify; padding:60px 10px 0 0;}


.nation_passport_right		{padding:0 35px 0 55px; margin:0;}
.monthly_status				{width:100%; padding:35px 0 0 0; margin:0;}
.monthly_status label		{font-family: 'Lato', sans-serif; font-size:22px; color:#484342; font-weight:700; line-height:30px; float:left; text-transform:uppercase;}
.monthly_status input 		{background:none; border:0 none; border-bottom:1px solid #484342; width:50%; height:30px; float:right; font-family: 'Lato', sans-serif; font-size:22px; color:#484342; padding:0 10px; margin:-12px 0 0 0;}
.monthly_status ul			{padding:10px 0 0 0; margin:0 -2%;}
.monthly_status ul li 		{padding:0 2% 3% 2%; margin:0;}
.monthly_status ul li span	{background:#ffffff; font-family: 'BebasNeueRegular'; font-size:39px; color:#484342; font-weight:normal; text-align:center; line-height:45px; padding:18px 0 8px 0; display:block; border-radius:20px 20px 0 0;} 
.monthly_status ul li small	{background:#484342; font-family: 'Lato', sans-serif; font-size:16px; color:#ffffff; line-height:18px; font-weight:700; padding:5px 0; text-align:center; display:block; border-radius:0 0 20px 20px; text-transform:uppercase;}

.member_balance				{width:100%; padding:10px 0 0 0; margin:0;}
.member_balance label		{font-family: 'Lato', sans-serif; font-size:22px; color:#484342; font-weight:700; line-height:30px; float:left; text-transform:uppercase;}
.member_balance input 		{background:none; border:0 none; border-bottom:1px solid #484342; width:60%; height:30px; float:right; font-family: 'Lato', sans-serif; font-size:22px; color:#484342; padding:0 10px; margin:-12px 0 0 0;}
.member_balance ul			{padding:10px 0 0 0; margin:0 -2%;}
.member_balance ul li 		{padding:0 2% 0 2%; margin:0;}
.member_balance ul li .balance_con	{padding:18px 0 0 0; margin:0; background:#fff; border-radius:30px; min-height:268px;}
.member_balance ul li .balance_con img	{display:block; margin:0 auto;}
.member_balance ul li:nth-child(3) .balance_con img	{padding:8px 0 0 0;}
.member_balance ul li .balance_con span	{font-family: 'BebasNeueRegular'; font-size:33px; color:#484342; font-weight:normal; text-align:center; display:block; text-transform:uppercase; padding:8px 0 3px 0}
.member_balance ul li .balance_con small	{font-family: 'Lato', sans-serif; font-size:16px; color:#484342; line-height:20px; font-weight:700; display:block; text-align:center; text-transform:uppercase; padding:0 0 8px 0;}
.member_balance ul li .balance_con .transfer	{background:#4567e3; width:80%; height:32px; margin:0 auto 12px auto; font-family: 'Lato', sans-serif; font-size:12px; color:#ffffff; font-weight:700; border:0 none; border-radius:10px; display:block; text-transform:uppercase;}
.member_balance ul li .balance_con .transfer.shop	{margin:44px auto 12px auto;}
.member_balance ul li .balance_con .transfer:hover	{background:#2c55ee;}


.customer_btn		{width:403px; height:65px; background:#f15b20; margin:70px auto 60px auto; display:block; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; font-weight:400; text-align:center; border:0 none; border-radius:18px; text-transform:uppercase;}
.customer_btn:hover	{background:#e84c0f;}

.monthly_leaderboard	{padding:0 0 70px 0; margin:0;}
.sort_by	{position:absolute; z-index:99; right:30px; top:9px;}
.sort_by label	{font-family: 'Lato', sans-serif; font-size:18px; color:#ffffff; font-weight:700; float:left; display:inline-block; line-height:31px; padding:0 14px 0 0; margin:0;}
.sort_by .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn)	{float:left; display:inline-block; width:235px; border:1px solid #00b5cb; background:#ffffff; padding:0; margin:0; border-radius:5px;}
.sort_by .bootstrap-select.btn-group .dropdown-toggle .filter-option		{font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:20px; font-weight:400; padding:0 12px;}
.sort_by .btn-default	{position:relative;}
.sort_by .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:12px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.sort_by .caret		{border:0 none;}
.sort_by .btn	{padding:4px 0 4px 0; outline:none;}
.sort_by .btn-default:active:hover, .sort_by .btn-default.active:hover, .sort_by .open > .dropdown-toggle.btn-default:hover, .sort_by .btn-default:active:focus, .sort_by .btn-default.active:focus, .sort_by .open > .dropdown-toggle.btn-default:focus, .sort_by .btn-default:active.focus, .sort_by .btn-default.active.focus, .sort_by .open > .dropdown-toggle.btn-default.focus	{color:#00b5cb; background:#ffffff; border:1px solid #00b5cb;}
.sort_by .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {color:#00b5cb; background:#ffffff; border:1px solid #00b5cb;}
.sort_by .bootstrap-select.btn-group .dropdown-menu li a	{font-family: 'Lato', sans-serif; font-size:18px; color:#454543; line-height:25px; font-weight:400; outline:none; padding:3px 12px;}
.sort_by .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff;}

.nicolas_porter			{padding:0; margin:0;}
.nicolas_porter ul		{padding:0; margin:0 -1px;}
.nicolas_porter	ul li	{float:left; display:inline-block; width:25%; padding:0 1px; margin:0 0 2px 0;}

.nicolas_con					{width:100%; background:#4567e3; padding:10px 10px 10px 10px; border:7px solid #ffffff; border-left:9px solid #ffffff; border-right:9px solid #ffffff;}
/*.nicolas_con label				{font-family: 'Lato', sans-serif; font-size:10px; color:#ffffff; font-weight:700; line-height:15px; float:left; text-transform:uppercase;}
.nicolas_con input		{background:none; border:0 none; border-bottom:1px solid #dae1f9; width:62%; height:15px; float:right; font-family: 'Lato', sans-serif; font-size:18px; color:#ffffff; padding:0 10px; margin:-5px 0 0 0;}
*/
.nicolas_con_left				{padding:0; margin:0; float:left; display:inline-block;}
.nicolas_con_left h3			{font-family: 'Lato', sans-serif; font-size:31px; color:#ffffff; font-weight:400; line-height:32px; padding:0 0 10px 20px;} 
.nicolas_con_left h2			{font-family: 'Lato', sans-serif; font-size:55px; color:#ffffff; font-weight:400; line-height:60px; padding:0 0 0 20px;}
.nicolas_con_left .pacesetter	{width:90px; height:25px; background:#484342; font-family: 'Lato', sans-serif; font-size:13px; color:#ffffff; font-weight:700; border:0 none; border-radius:5px; text-transform:uppercase; margin:0 0 0 21px;  -moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.nicolas_con_left .pacesetter:hover {background:#343130;}
.nicolas_con_left p				{font-family: 'Lato', sans-serif; font-size:11px; color:#ffffff; font-weight:400; line-height:15px;}
.nicolas_con_right				{float:right; display:inline-block; padding:0; margin:0;}
.nicolas_con_right img			{display:block;}
.nicolas_con_btns				{padding:6px 0 0 0; margin:0;}
.nicolas_con_btns ul			{padding:0; margin:0 1px; width:100%; display:inline-block;}
.nicolas_con_btns ul li 		{float:left; display:inline-block; padding:0 4.5px; width:auto !important; border:none;}
.nicolas_con_btns ul li a		{display:block; padding:0; margin:0; font-family: 'Lato', sans-serif; font-size:12px; color:#ffffff; font-weight:300; line-height:22px; background:#484342; text-align:center; border-radius:5px; text-transform:uppercase; padding:0 5px; -moz-transition:all 0.3s ease-in-out; -ms-transition:all 0.3s ease-in-out; -o-transition:all 0.3s ease-in-out; -webkit-transition:all 0.3s ease-in-out; transition:all 0.3s ease-in-out;}
.nicolas_con_btns ul li a:hover	{background:#343130;}

.nicolas_con.bg_clr1            {background:#ffd029;}
.nicolas_con.bg_clr2            {background:#ff162c;}
.nicolas_con.bg_clr3            {background:#ff4616;}
/*---------------Inner16 Page Ends--------------*/

/*---------------Inner17 Page Starts--------------*/
.acct_information		{background:#ffffff; padding:0; margin:0;}
.acct_information_in	{width:80%; margin:0 auto;}
.form_block				{padding:60px 0 85px 0; margin:0;}
.form_block ul			{padding:0; margin:0 -40px;}
.form_block ul li 		{padding:0 40px; margin:0 0 20px 0;}
.form_block ul li label	{font-family: 'Lato', sans-serif; font-size:24px; color:#000000; font-weight:700; line-height:28px; display:block;}
.form_block ul li input	{width:100%; height:40px; border:3px solid #acacac; border-radius:8px; font-family: 'Lato', sans-serif; font-size:20px; color:#8d8c8c; font-weight:400; display:block; padding:0 10px;}
.birthday_main		{padding:0; margin:0 -2%;}
.birthday	{padding:0 2%; margin:0;}

.birthday .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn)	{float:left; display:inline-block; width:100%; background:#ffffff; padding:0; margin:0; border-radius:5px;}
.birthday .bootstrap-select.btn-group .dropdown-toggle .filter-option		{font-family: 'Lato', sans-serif; font-size:20px; color:#8d8c8c; line-height:20px; font-weight:400; padding:0 12px;}
.birthday .btn-default	{position:relative;}
.birthday .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:15px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.birthday .caret		{border:0 none;}
.birthday .btn	{padding:4px 0 4px 0; outline:none; border:3px solid #acacac; height:40px;}
.birthday .btn-default:active:hover, .birthday .btn-default.active:hover, .birthday .open > .dropdown-toggle.btn-default:hover, .birthday .btn-default:active:focus, .birthday .btn-default.active:focus, .birthday .open > .dropdown-toggle.btn-default:focus, .birthday .btn-default:active.focus, .birthday .btn-default.active.focus, .birthday .open > .dropdown-toggle.btn-default.focus	{color:#00b5cb; background:#ffffff; border:3px solid #acacac;}

.birthday .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {color:#00b5cb; background:#ffffff; border:3px solid #acacac;}
.birthday .bootstrap-select.btn-group .dropdown-menu li	{padding:0; margin:0;}
.birthday .bootstrap-select.btn-group .dropdown-menu li a	{font-family: 'Lato', sans-serif; font-size:20px; color:#8d8c8c; line-height:25px; font-weight:400; outline:none; padding:3px 10px;}
.birthday .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff;}

.language		{padding:0; margin:0;}
.language ul	{padding:0; margin:0;}		
.language ul li	{display:block; padding:0; margin:0 0 10px 0;}
.language ul li label	{display: inline-block;margin: 0;}
.language ul li label span {font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:20px; font-weight:400; background:url(../../images/unchecked2.png) no-repeat; cursor:pointer; padding:0 0 0 35px; margin:0; display:block;}		
.language ul li input[type=checkbox]:checked + span {background:url(../../images/checked.png) no-repeat;}		
.language ul li input {display:none;}
.save_btn		{width:80%; height:65px; border-radius:15px; display:block; background:#f15b20; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; line-height:25px; font-weight:400; border:0 none; text-transform:uppercase; margin:35px 0 0 0;}
.save_btn:hover	{background:#e84c0f;}
.empty_blk	{display:block;}

/*---------------Inner17 Page Ends--------------*/

/*---------------Inner18 Page Starts--------------*/
.your_intrest		{background:#ffffff;padding: 13px 0 0 60px;margin: -13px 0 0 0;border-radius: 0 0 18px 18px;}
.your_intrest_in	{width: 90%;margin:0 auto;padding: 40px 0 0 20px;}
.your_intrest_in h4 {font-size: 24px;line-height: 26px;color: #000000;font-weight: bold;padding: 0px 10px 10px 0;}
.your_intrest_in ul {width: 100%;display: inline-block;margin: 0 0 42px 0; font-size:0;}
.your_intrest_in ul li{padding: 0;}
.your_intrest_in ul li .chek_box1{padding: 0 0 10px 0;}
.your_intrest_in ul li label{display: inline-block;margin: 0;}
.your_intrest_in ul li span {font-size: 24px;line-height: 24px;color: #000000;font-weight: 400;padding: 0px 11px 3px 34px;position: relative;background: url(../../images/cheak_box1.png) no-repeat left top;background-position: 0px 3px;cursor: pointer;display: block;}
.your_intrest_in ul li input[type=checkbox]{display: none;}
.your_intrest_in ul li input[type=checkbox]:checked + span{background: url(../../images/cheak_box_cheak.png) no-repeat left top;background-position: 0px 3px;}
.spl_dates_in       {width: 195px;display: inline-block;padding: 4px 0 93px 0;margin: 0 30px 0 0;}

/*.spl_dates_in .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn)       {width: 100%;display: inline-block;margin: 0;}
*/
.save_chnages {width: 310px;height: 64px;background: #f15b20;font-weight: 400;font-size: 26px;color: #ffffff;border-radius: 10px;margin: 0 0 63px 0;border: none;text-transform: uppercase;}
.save_chnages:hover	{background:#e84c0f;}
.spl_dates_in .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn)	{width: 100%; height: 44px; display: inline-block;margin: 0;}
.spl_dates_in .bootstrap-select.btn-group .dropdown-toggle .filter-option		{font-family: 'Lato', sans-serif; font-size:20px; color:#8d8c8c; line-height:30px; font-weight:400; padding:0 12px;}
.spl_dates_in .btn-default	{position:relative;}
.spl_dates_in .btn-default:after	{content:''; position:absolute; z-index:9; right:0; top:18px; background:url(../../images/down_arrow1.png) no-repeat; width:22px; height:11px;}
.spl_dates_in .caret		{border:0 none;}
.spl_dates_in .btn	{padding:4px 0 4px 0; outline:none; border: 2px solid #acacac;}
.spl_dates_in .btn-default:active:hover, .spl_dates_in .btn-default.active:hover, .spl_dates_in .open > .dropdown-toggle.btn-default:hover, .spl_dates_in .btn-default:active:focus, .spl_dates_in .btn-default.active:focus, .spl_dates_in .open > .dropdown-toggle.btn-default:focus, .spl_dates_in .btn-default:active.focus, .spl_dates_in .btn-default.active.focus, .spl_dates_in .open > .dropdown-toggle.btn-default.focus	{color:#00b5cb; background:#ffffff; border:2px solid #acacac;}
.spl_dates_in .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {color:#00b5cb; background:#ffffff; border:2px solid #acacac;}
.spl_dates_in .bootstrap-select.btn-group .dropdown-menu li	{padding:0;}
.spl_dates_in .bootstrap-select.btn-group .dropdown-menu li a	{font-size:20px; color:#8d8c8c; line-height:24px; font-weight:400; outline:none; padding:3px 14px; outline:none;}
.spl_dates_in .bootstrap-select.btn-group .dropdown-menu	{background:#ffffff;}
.spl_dates_in .bootstrap-select.btn-group .dropdown-menu li a span.text	{font-size:20px; color:#8d8c8c; line-height:24px; font-weight:400; padding:0; background: none;}

/*---------------Inner18 Page Ends--------------*/

/*---------------Inner20 Page Starts--------------*/
.comsumer_dashboard	{padding:65px 0 0 0; margin:0;}
.dashboard_bar	{padding:10px 0 10px 0; margin:0 0 0 0; background:#ffffff; border-radius: 22px 22px 0 0; display:inline-block; width:100%;}
.dashboard_bar h3      {font-family: 'Lato', sans-serif; font-size: 30px;line-height: 32px;font-weight: bold;padding: 0 0 0 0;color: #ffffff;display: block; text-transform:uppercase; text-align:center;}

.dashboard_top	{padding:80px 74px 94px 74px; margin:0 0 45px 0; background:url(../../images/opacity_bg.png) repeat;}
.dashboard_top ul {padding:0; margin:0 -17px;}
.dashboard_top ul li {padding:0 17px; margin:0;}
.dashboard_top ul li span	{font-family: 'BebasNeueRegular'; font-size:80px; color:#454543; font-weight:normal; text-align:center; line-height:85px; padding:15px 0 8px 0; display:block; border-radius:30px 30px 0 0;}
.dashboard_top ul li small {background:#484342; font-family: 'Lato', sans-serif; font-size:28px; color:#ffffff; line-height:30px; font-weight:700; padding:5px 0; text-align:center; display:block; border-radius:0 0 30px 30px; text-transform:uppercase;}

.cust_rpt			{width:33%;}
.customer_reports	{padding:0; margin:0;}
.customer_reports h6 {font-family: 'Lato', sans-serif; font-size:29px; color:#ffffff; line-height:52px; font-weight:700; background:#454543; border-radius:20px 20px 0 0; text-align:center;} 
.customer_reports ul {padding:22px 20px 28px 20px; margin:0; background:url(../../images/opacity_bg.png) repeat;}
.customer_reports ul li {border:3px solid #929292; padding:0; margin:0 0 18px 0; display:block; border-radius:15px;}
.customer_reports ul li p {font-family: 'Lato', sans-serif; font-size:24px; color:#000000; line-height:25px; font-weight:700; padding:15px 10px 15px 118px; position:relative;}
.customer_reports ul li p span {font-family: 'Lato', sans-serif; font-size:20px; color:#a3a3a3; font-weight:900; display:block;}
.customer_reports ul li p span small {color:#454543;}
.customer_reports ul li p:after	{content:''; position:absolute; z-index:9; left:16px; top:9px; background:url(../../images/icon51.png) no-repeat; width:81px; height:63px;}
.customer_reports ul li:nth-child(2) p:after	{background:url(../../images/icon52.png) no-repeat;}
.customer_reports ul li:nth-child(3) p:after	{background:url(../../images/icon53.png) no-repeat;}
.customer_reports ul li:nth-child(4) p:after	{background:url(../../images/icon54.png) no-repeat;}
.customer_reports ul li:nth-child(5) p:after	{background:url(../../images/icon55.png) no-repeat;}
.customer_reports ul li:nth-child(5) p			{padding:28px 10px 28px 118px;} 

.trend_chart	{width:67%;}
.graph_sec		{padding:82px 0 60px 0; margin:0; background:#f7f7f7;}
.graph_sec img	{display:block; margin:0 auto; height:auto;}
.graph_sec ul	{padding:35px 0 0 80px; margin:0;}
.graph_sec ul li {float:left; display:inline-block; margin:0 37px 0 0;}
.graph_sec ul li span	{font-family: 'Lato', sans-serif; font-size:16px; color:#a3a3a3; line-height:34px; font-weight:400; padding:0 0 0 48px; position:relative;}
.graph_sec ul li span:after	{content:''; position:absolute; z-index:9; left:0; top:-7px; background:#f16f20; width:36px; height:34px;}
.graph_sec ul li:nth-child(2) span:after	{background:#00b5cb;}
.graph_sec ul li:nth-child(3) span:after	{background:#33d369;}

.product_blk ul li.dash	{width:15.66%;}
.dash_btns_blk	{width:75%; margin:0 auto;}
.nation_passport_right.passport	{background:url(../../images/bg_img1.jpg) no-repeat; background-size:cover;}


/*---------------Inner20 Page Ends--------------*/








/*---------------Inner10 Page Starts--------------*/
.popular_deals    {padding: 0 0 0 30px;margin: 0 0 35px 0;}
.deals_top        {border-radius:22px 22px 0 0;padding: 16px 10px 14px 10px;}
.deals_top h6     {color: #ffffff;font-family: 'Helvetica75Bold';font-size: 16px;line-height: 18px;display: inline-block;padding: 3px 0 0px 0;}
.deals_end        {background: #ffffff;padding: 15px 7px 25px 9px;position: relative;}
.deals_end:after  {content: '';position: absolute;width: 90%;height: 1px;background: #f6f6f6;left: 0;right: 0;margin: 0 auto;bottom: 0;}
.end_left         {display: inline-block;}
.end_left img     {border: 1px solid #f6f6f6;border-radius: 4px;}
.end_left a       {font-size:16px;line-height: 18px;color: #ffffff;font-weight: bold;background: #e23173;padding: 5px 22px;border-radius: 4px;display: inline-block;margin: 10px 0 0 0;}
.end_left p       {display: inline-block;color: #707373;font-size: 15px;line-height: 18px;font-weight: 400;}
.end_right        {display: inline-block;padding: 0;}
.end_right small  {display: inline-block;padding: 7px 12px;background: #fff5c1;position: relative;margin: 0;font-size: 12px;line-height: 12px;}
.end_right small:after {content: '';position: absolute;right: -11px;top: 0;width: 5px;height: 5px;background: #c7c7c7;border-radius: 100%;top: 10px;}
.end_right span   {display: inline-block;padding: 7px 10px 7px 44px;font-size: 11px;line-height: 13px;position: relative;vertical-align: top;}
.end_right span:after{content: '';position: absolute;left: 16px;top: 5px;background: url(../../images/icon34.png) no-repeat center;width: 22px;height: 18px;}
.end_right strong  {display: block;color: #e23173;font-size: 15px;line-height: 18px;font-weight: bold;padding: 0 0 10px 10px;}
.end_right p       {display: block;color: #6d676c;font-size: 15px;line-height: 18px;font-weight: 400;padding: 0 0 10px 10px;}
.end_right em      {display: block;color: #6d676c;font-size: 13px;line-height: 15px;font-weight: 400;font-style: normal;padding: 0 0 0 10px;}
.end_right .product ul{margin: 0;padding: 10px 0 0 0;}
.end_right .product ul li{float: left;display: inline-block;margin: 0;width:  20% !important;}
.end_right .product ul li a img{width: 77%;}
/*---------------Inner10 Page Ends--------------*/

/*---------------Inner11 Page Starts--------------*/
.right_block .direct_comsumer {width: 100%;text-align: center;padding: 0;}
.right_block .direct_comsumer h1{padding: 0 0 15px 0;}
.right_block .direct_comsumer p {font-size: 18px;line-height: 22px;color: #000000;padding: 32px 45px 69px 45px;}
.video_sec                    {display: block;padding: 0 0px 0 13px;}
.video_sec a                  {display: inline-block;}
/*---------------Inner11 Page Ends--------------*/


/*---------------Inner14 Page Starts--------------*/
/*.connection_map_in    {text-align: center;}
.connection_map_in a  {display: inline-block;}
.connection_map_in ul {width: 100%;display: inline-block;padding: 28px 0 0 0;margin: 0;}
.connection_map_in ul li{display: block;padding: 0 0 2px 0;}
.left_no              {display: inline-block;width: 7.8%;padding: 20px 0 0 0;}
.left_no strong       {font-size: 53px;line-height: 55px;font-family: 'BebasNeueBold';}
.map_middle              {display: inline-block;width: 84.4%;text-align: left;padding: 0 2% 0 6%;}
.nicolas_in           {display: inline-block;width: 89px;height: 89px;position: relative;border-radius: 100%;text-align: center;padding: 0.4% 0;margin: 0 5px 10px 5px;vertical-align: top;cursor: pointer;}
.nicolas_in:after     {content: '';position: absolute;left: 3px;top: 3px;width: 93.2%;height: 93.2%;border: 3px solid #ffffff;border-radius: 100%;}
.nicolas_in:before    {content: '';position: absolute;left: 0;top: 0;width: 100%;height: 100%;border: 3px solid #393535;border-radius: 100%;z-index: 2;}
.nicolas_in h2        {font-size: 81px;line-height: 81px;display: inline-block;color: #ffffff;font-family: 'BebasNeueBold';}
.nicolas_up           {position: absolute;top: 0;left: 0;width: 100%;height: 100%;border-radius: 100%;padding: 28% 0;}
.nicolas_up h4        {font-size: 37px;line-height: 37px;display: inline-block;color: #ffffff;font-family: 'BebasNeueBold';position: relative;z-index: 9;}
.nicolas_up h6        {font-size: 22px;line-height: 24px;display: block;color: #ffffff;font-family: 'BebasNeueBold';position: relative;z-index: 9;text-transform: uppercase;padding: 8% 0 1% 0;}
.nicolas_4line        {position: absolute;width: 100%;height: 100%;left: 0;top: 0;}
.nicolas_4line:after  {content: '';position: absolute;left: 50%;top: 0;width: 3px;height: 100%;background: #393535;z-index: 2;transform: translateX(-50%);-webkit-transform: translateX(-50%);-moz-transform: translateX(-50%);-ms-transform: translateX(-50%);-o-transform: translateX(-50%);}
.nicolas_4line:before {content: '';position: absolute;left: 0%;top: 50%;width: 100%;height: 3px;background: #393535;z-index: 2;transform: translateY(-50%);-webkit-transform: translateY(-50%);-moz-transform: translateY(-50%);-ms-transform: translateY(-50%);-o-transform: translateY(-50%);}

.nicolas_4line1        {position: absolute;width: 100%;height: 100%;left: 0;top: 0;}
.nicolas_4line1:after  {content: '';position: absolute;left: 50%;top: 0;width: 3px;height: 100%;background: #393535;z-index: 2;transform: translateX(-50%) rotate(45deg);-webkit-transform: translateX(-50%) rotate(45deg);-moz-transform: translateX(-50%) rotate(45deg);-ms-transform: translateX(-50%) rotate(45deg);-o-transform: translateX(-50%) rotate(45deg);}
.nicolas_4line1:before {content: '';position: absolute;left: 0%;top: 50%;width: 100%;height: 3px;background: #393535;z-index: 2;transform: translateY(-50%) rotate(45deg);-webkit-transform: translateY(-50%) rotate(45deg);-moz-transform: translateY(-50%) rotate(45deg);-ms-transform: translateY(-50%) rotate(45deg);-o-transform: translateY(-50%) rotate(45deg);}
.for_after             {position: relative;}
.for_after:after       {content: '';position: absolute;right: -45%;width: 73%;height: 2px;background: #ffffff;top: 58%;}

.for_before             {position: relative;}
.for_before:before      {content: '';position: absolute;left: -45%;width: 73%;height: 2px;background: #ffffff;top: 58%;}
.for_border             {width: 65px;height: 65px;border: 2px solid;border-radius: 100%;display: inline-block;padding: 3% 5% 3% 0%;text-align: center;}

.pop_up_trained         {width: 650px;position: absolute;top: 63px;left: 0;z-index: 99;display: none; cursor:default;}
.pop_up_trained:after   {content: "\f0d8";position: absolute;top: -31px;left: 32px;font-family: FontAwesome;color: #494543;font-size: 36px;}
.trained_head           {background: #494543;border-radius: 20px 20px 0 0;position: relative;text-align: center;padding: 6px 0;}
.trained_head h5        {font-size: 20px;line-height: 22px;color: #ffffff;display: block;}
.trained_head .btn      {position: absolute;right: 10px;top: 4px;border-radius: 100%;width: 25px;height: 25px;text-align: center;padding: 0;font-size: 16px;font-weight: bold;background: #989592;color: #fff;outline: none;}
.trained_middle         {display: block;}
.trained_middle h6      {font-size: 10px;line-height: 12px;font-weight: bold;position: relative;text-align: left;}
.trained_middle h6:after{content: '';position: absolute;right: 0;top: 50%; width: 69%;height: 1px;background: #ffffff;}
.trained_middle_left    {display: inline-block;width: 54.7%;border-right: 3px solid #ffffff;}
.trained_middle_top     {padding: 8px 25px;}
.trained_middle_top_left{display: inline-block;width: 70%;padding: 3px 0 0px 0; text-align:left;}
.trained_middle_top_left h3{font-size: 30px;color:#ffffff;font-weight:400;line-height: 32px;padding: 0 0 1px 0;}
.trained_middle_top_left a{font-size: 12px;color:#ffffff;font-weight: bold;line-height: 14px;padding: 2px 10px 3px 10px;display: inline-block;background: #494543;border-radius: 5px;}
.trained_middle_top_left a:hover {background:#343130;}

.trained_middle_top_left small{font-size: 12px;color:#ffffff;font-weight: 400;line-height: 14px;padding: 0px 10px 0px 4px;display: block; text-align:left;}
.trained_middle_top_bottom {display: block;padding: 5px 0 5px 0;}
.trained_middle_top_bottom ul{width: 100%;display: inline-block;margin: 0;}
.trained_middle_top_bottom ul li{display: inline-block;width: 20%;float: left;padding: 0 3px;}
.trained_middle_top_bottom ul li a{display:block; padding:0; margin:0; font-family: 'Lato', sans-serif; font-size:7px; color:#ffffff; font-weight:700; line-height:20px; background:#484342; text-align:center; border-radius:5px; text-transform:uppercase;}
.trained_middle_top_bottom ul li a:hover {background:#343130;}
.trained_middle_bottom  {padding: 8px 28px 17px 28px;border-top: 3px solid #ffffff;}
.trained_middle_bottom h6{margin: 0 0 4px 0;}
.trained_middle_bottom h6:after{background: #7c7c7c;}
.trained_middle_bottom ul{width: 100%;display: inline-block;padding: 10px 0 18px 0;margin: 0;}
.trained_middle_bottom ul li{width: 20%;display: inline-block;float: left;padding: 0 3px;}
.trained_middle_bottom p{font-size: 8px;line-height: 10px;font-weight: 400;color:#484342;display: block;text-align: center;}

.trained_middle_top_right{display: inline-block;width: 30%;}
.trained_middle_top_right img{width: 100%;height: auto;}

.trained_middle_right   {display: inline-block;width: 45.3%;padding: 8px 15px 36px 15px;background: url(../../images/background1.png) no-repeat center;background-size: cover;}
.trained_middle_right h6:after   {background: #7c7c7c;width: 48%;}
.trained_middle_right ul       {display: inline-block;width: 100%;padding: 7px 0 0 0;margin: 0;}
.trained_middle_right ul li    {display: inline-block;padding: 0 7px 5px 7px;}
.monthly_stats                 {display: block;background: #ffffff;border-radius: 8px 8px;}
.monthly_stats span            {font-family: 'BebasNeueBold';font-size: 16px;line-height: 18px;color: #494543;display: block;text-align: center;padding: 8px 0 2px 0;}
.monthly_stats small           {font-size: 7px;line-height: 9px;color: #ffffff;display: block;text-align: center;padding: 0;background: #494543;text-transform: uppercase;border-radius: 0 0 8px 8px;padding: 3px 0 2px 0;}
.balance_member                 {}
.balance_member h6:after        {width: 60%;}
.balance_member ul       {display: inline-block;width: 100%;padding: 4px 0 0 0;margin: 0;}
.balance_member ul li    {display: inline-block;padding: 0 7px 5px 7px;}
.balance_member_in       {background: #ffffff;padding: 7px 0 10px 0;border-radius: 8px;}
.balance_member_in img   {width: 45%;display: block;margin: 0 auto;}
.balance_member_in span  {display: block;font-family: 'BebasNeueBold';font-size:12px;color:#484342;font-weight:normal;text-align:center;display:block;text-transform:uppercase;padding: 5px 0 3px;}
.balance_member_in small  {display: block;font-size: 7px;color:#484342;font-weight:normal;text-align:center;display:block;text-transform:uppercase;padding: 0px 0 3px;line-height: 8px;}
.balance_member_in .btn  {background:#4567e3;width:80%;height: 15px;margin: 1px auto 3px auto;font-family: 'Lato', sans-serif;font-size: 6px;color:#ffffff;font-weight:700;border:0 none;border-radius:10px;display:block;text-transform:uppercase;padding: 0;}
.balance_member_in .btn:hover	{background:#2c55ee;}

.mrgn                    {margin: 15px auto 3px auto !important;}
.in_after                {position: relative;}
.in_after:after          {content: '';position: absolute;left: -3px;top: -3px;width: 107%;height: 107%;border: 3px solid #bd8f3c;}
.pop_open	{z-index:3;}*/

/*---------------Inner14 Page Ends--------------*/



/*---------------Inner20 Page Starts--------------*/

.back_btn		{position:absolute; z-index:9; left:22px; top:10px; font-family: 'Lato', sans-serif; font-size:21px; color:#ffffff; line-height:24px; font-weight:700; text-transform:uppercase;}
.back_btn:hover	{color:#ffffff;}

.transaction_report		{padding:0; margin:0;}
.available_cards_title	{padding:0 0; margin:0; background:#454543; border-top:2px solid #383836;}
.available_cards_title ul {padding:0; margin:0;}
.available_cards_title ul li {padding:0; margin:0 0 0 0; float:left; display:inline-block;}
.available_cards_title ul li span {display:block; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; line-height:62px; font-weight:700; padding:0 0 0 0; text-align:center;}
.available_cards_title ul li:nth-child(1)	{width:20%;}
.available_cards_title ul li:nth-child(2)	{width:28%;}
.available_cards_title ul li:nth-child(3)	{width:28%;}
.available_cards_title ul li:nth-child(4)	{width:24%;}


.movie_ticket			{padding:0 0; margin:0; background:#ffffff;}
.movie_ticket ul		{padding:0 0; margin:0;}
.movie_ticket ul li 	{padding:0; display:block; margin:0 0 0 0; border-top:2px solid #b7b7b7;}
.movie_ticket ul li span {font-family: 'Lato', sans-serif; font-size:20px; color:#000000; line-height:65px; font-weight:400; padding:0 0 0 0; display:block; text-align:center; float:left; display:inline-block;}
.movie_ticket ul li em	{font-family: 'Lato', sans-serif; font-size:20px; color:#000000; line-height:65px; font-weight:400; padding:0 0 0 105px; font-style:normal; position:relative; float:left; display:inline-block;}
.movie_ticket ul li em:after	{content:''; position:absolute; z-index:99; left:22px; top:12px; background:url(../../images/movie_icon.png) no-repeat; width:68px; height:42px;}

.movie_ticket ul li em	{width:20%;}
.movie_ticket ul li span:nth-child(2)	{width:28%;}
.movie_ticket ul li span:nth-child(3)	{width:28%;}
.movie_ticket ul li span:nth-child(4) 	{width:24%;}


.movie_ticket.empty_bk	{min-height:65px; border-top:2px solid #b7b7b7;}

.orange_btn_blk		{padding:90px 0 60px 0; margin:0;}
.orange_btn_blk ul	{padding:0; margin:0 -1%;}
.orange_btn_blk ul li 	{padding:0 2%; margin:0;}
.orange_btn_blk ul li .buy_gift	{background:#f15b20; width:100%; height:65px; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; font-weight:400; text-transform:uppercase; display:block; border-radius:15px; border:0 none;}
.orange_btn_blk ul li .buy_gift:hover	{background:#e84c0f;}

/*---------------Inner 20 Page Ends--------------*/
/*---------------Inner 21 Page Starts--------------*/

.available_cards_title.order	{}
.available_cards_title.order ul li:nth-child(1) 	{width:20%;}
.available_cards_title.order ul li:nth-child(2) 	{width:40%;}
.available_cards_title.order ul li:nth-child(3) 	{width:25%;}
.available_cards_title.order ul li:nth-child(4) 	{width:15%;}

.movie_ticket.order2	{}
.movie_ticket.order2 ul li span:nth-child(1)  	{width:20%;}
.movie_ticket.order2 ul li span:nth-child(2) 	{width:40%;}
.movie_ticket.order2 ul li span:nth-child(3) 	{width:25%;}
.movie_ticket.order2 ul li span:nth-child(4)	{width:15%;}

.orange_back_btn	{background:#f15b20; width:360px; height:65px; font-family: 'Lato', sans-serif; font-size:24px; color:#ffffff; font-weight:400; text-transform:uppercase; display:block; border-radius:15px; border:0 none; margin:57px auto 0 auto;}
.orange_back_btn:hover	{background:#e84c0f;}

/*---------------Inner 21 Page Ends--------------*/



.gift_info{
    text-align: center;
}


.never_forgot_under .apply_trugiftcard #use_trugiftcard{
    width: 20px;
    vertical-align: middle;
    height: 17px;
}

.never_forgot_under .apply_trugiftcard label{
    font-size: 12px;
    vertical-align: -webkit-baseline-middle;
    display: inline-block !important;
}

.never_forgot_under .apply_trugiftcard img{
    vertical-align: super;
}





