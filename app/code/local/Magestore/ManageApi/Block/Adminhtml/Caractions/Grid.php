<?php

class Magestore_ManageApi_Block_Adminhtml_Caractions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('caractionsGrid');
        $this->setDefaultSort('car_actions_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('manageapi/caractions')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('car_actions_id', array(
            'header' => Mage::helper('manageapi')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'car_actions_id',
        ));

        $this->addColumn('driver_fname', array(
            'header' => Mage::helper('manageapi')->__('Driver First Name'),
            'align' => 'left',
            'index' => 'driver_fname',
        ));
        $this->addColumn('driver_lname', array(
            'header' => Mage::helper('manageapi')->__('Driver Last Name'),
            'align' => 'left',
            'index' => 'driver_lname',
        ));
        $this->addColumn('pickup_locationid', array(
            'header' => Mage::helper('manageapi')->__('Pickup Location ID'),
            'align' => 'left',
            'index' => 'pickup_locationid',
        ));
        $this->addColumn('dropoff_locationid', array(
            'header' => Mage::helper('manageapi')->__('Dropoff Location ID'),
            'align' => 'left',
            'index' => 'dropoff_locationid',
        ));
        $this->addColumn('pickup_time', array(
            'header' => Mage::helper('manageapi')->__('Pickup Time'),
            'align' => 'left',
            'index' => 'pickup_time',
            'type' => 'date',
        ));
        $this->addColumn('dropoff_time', array(
            'header' => Mage::helper('manageapi')->__('Dropoff Time'),
            'align' => 'left',
            'index' => 'dropoff_time',
            'type' => 'date',
        ));
        $this->addColumn('pickup_location_city', array(
            'header' => Mage::helper('manageapi')->__('Pickup Location City'),
            'align' => 'left',
            'index' => 'pickup_location_city',
        ));
        $this->addColumn('pickup_location_state', array(
            'header' => Mage::helper('manageapi')->__('Pickup Location State'),
            'align' => 'left',
            'index' => 'pickup_location_state',
        ));
        $this->addColumn('pickup_location_country', array(
            'header' => Mage::helper('manageapi')->__('Pickup Location Country'),
            'align' => 'left',
            'index' => 'pickup_location_country',
        ));
        $this->addColumn('dropoff_location_city', array(
            'header' => Mage::helper('manageapi')->__('Dropoff Location City'),
            'align' => 'left',
            'index' => 'dropoff_location_city',
        ));
        $this->addColumn('dropoff_location_state', array(
            'header' => Mage::helper('manageapi')->__('Dropoff Location State'),
            'align' => 'left',
            'index' => 'dropoff_location_state',
        ));
        $this->addColumn('dropoff_location_country', array(
            'header' => Mage::helper('manageapi')->__('Dropoff Location Country'),
            'align' => 'left',
            'index' => 'dropoff_location_country',
        ));
        $this->addColumn('affiliate_cut', array(
            'header' => Mage::helper('manageapi')->__('Affiliate Cut'),
            'align' => 'left',
            'index' => 'affiliate_cut',
        ));
        $this->addColumn('date', array(
            'header' => Mage::helper('manageapi')->__('Date'),
            'align' => 'left',
            'index' => 'date',
            'type' => 'date',
        ));
        $this->addColumn('revenue', array(
            'header' => Mage::helper('manageapi')->__('Revenue'),
            'align' => 'left',
            'index' => 'revenue',
        ));
        $this->addColumn('promo_coupon_code', array(
            'header' => Mage::helper('manageapi')->__('Promo Coupon Code'),
            'align' => 'left',
            'index' => 'promo_coupon_code',
        ));
        $this->addColumn('confirmed_subtotal', array(
            'header' => Mage::helper('manageapi')->__('Confirmed Subtotal'),
            'align' => 'left',
            'index' => 'confirmed_subtotal',
        ));

        $this->addColumn('confirmed_commission', array(
            'header' => Mage::helper('manageapi')->__('Confirmed Commission'),
            'align' => 'left',
            'index' => 'confirmed_commission',
        ));

        $this->addColumn('confirmed_fee', array(
            'header' => Mage::helper('manageapi')->__('Confirmed Fee'),
            'align' => 'left',
            'index' => 'confirmed_fee',
        ));

        $this->addColumn('confirmed_total_earnings', array(
            'header' => Mage::helper('manageapi')->__('Confirmed Total Earning'),
            'align' => 'left',
            'index' => 'confirmed_total_earnings',
        ));

        $this->addColumn('reconciled_status', array(
            'header' => Mage::helper('manageapi')->__('Reconciled Status'),
            'align' => 'left',
            'index' => 'reconciled_status',
        ));

        $this->addColumn('invoice_number', array(
            'header' => Mage::helper('manageapi')->__('Invoice Number'),
            'align' => 'left',
            'index' => 'invoice_number',
        ));
        $this->addColumn('confirmed_insurance_commission', array(
            'header' => Mage::helper('manageapi')->__('Confirmed Insurance Commission'),
            'align' => 'left',
            'index' => 'confirmed_insurance_commission',
        ));
        $this->addColumn('insurance_reconciled_status', array(
            'header' => Mage::helper('manageapi')->__('Insurance Reconciled Status'),
            'align' => 'left',
            'index' => 'insurance_reconciled_status',
        ));
        $this->addColumn('insurance_invoice_number', array(
            'header' => Mage::helper('manageapi')->__('Insurance Invoice Number'),
            'align' => 'left',
            'index' => 'insurance_invoice_number',
        ));
        $this->addColumn('accountid', array(
            'header' => Mage::helper('manageapi')->__('Account ID'),
            'align' => 'left',
            'index' => 'accountid',
        ));
        $this->addColumn('refid', array(
            'header' => Mage::helper('manageapi')->__('Ref ID'),
            'align' => 'left',
            'index' => 'refid',
        ));
        $this->addColumn('ratecat', array(
            'header' => Mage::helper('manageapi')->__('Rate Cat'),
            'align' => 'left',
            'index' => 'ratecat',
        ));
        $this->addColumn('site_name', array(
            'header' => Mage::helper('manageapi')->__('Site Name'),
            'align' => 'left',
            'index' => 'site_name',
        ));
        $this->addColumn('portal', array(
            'header' => Mage::helper('manageapi')->__('Portal'),
            'align' => 'left',
            'index' => 'portal',
        ));
        $this->addColumn('refclickid', array(
            'header' => Mage::helper('manageapi')->__('Refclick ID'),
            'align' => 'left',
            'index' => 'refclickid',
        ));
        $this->addColumn('requestid', array(
            'header' => Mage::helper('manageapi')->__('Request ID'),
            'align' => 'left',
            'index' => 'requestid',
        ));
        $this->addColumn('insurance_flag', array(
            'header' => Mage::helper('manageapi')->__('Insurance Flag'),
            'align' => 'left',
            'index' => 'insurance_flag',
        ));
        $this->addColumn('total', array(
            'header' => Mage::helper('manageapi')->__('Total'),
            'align' => 'left',
            'index' => 'total',
        ));
        $this->addColumn('sub_total', array(
            'header' => Mage::helper('manageapi')->__('Subtotal'),
            'align' => 'left',
            'index' => 'sub_total',
        ));
        $this->addColumn('tax', array(
            'header' => Mage::helper('manageapi')->__('Subtotal'),
            'align' => 'left',
            'index' => 'tax',
        ));
        $this->addColumn('insured_days', array(
            'header' => Mage::helper('manageapi')->__('Insured Days'),
            'align' => 'left',
            'index' => 'insured_days',
        ));
        $this->addColumn('currency', array(
            'header' => Mage::helper('manageapi')->__('Currency'),
            'align' => 'left',
            'index' => 'currency',
        ));
        $this->addColumn('user_name', array(
            'header' => Mage::helper('manageapi')->__('User name'),
            'align' => 'left',
            'index' => 'user_name',
        ));
        $this->addColumn('user_middlename', array(
            'header' => Mage::helper('manageapi')->__('User Middlename'),
            'align' => 'left',
            'index' => 'user_middlename',
        ));
        $this->addColumn('user_lastname', array(
            'header' => Mage::helper('manageapi')->__('User Lastname'),
            'align' => 'left',
            'index' => 'user_lastname',
        ));
        $this->addColumn('user_email', array(
            'header' => Mage::helper('manageapi')->__('User email'),
            'align' => 'left',
            'index' => 'user_email',
        ));
        $this->addColumn('user_phone', array(
            'header' => Mage::helper('manageapi')->__('User phone'),
            'align' => 'left',
            'index' => 'user_phone',
        ));
        $this->addColumn('user_phone_extension', array(
            'header' => Mage::helper('manageapi')->__('User Phone Extension'),
            'align' => 'left',
            'index' => 'user_phone_extension',
        ));
        $this->addColumn('user_address', array(
            'header' => Mage::helper('manageapi')->__('User Address'),
            'align' => 'left',
            'index' => 'user_address',
        ));
        $this->addColumn('user_location_city', array(
            'header' => Mage::helper('manageapi')->__('User Location City'),
            'align' => 'left',
            'index' => 'user_location_city',
        ));
        $this->addColumn('user_location_state', array(
            'header' => Mage::helper('manageapi')->__('User Location State'),
            'align' => 'left',
            'index' => 'user_location_state',
        ));
        $this->addColumn('user_country', array(
            'header' => Mage::helper('manageapi')->__('User Country'),
            'align' => 'left',
            'index' => 'user_country',
        ));
        $this->addColumn('user_zip', array(
            'header' => Mage::helper('manageapi')->__('User Zip'),
            'align' => 'left',
            'index' => 'user_zip',
        ));
        $this->addColumn('car_type', array(
            'header' => Mage::helper('manageapi')->__('Car Type'),
            'align' => 'left',
            'index' => 'car_type',
        ));
        $this->addColumn('company_name', array(
            'header' => Mage::helper('manageapi')->__('Company Name'),
            'align' => 'left',
            'index' => 'company_name',
        ));
        $this->addColumn('company_code', array(
            'header' => Mage::helper('manageapi')->__('Company Code'),
            'align' => 'left',
            'index' => 'company_code',
        ));
        $this->addColumn('num_days', array(
            'header' => Mage::helper('manageapi')->__('Num Days'),
            'align' => 'left',
            'index' => 'num_days',
        ));
        $this->addColumn('pickup_location', array(
            'header' => Mage::helper('manageapi')->__('Pickup Location'),
            'align' => 'left',
            'index' => 'pickup_location',
        ));
        $this->addColumn('dropoff_location', array(
            'header' => Mage::helper('manageapi')->__('Dropoff Location'),
            'align' => 'left',
            'index' => 'dropoff_location',
        ));
        $this->addColumn('bookingid', array(
            'header' => Mage::helper('manageapi')->__('Booking ID'),
            'align' => 'left',
            'index' => 'bookingid',
        ));
        $this->addColumn('tripid', array(
            'header' => Mage::helper('manageapi')->__('Trip ID'),
            'align' => 'left',
            'index' => 'tripid',
        ));
        $this->addColumn('newsletter_optin', array(
            'header' => Mage::helper('manageapi')->__('Newsletter'),
            'align' => 'left',
            'index' => 'newsletter_optin',
        ));
        $this->addColumn('device', array(
            'header' => Mage::helper('manageapi')->__('Device'),
            'align' => 'left',
            'index' => 'device',
        ));
        $this->addColumn('ins_subtotal', array(
            'header' => Mage::helper('manageapi')->__('Ins Subtotal'),
            'align' => 'left',
            'index' => 'ins_subtotal',
        ));
        $this->addColumn('accounting_aux_value', array(
            'header' => Mage::helper('manageapi')->__('Accounting Aux Value'),
            'align' => 'left',
            'index' => 'accounting_aux_value',
        ));
        $this->addColumn('fee', array(
            'header' => Mage::helper('manageapi')->__('Fee'),
            'align' => 'left',
            'index' => 'fee',
        ));
        $this->addColumn('commission', array(
            'header' => Mage::helper('manageapi')->__('Commission'),
            'align' => 'left',
            'index' => 'commission',
        ));
        $this->addColumn('rate_type', array(
            'header' => Mage::helper('manageapi')->__('Rate Type'),
            'align' => 'left',
            'index' => 'rate_type',
        ));
        $this->addColumn('phn_sale', array(
            'header' => Mage::helper('manageapi')->__('Phn Sale'),
            'align' => 'left',
            'index' => 'phn_sale',
        ));
        $this->addColumn('member_id', array(
            'header' => Mage::helper('manageapi')->__('Member ID'),
            'align' => 'left',
            'index' => 'member_id',
        ));
        $this->addColumn('invoice_date', array(
            'header' => Mage::helper('manageapi')->__('Invoice Date'),
            'align' => 'left',
            'index' => 'invoice_date',
        ));
        $this->addColumn('pending_commission', array(
            'header' => Mage::helper('manageapi')->__('Pending Commission'),
            'align' => 'left',
            'index' => 'pending_commission',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('manageapi')->__('Status'),
            'align' => 'left',
            'index' => 'status',
        ));
        $this->addColumn('payment_commission', array(
            'header' => Mage::helper('manageapi')->__('Payment Commission'),
            'align' => 'left',
            'index' => 'payment_commission',
        ));

        $this->addColumn('other', array(
            'header' => Mage::helper('manageapi')->__('Other'),
            'align' => 'left',
            'index' => 'other',
        ));

        $this->addColumn('created_time', array(
            'header' => Mage::helper('manageapi')->__('Created At'),
            'align' => 'left',
            'index' => 'created_time',
            'type' => 'date'
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('manageapi')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('manageapi')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('caractions_id');
        $this->getMassactionBlock()->setFormFieldName('manageapi');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('manageapi')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('manageapi')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
    }
}