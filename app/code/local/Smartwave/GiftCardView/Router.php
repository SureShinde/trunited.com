<?php 
class Smartwave_GiftCardView_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer){
        
        $front = $observer->getEvent()->getFront();
        $front->addRouter('giftcardview', $this);
        return $this;
    }
    public function match(Zend_Controller_Request_Http $request){

        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $pathInfo = trim($request->getPathInfo(), '/');
        $params = explode('/', $pathInfo);
        
        //if module is market and controller is index
        if(isset($params[0]) && strtolower($params[0]) == 'giftcardview' ) {
            
             //if the action is index - proceed as normal
             if (!isset($params[1]) || $params[1] == 'index') {
                 return false; //standard router will pick it up
             }
             
             //if action is not index, map the request to the `globalAction`
             if (isset($params[1]) && $params[1] != 'index') {
                
                 $request->setModuleName('giftcardview')  
                     ->setControllerName('index')
                     ->setActionName('global')
                     ->setParam('url',$params[1]);
                 $request->setAlias(
                     Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                     $pathInfo
                 );
                return true;
            }
        }
        return false;
    }
}