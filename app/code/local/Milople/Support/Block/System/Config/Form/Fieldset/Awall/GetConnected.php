<?php
/**
* 
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your 
* needs please contact us to https://www.milople.com/magento-extensions/contacts/ 
* 
* @category    Ecommerce
* @package     Milople_Support
* @copyright   Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url	       http://www.milople.com/
*
**/

class Milople_Support_Block_System_Config_Form_Fieldset_Awall_GetConnected extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$html .='<div><p>Connect with us for new extensions, themes, free upgrades, etc.</p></div>';
		$html .='<table>
   					<tr>
				    	<td>Facebook:</td>
				        <td><a target="_blank" href="https://www.facebook.com/Milople">https://www.facebook.com/Milople</a></td>
				    </tr>
	
					<tr>
				    	<td>Twitter:</td>
				        <td><a target="_blank" href="https://twitter.com/milople">https://twitter.com/milople</a></td>
				    </tr>
	
					<tr>
				    	<td>Google+:</td>
				        <td><a target="_blank" href="https://plus.google.com/+MilopleInc/">https://plus.google.com/+MilopleInc/</a></td>
				    </tr>
					
					<tr>
				    	<td>SlideShare:</td>
				        <td><a target="_blank" href="http://www.slideshare.net/indieswebs/">http://www.slideshare.net/indieswebs/</a></td>
				    </tr>
				</table>';

		$html .='
		<div style="margin-left:24px; margin-top:28px; width:300px;">
        	<div class="facebook" style="float:left; margin-bottom:5px;">
				<script>(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, "script", "facebook-jssdk"));</script>
				<div id="fb-root" style="float:left;"></div>
				<div style="float:left;" class="fb-like" data-href="https://www.facebook.com/Milople" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
          	</div>
          	<div class="twitter" style="float:left;">
             	<a href="https://twitter.com/milople" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @Milople</a>
             	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>             
          	</div> 
          	<div class="google" style="float:left; margin-left:20px;">
            	<div class="g-plusone" data-size="medium" data-href="https://plus.google.com/+MilopleInc/"></div>
                	<script type="text/javascript">
						(function() 
						{
							var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
							po.src = "https://apis.google.com/js/plusone.js";
							var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
						})
						();
	               </script>
    	        </div>
        	</div>';

		$html .= $this->_getFooterHtml($element);
		return $html;
	}

}