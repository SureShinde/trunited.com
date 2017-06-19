<?php

class Magestore_ManageApi_Block_Adminhtml_Flightactions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('flightactionsGrid');
        $this->setDefaultSort('hotel_actions_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('manageapi/flightactions')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('flight_actions_id', array(
            'header' => Mage::helper('manageapi')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'flight_actions_id',
        ));

        $this->addColumn('air_offer_id', array(
            'header' => Mage::helper('manageapi')->__('Air Offer ID'),
            'align' => 'left',
            'index' => 'air_offer_id',
        ));
        $this->addColumn('reservation_date_time', array(
            'header' => Mage::helper('manageapi')->__('Reservation Date Time'),
            'align' => 'left',
            'index' => 'reservation_date_time',
            'type' => 'date',
        ));
        $this->addColumn('session_id', array(
            'header' => Mage::helper('manageapi')->__('Session ID'),
            'align' => 'left',
            'index' => 'session_id',
        ));
        $this->addColumn('accountid', array(
            'header' => Mage::helper('manageapi')->__('Account ID'),
            'align' => 'left',
            'index' => 'accountid',
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
        $this->addColumn('refclickid', array(
            'header' => Mage::helper('manageapi')->__('Refclick ID'),
            'align' => 'left',
            'index' => 'refclickid',
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
        $this->addColumn('confirmed_insurance_commission', array(
            'header' => Mage::helper('manageapi')->__('Confirmed Insurance Commission'),
            'align' => 'left',
            'index' => 'confirmed_insurance_commission',
        ));
        $this->addColumn('ratecat', array(
            'header' => Mage::helper('manageapi')->__('Rate Cat'),
            'align' => 'left',
            'index' => 'ratecat',
        ));
        $this->addColumn('ratecat', array(
            'header' => Mage::helper('manageapi')->__('Rate Cat'),
            'align' => 'left',
            'index' => 'ratecat',
        ));
        $this->addColumn('passengers', array(
            'header' => Mage::helper('manageapi')->__('Passengers'),
            'align' => 'left',
            'index' => 'passengers',
        ));
        $this->addColumn('passengers', array(
            'header' => Mage::helper('manageapi')->__('Passengers'),
            'align' => 'left',
            'index' => 'passengers',
        ));
        $this->addColumn('insured_passengers', array(
            'header' => Mage::helper('manageapi')->__('Insured Passengers'),
            'align' => 'left',
            'index' => 'insured_passengers',
        ));
        $this->addColumn('air_search_type', array(
            'header' => Mage::helper('manageapi')->__('Air Search Type'),
            'align' => 'left',
            'index' => 'air_search_type',
        ));
        $this->addColumn('start_date_time', array(
            'header' => Mage::helper('manageapi')->__('Start Date Time'),
            'align' => 'left',
            'index' => 'start_date_time',
            'type' => 'date'
        ));
        $this->addColumn('end_date_time', array(
            'header' => Mage::helper('manageapi')->__('End Date Time'),
            'align' => 'left',
            'index' => 'end_date_time',
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
        $this->addColumn('status', array(
            'header' => Mage::helper('manageapi')->__('State'),
            'align' => 'left',
            'index' => 'status',
        ));
        $this->addColumn('revenue', array(
            'header' => Mage::helper('manageapi')->__('Revenue'),
            'align' => 'left',
            'index' => 'revenue',
        ));
        $this->addColumn('flights', array(
            'header' => Mage::helper('manageapi')->__('Flights'),
            'align' => 'left',
            'index' => 'flights',
        ));
        $this->addColumn('total_insurance', array(
            'header' => Mage::helper('manageapi')->__('Total Insurance'),
            'align' => 'left',
            'index' => 'total_insurance',
        ));
        $this->addColumn('ins_subtotal', array(
            'header' => Mage::helper('manageapi')->__('Ins Subtotal'),
            'align' => 'left',
            'index' => 'ins_subtotal',
        ));
        $this->addColumn('affiliate_cut', array(
            'header' => Mage::helper('manageapi')->__('Affiliate Cut'),
            'align' => 'left',
            'index' => 'affiliate_cut',
        ));
        $this->addColumn('accounting_sub_total', array(
            'header' => Mage::helper('manageapi')->__('Affiliate Subtotal'),
            'align' => 'left',
            'index' => 'accounting_sub_total',
        ));
        $this->addColumn('origin_Airport_name', array(
            'header' => Mage::helper('manageapi')->__('Origin Airport Name'),
            'align' => 'left',
            'index' => 'origin_Airport_name',
        ));
        $this->addColumn('dest_Airport_name', array(
            'header' => Mage::helper('manageapi')->__('Dest Airport Name'),
            'align' => 'left',
            'index' => 'dest_Airport_name',
        ));
        $this->addColumn('origin_City', array(
            'header' => Mage::helper('manageapi')->__('Origin City'),
            'align' => 'left',
            'index' => 'origin_City',
        ));
        $this->addColumn('dest_City', array(
            'header' => Mage::helper('manageapi')->__('Dest City'),
            'align' => 'left',
            'index' => 'dest_City',
        ));
        $this->addColumn('device', array(
            'header' => Mage::helper('manageapi')->__('Device'),
            'align' => 'left',
            'index' => 'device',
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
        $this->setMassactionIdField('flightactions_id');
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