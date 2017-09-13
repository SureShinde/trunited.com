<?php
class Smartwave_GiftCardView_IndexController extends Mage_Core_Controller_Front_Action
{
    
    public function indexAction(){
        //Get current layout state
        $this->loadLayout();          
        $this->renderLayout();
    }
    public function globalAction(){
        //Get current layout state
        $params = $this->getRequest()->getParams();
        $url = $params['url'];
        $url = str_replace('.html', '', $url);
        $products = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter(
                        array(array('attribute' => 'url_key', 'like' => $url))
                    )
                    ->load();
        if (count($products)<=0){
            $products = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter(
                        array(array('attribute' => 'sku', 'like' => $url))
                    )
                    ->load();
        }
        if (count($products)>0){
            foreach($products as $product){
                $product = $product;
                break;
            }
        }

        $this->loadLayout();   
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'gift_card_detail',
            array('template' => 'page/gift_card_detail.phtml')
        );
        Mage::register('current_product',$product);
        $name = ($product) ? $product->getName() : '404 page not found';
        $this->getLayout()->getBlock('head')->setTitle($name);
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }
}