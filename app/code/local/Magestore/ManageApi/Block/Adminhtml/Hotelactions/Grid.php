<?php

class Magestore_ManageApi_Block_Adminhtml_Hotelactions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('hotelactionsGrid');
        $this->setDefaultSort('hotel_actions_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('manageapi/hotelactions')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('hotel_actions_id', array(
            'header' => Mage::helper('manageapi')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'hotel_actions_id',
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
        $this->addColumn('reservation_date_time', array(
            'header' => Mage::helper('manageapi')->__('Reservation Date Time'),
            'align' => 'left',
            'index' => 'reservation_date_time',
            'type' => 'date',
        ));
        $this->addColumn('refid', array(
            'header' => Mage::helper('manageapi')->__('Refid'),
            'align' => 'left',
            'index' => 'refid',
        ));
        $this->addColumn('site_name', array(
            'header' => Mage::helper('manageapi')->__('Site Name'),
            'align' => 'left',
            'index' => 'site_name',
        ));
        $this->addColumn('member_id', array(
            'header' => Mage::helper('manageapi')->__('Member ID'),
            'align' => 'left',
            'index' => 'member_id',
        ));
        $this->addColumn('accountid', array(
            'header' => Mage::helper('manageapi')->__('Account ID'),
            'align' => 'left',
            'index' => 'accountid',
        ));
        $this->addColumn('refclickid', array(
            'header' => Mage::helper('manageapi')->__('Refclick ID'),
            'align' => 'left',
            'index' => 'refclickid',
        ));
        $this->addColumn('hotelid', array(
            'header' => Mage::helper('manageapi')->__('Hotel ID'),
            'align' => 'left',
            'index' => 'hotelid',
        ));
        $this->addColumn('cityid', array(
            'header' => Mage::helper('manageapi')->__('City ID'),
            'align' => 'left',
            'index' => 'cityid',
        ));
        $this->addColumn('tripid', array(
            'header' => Mage::helper('manageapi')->__('Trip ID'),
            'align' => 'left',
            'index' => 'tripid',
        ));
        $this->addColumn('ratecat', array(
            'header' => Mage::helper('manageapi')->__('Rate Cat'),
            'align' => 'left',
            'index' => 'ratecat',
        ));
        $this->addColumn('portal', array(
            'header' => Mage::helper('manageapi')->__('Portal'),
            'align' => 'left',
            'index' => 'portal',
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
        $this->addColumn('commission', array(
            'header' => Mage::helper('manageapi')->__('Commission'),
            'align' => 'left',
            'index' => 'commission',
        ));
        $this->addColumn('fee', array(
            'header' => Mage::helper('manageapi')->__('Fee'),
            'align' => 'left',
            'index' => 'fee',
        ));
        $this->addColumn('revenue', array(
            'header' => Mage::helper('manageapi')->__('Revenue'),
            'align' => 'left',
            'index' => 'revenue',
        ));
        $this->addColumn('booked_currency', array(
            'header' => Mage::helper('manageapi')->__('Booked Currency'),
            'align' => 'left',
            'index' => 'booked_currency',
        ));
        $this->addColumn('rooms', array(
            'header' => Mage::helper('manageapi')->__('Rooms'),
            'align' => 'left',
            'index' => 'rooms',
        ));
        $this->addColumn('city', array(
            'header' => Mage::helper('manageapi')->__('City'),
            'align' => 'left',
            'index' => 'city',
        ));
        $this->addColumn('hotel_name', array(
            'header' => Mage::helper('manageapi')->__('Hotel Name'),
            'align' => 'left',
            'index' => 'hotel_name',
        ));
        $this->addColumn('state', array(
            'header' => Mage::helper('manageapi')->__('State'),
            'align' => 'left',
            'index' => 'state',
        ));
        $this->addColumn('country', array(
            'header' => Mage::helper('manageapi')->__('Country'),
            'align' => 'left',
            'index' => 'country',
        ));
        $this->addColumn('number_of_days', array(
            'header' => Mage::helper('manageapi')->__('Number of Days'),
            'align' => 'left',
            'index' => 'number_of_days',
        ));
        $this->addColumn('promo', array(
            'header' => Mage::helper('manageapi')->__('Promo'),
            'align' => 'left',
            'index' => 'promo',
        ));
        $this->addColumn('check_in_date_time', array(
            'header' => Mage::helper('manageapi')->__('Check In Date Time'),
            'align' => 'left',
            'index' => 'check_in_date_time',
            'type' => 'date'
        ));
        $this->addColumn('check_out_date_time', array(
            'header' => Mage::helper('manageapi')->__('Check Out Date Time'),
            'align' => 'left',
            'index' => 'check_out_date_time',
            'type' => 'date'
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
        $this->addColumn('phn_sale', array(
            'header' => Mage::helper('manageapi')->__('Phn Sale'),
            'align' => 'left',
            'index' => 'phn_sale',
        ));
        $this->addColumn('mobile_sale', array(
            'header' => Mage::helper('manageapi')->__('Mobile Sale'),
            'align' => 'left',
            'index' => 'mobile_sale',
        ));
        $this->addColumn('device', array(
            'header' => Mage::helper('manageapi')->__('Device'),
            'align' => 'left',
            'index' => 'device',
        ));
        $this->addColumn('rate_type', array(
            'header' => Mage::helper('manageapi')->__('Rate Type'),
            'align' => 'left',
            'index' => 'rate_type',
        ));
        $this->addColumn('chain_code', array(
            'header' => Mage::helper('manageapi')->__('Chain Code'),
            'align' => 'left',
            'index' => 'chain_code',
        ));
        $this->addColumn('insurance_flag', array(
            'header' => Mage::helper('manageapi')->__('Insurance Flag'),
            'align' => 'left',
            'index' => 'insurance_flag',
        ));
        $this->addColumn('insured_days', array(
            'header' => Mage::helper('manageapi')->__('Insured Days'),
            'align' => 'left',
            'index' => 'insured_days',
        ));
        $this->addColumn('insurance_commission', array(
            'header' => Mage::helper('manageapi')->__('Insurance Commission'),
            'align' => 'left',
            'index' => 'insurance_commission',
        ));
        $this->addColumn('est_insurance_subtotal', array(
            'header' => Mage::helper('manageapi')->__('Est Insurance Subtotal'),
            'align' => 'left',
            'index' => 'est_insurance_subtotal',
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
            'header' => Mage::helper('manageapi')->__('State'),
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
        $this->setMassactionIdField('hotelactions_id');
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