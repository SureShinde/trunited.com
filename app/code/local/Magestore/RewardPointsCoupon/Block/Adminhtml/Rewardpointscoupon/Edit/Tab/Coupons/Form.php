<?php

class Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Edit_Tab_Coupons_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * Prepare coupon codes generation parameters form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();

        /**
         * @var Mage_SalesRule_Helper_Coupon $couponHelper
         */
        $couponHelper = Mage::helper('rewardpointscoupon/coupon');

        $model = Mage::registry('current_rewardpoints_coupon_template');
        $template_id = $model->getId();

        $form->setHtmlIdPrefix('coupons_');

        $gridBlock = $this->getLayout()->getBlock('rewardpointscoupon_edit_tab_coupons_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset = $form->addFieldset('information_fieldset', array('legend' => Mage::helper('salesrule')->__('Coupon Code  Information')));
        $fieldset->addClass('ignore-validate');

        $fieldset->addField('template_id', 'hidden', array(
            'name' => 'template_id',
            'value' => $template_id
        ));

        $fieldset->addField('qty', 'text', array(
            'name' => 'qty',
            'label' => Mage::helper('rewardpointscoupon')->__('Coupon Qty'),
            'title' => Mage::helper('rewardpointscoupon')->__('Coupon Qty'),
            'required' => true,
            'class' => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset->addField('length', 'text', array(
            'name' => 'length',
            'label' => Mage::helper('rewardpointscoupon')->__('Code Length'),
            'title' => Mage::helper('rewardpointscoupon')->__('Code Length'),
            'required' => true,
            'note' => Mage::helper('rewardpointscoupon')->__('Excl. prefix, suffix and spacing.'),
            'class' => 'validate-digits validate-greater-than-zero',
            'value' => 12,
            'onclick' => 'this.select()'
        ));

        $fieldset->addField('format', 'select', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Code Format'),
            'name' => 'format',
            'options' => $couponHelper->getFormatsList(),
            'required' => true,
        ));

        $fieldset->addField('prefix', 'text', array(
            'name' => 'prefix',
            'label' => Mage::helper('rewardpointscoupon')->__('Code Prefix'),
            'title' => Mage::helper('rewardpointscoupon')->__('Code Prefix'),
            'value' => $this->__('REWARD'),
            'onclick' => 'this.select()',
        ));

        $fieldset->addField('suffix', 'text', array(
            'name' => 'suffix',
            'label' => Mage::helper('rewardpointscoupon')->__('Code Suffix'),
            'title' => Mage::helper('rewardpointscoupon')->__('Code Suffix'),
        ));

        $fieldset->addField('dash', 'text', array(
            'name' => 'dash',
            'title' => Mage::helper('rewardpointscoupon')->__('Dash Every X Characters '),
            'label' => Mage::helper('rewardpointscoupon')->__('Dash Every X Characters '),
            'note' => Mage::helper('rewardpointscoupon')->__('Separate every X characters with a dash. If empty, there is no separation.'),
            'class' => 'validate-digits'
        ));

        $idPrefix = $form->getHtmlIdPrefix();
        $generateUrl = $this->getGenerateUrl();

        $fieldset->addField('generate_button', 'note', array(
            'text' => $this->getButtonHtml(
                    Mage::helper('rewardpointscoupon')->__('Generate'), "generateCouponCodes('{$idPrefix}' ,'{$generateUrl}', '{$gridBlockJsObject}')", 'generate'
            )
        ));

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_promo_quote_edit_tab_coupons_form_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl() {
        return $this->getUrl('*/*/generate');
    }

}
