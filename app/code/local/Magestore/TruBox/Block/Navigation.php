<?php

class Magestore_TruBox_Block_Navigation extends Mage_Core_Block_Template
{
    protected $_links = array();
    protected $_activeLink = false;
    
    public function addLink($name, $path, $label, $enable = true, $order = 0)
    {
        while (isset($this->_links[$order])) {
            $order++;
        }
        
        $this->_links[$order] = new Varien_Object(array(
            'name'  => $name,
            'path'  => $path,
            'label' => $label,
            'enable'    => $enable,
            'order'     => $order,
            'url'   => $this->getUrl($path)
        ));
        
        return $this;
    }

    public function getCategories()
    {
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('display_trubox', 1)
            ->setOrder('order_trubox', 'desc')
            ;
        return $categories;
    }

    public function getCategoryLink($category_id)
    {
        return $this->getUrl('mytrubox/*/category', array('id' => $category_id));
    }
    
    /**
     * get Sorted links (by order)
     * 
     * @return array
     */
    public function getLinks()
    {
        ksort($this->_links);
        return $this->_links;
    }

    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }
    
    /**
     * Check activate link
     * 
     * @param string link
     * @return boolean
     */
    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->getAction()->getFullActionName('/');
        }
        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        }
        return false;
    }
    
    /**
     * Repare complete path
     * 
     * @param string $path
     * @return string
     */
    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
            case 2:
                $path .= '/index';
        }
        return $path;
    }


    public function isSpecialOccasion()
    {
        $is_enable = false;
        if (Mage::helper('core')->isModuleOutputEnabled('Magestore_SpecialOccasion')) {
            $is_enable = Mage::helper('specialoccasion')->isEnable();
        }
        return $is_enable;
    }
}
