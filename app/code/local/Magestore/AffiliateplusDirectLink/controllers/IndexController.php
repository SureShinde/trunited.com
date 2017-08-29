<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusDirectLink Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        $this->loadLayout();
        $this->renderLayout();
    }

    public function addDomain() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $domaininput = $this->getRequest()->getParam('source_page');
        $domain = Mage::helper('affiliateplusdirectlink')->refineDomain($domaininput);
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $model = Mage::getModel('affiliateplusdirectlink/domain')->saveDomainAccount($domain, $account->getId());
        return $model;
    }

    public function deleteDomainAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $id = $this->getRequest()->getParam('id');
        try {
            $model = Mage::getModel('affiliateplusdirectlink/domain')->load($id);
            Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($model->getId(), 3);
            $model->delete();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliateplusdirectlink')->__('Domain link has been successfully deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    public function addDirectLinkFormAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $block = Mage::getBlockSingleton('affiliateplusdirectlink/adddirectlink');
        $block->setDomainId($this->getRequest()->getParam('id'));

        $block->setDirectlink($this->getRequest()->getParam('directlink'));
        $this->getResponse()->setBody($block->toHtml());
    }

    public function deleteDirectLinkAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $id = $this->getRequest()->getParam('id');
        try {
            Mage::getModel('affiliateplusdirectlink/directlink')->load($id)->delete();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliateplusdirectlink')->__('Direct link has been successfully deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    public function verifyFormAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $block = Mage::getBlockSingleton('affiliateplusdirectlink/verify');
        $block->setDomain($this->getRequest()->getParam('id'));
        $this->getResponse()->setBody($block->toHtml());
    }

    public function addDirectLinkAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $directlinkId = $this->getRequest()->getParam('directlink');
        if (Mage::getModel('affiliateplusdirectlink/directlink')->isDirectLinkExited($directlinkId)) {
            Mage::getSingleton('core/session')->addError('Direct link to this page has already existed!');
        } else {
            $domain = $this->addDomain();
            $data = $this->getRequest()->getParams();
            $data['source_page'] = Mage::helper('affiliateplusdirectlink')->refineSourcePage($data['source_page']);
            $data['domain_id'] = $domain->getId();
            $data['status'] = $domain->getStatus();
            if ($data['use_link_banner']) {
                $link = Mage::getModel('affiliateplus/banner')->load($data['banner_id'])->getLink();
                if (!$link)
                    $link = Mage::getUrl();
                $data['link'] = $link;
            }
            $model = Mage::getModel('affiliateplusdirectlink/directlink');
            $model->setData($data);
            if ($directlinkId) {
                $model->setId($directlinkId);
            }
            try {
                $model->save();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliateplusdirectlink')->__('Your direct link has been successfully saved.'));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function verifyAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $model = Mage::getModel('affiliateplusdirectlink/domain');
        $domain = $model->load($this->getRequest()->getParam('domainId'));
        $web = Mage::helper('affiliateplusdirectlink')->refineDomain(Mage::getUrl());
        $name = $domain->getDomain();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $verifynamehtml = md5($account->getId() . $name . '1905') . '.html';
        $url = 'http://' . $name . '/verify_domain_' . $verifynamehtml;
        $check = FALSE;
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($httpCode == 200) {
            if (strstr($response, $web.' verify '.$name . $verifynamehtml))
                $check = TRUE;
        }
        if ($check) {
            try {
                $domain->setStatus(2)->save();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliateplusdirectlink')->__('Your domain has been successfully verified'));
                $collection = $model->getDomainOtherAccountByName($name, $account->getId());
                //gưi mail thông báo
                $domain->sendMailDomainDisable($domain->getDomain(), $domain->getAccountId());
                foreach ($collection as $value) {
                    $value->setStatus(1)->save();
                    Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($value->getId(), 1);
                }
                Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($domain->getId(), 2);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        } else {
            Mage::getSingleton('core/session')->addError(Mage::helper('affiliateplusdirectlink')->__('Verification failed for %s using the HTML file method (less than a minute ago). We encountered an error looking up your site%ss domain name.','http://' . $name . '/','\''));
        }
        $this->_redirect('*/*/index');
    }

    public function downloadFileAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $domain = $this->getRequest()->getParam('domain');
        $web = Mage::helper('affiliateplusdirectlink')->refineDomain(Mage::getUrl());
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $verifynamehtml = md5($account->getId() . $domain . '1905') . '.html';
        $content = '<html><body><h1>' .$web. ' verify '. $domain . $verifynamehtml . '</h1></body></html>';
        $this->_prepareDownloadResponse('verify_domain_'.$verifynamehtml, $content);
    }

    public function getCodeAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $block = Mage::getBlockSingleton('affiliateplusdirectlink/bannercode');
        $block->setDirectlink($this->getRequest()->getParam('directlink'));
        $this->getResponse()->setBody($block->toHtml());
    }

    public function getDirectLinkDefaultAction() {
        if (Mage::helper('affiliateplusdirectlink')->isPluginDisable())
            return $this->_redirect('affiliates/index/index/');
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $banner_id = $this->getRequest()->getParam('banner_id');
        $link = Mage::getModel('affiliateplus/banner')->load($banner_id)->getLink();
        if (!$link)
            $link = Mage::getUrl();
        $this->getResponse()->setBody($link);
    }

    /**
     * Declare headers and content file in responce for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     *                              that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile = true;
                $file = $content['value'];
                $contentLength = filesize($file);
            }
        }

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->setHeader('Last-Modified', date('r'));

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }

}