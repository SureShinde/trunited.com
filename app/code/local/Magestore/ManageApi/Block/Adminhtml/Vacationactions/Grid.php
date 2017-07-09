<?php

class Magestore_ManageApi_Block_Adminhtml_Vacationactions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('vacationactionsGrid');
        $this->setDefaultSort('vacation_actions_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('manageapi/vacationactions')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('vacation_actions_id', array(
            'header' => Mage::helper('manageapi')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'vacation_actions_id',
        ));
        $this->addColumn('date', array(
            'header' => Mage::helper('manageapi')->__('Date'),
            'align' => 'left',
            'index' => 'date',
            'type' => 'date',
        ));

        $this->addColumn('car_driver_fname', array(
            'header' => Mage::helper('manageapi')->__('Car Driver Frist Name'),
            'align' => 'left',
            'index' => 'car_driver_fname',
        ));
        $this->addColumn('car_driver_lname', array(
            'header' => Mage::helper('manageapi')->__('Car Driver Last Name'),
            'align' => 'left',
            'index' => 'car_driver_lname',
        ));
        $this->addColumn('car_pickup_locationid', array(
            'header' => Mage::helper('manageapi')->__('Car Pickup Location ID'),
            'align' => 'left',
            'index' => 'car_pickup_locationid',
        ));
        $this->addColumn('car_dropoff_locationid', array(
            'header' => Mage::helper('manageapi')->__('Car Dropoff Location ID'),
            'align' => 'left',
            'index' => 'car_dropoff_locationid',
        ));
        $this->addColumn('car_pickup_datetime', array(
            'header' => Mage::helper('manageapi')->__('Car Pickup Time'),
            'align' => 'left',
            'index' => 'car_pickup_datetime',
        ));
        $this->addColumn('car_dropoff_datetime', array(
            'header' => Mage::helper('manageapi')->__('Car Dropoff Time'),
            'align' => 'left',
            'index' => 'car_dropoff_datetime',
        ));
        $this->addColumn('car_pickup_location_city', array(
            'header' => Mage::helper('manageapi')->__('Car Pickup Location City'),
            'align' => 'left',
            'index' => 'car_pickup_location_city',
        ));
        $this->addColumn('car_pickup_location_state', array(
            'header' => Mage::helper('manageapi')->__('Car Pickup Location State'),
            'align' => 'left',
            'index' => 'car_pickup_location_state',
        ));
        $this->addColumn('car_pickup_location_country', array(
            'header' => Mage::helper('manageapi')->__('Car Pickup Location Country'),
            'align' => 'left',
            'index' => 'car_pickup_location_country',
        ));
        $this->addColumn('car_dropoff_location_city', array(
            'header' => Mage::helper('manageapi')->__('Car Dropoff Location City'),
            'align' => 'left',
            'index' => 'car_dropoff_location_city',
        ));
        $this->addColumn('car_dropoff_location_state', array(
            'header' => Mage::helper('manageapi')->__('Car Dropoff Location State'),
            'align' => 'left',
            'index' => 'car_dropoff_location_state',
        ));
        $this->addColumn('car_dropoff_location_country', array(
            'header' => Mage::helper('manageapi')->__('Car Dropoff Location Country'),
            'align' => 'left',
            'index' => 'car_dropoff_location_country',
        ));
        $this->addColumn('flights', array(
            'header' => Mage::helper('manageapi')->__('Flights'),
            'align' => 'left',
            'index' => 'flights',
        ));
        $this->addColumn('insurance_fee', array(
            'header' => Mage::helper('manageapi')->__('Insurance Fee'),
            'align' => 'left',
            'index' => 'insurance_fee',
        ));
        $this->addColumn('orig_airport_code', array(
            'header' => Mage::helper('manageapi')->__('Origin Airport Code'),
            'align' => 'left',
            'index' => 'orig_airport_code',
        ));
        $this->addColumn('dest_airport_code', array(
            'header' => Mage::helper('manageapi')->__('Dest Airport Code'),
            'align' => 'left',
            'index' => 'dest_airport_code',
        ));
        $this->addColumn('hotel_city', array(
            'header' => Mage::helper('manageapi')->__('Hotel City'),
            'align' => 'left',
            'index' => 'hotel_city',
        ));
        $this->addColumn('hotel_name', array(
            'header' => Mage::helper('manageapi')->__('Hotel Name'),
            'align' => 'left',
            'index' => 'hotel_name',
        ));
        $this->addColumn('hotel_state', array(
            'header' => Mage::helper('manageapi')->__('Hotel State'),
            'align' => 'left',
            'index' => 'hotel_state',
        ));
        $this->addColumn('hotel_country', array(
            'header' => Mage::helper('manageapi')->__('Hotel Country'),
            'align' => 'left',
            'index' => 'hotel_country',
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
        $this->addColumn('portal', array(
            'header' => Mage::helper('manageapi')->__('Portal'),
            'align' => 'left',
            'index' => 'portal',
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
        $this->addColumn('process_fee', array(
            'header' => Mage::helper('manageapi')->__('Process Fee'),
            'align' => 'left',
            'index' => 'process_fee',
        ));
        $this->addColumn('commission', array(
            'header' => Mage::helper('manageapi')->__('Commission'),
            'align' => 'left',
            'index' => 'commission',
        ));
        $this->addColumn('commission_fee', array(
            'header' => Mage::helper('manageapi')->__('Commission Fee'),
            'align' => 'left',
            'index' => 'commission_fee',
        ));
        $this->addColumn('currency', array(
            'header' => Mage::helper('manageapi')->__('Currency'),
            'align' => 'left',
            'index' => 'currency',
        ));
        $this->addColumn('accounting_currency', array(
            'header' => Mage::helper('manageapi')->__('Accounting Currency'),
            'align' => 'left',
            'index' => 'accounting_currency',
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
        $this->addColumn('tripid', array(
            'header' => Mage::helper('manageapi')->__('Trip ID'),
            'align' => 'left',
            'index' => 'tripid',
        ));
        $this->addColumn('depart_date_time', array(
            'header' => Mage::helper('manageapi')->__('Depart Date Time'),
            'align' => 'left',
            'index' => 'depart_date_time',
        ));
        $this->addColumn('return_date_time', array(
            'header' => Mage::helper('manageapi')->__('Return Date Time'),
            'align' => 'left',
            'index' => 'return_date_time',
        ));
        $this->addColumn('check_in_date_time', array(
            'header' => Mage::helper('manageapi')->__('Checkin Date Time'),
            'align' => 'left',
            'index' => 'check_in_date_time',
            'type' => 'date',
        ));
        $this->addColumn('check_out_date_time', array(
            'header' => Mage::helper('manageapi')->__('Checkout Date Time'),
            'align' => 'left',
            'index' => 'check_out_date_time',
            'type' => 'date',
        ));
        $this->addColumn('air_city_id', array(
            'header' => Mage::helper('manageapi')->__('Air City ID'),
            'align' => 'left',
            'index' => 'air_city_id',
        ));
        $this->addColumn('car', array(
            'header' => Mage::helper('manageapi')->__('Car'),
            'align' => 'left',
            'index' => 'car',
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
        $this->addColumn('member_id', array(
            'header' => Mage::helper('manageapi')->__('Member ID'),
            'align' => 'left',
            'index' => 'member_id',
        ));
        $this->addColumn('rooms', array(
            'header' => Mage::helper('manageapi')->__('Rooms'),
            'align' => 'left',
            'index' => 'rooms',
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
        $this->setMassactionIdField('vacationactions_id');
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