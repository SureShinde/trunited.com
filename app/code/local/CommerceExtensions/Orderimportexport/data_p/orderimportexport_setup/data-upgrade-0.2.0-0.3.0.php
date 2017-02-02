<?php

$installer = $this;
$installer->startSetup();

$dataflowData = array(
    array(
        'name'         => 'Import Orders (CSV)',
        'actions_xml'  => '<action type="dataflow/convert_parser_csv" method="parse">'."\r\n".'    <var name="delimiter"><![CDATA[,]]></var>'."\r\n".'    <var name="enclose"><![CDATA["]]></var>'."\r\n".'    <var name="fieldnames">true</var>'."\r\n".'    <var name="store"><![CDATA[0]]></var>'."\r\n".'    <var name="number_of_records">1</var>'."\r\n".'    <var name="decimal_separator"><![CDATA[.]]></var>'."\r\n".'    <var name="update_orders"><![CDATA[false]]></var>'."\r\n".'    <var name="create_invoice"><![CDATA[false]]></var>'."\r\n".'    <var name="create_shipment"><![CDATA[false]]></var>'."\r\n".'    <var name="adapter">commerceextensions_orderimportexport/convert_adapter_importorders</var>'."\r\n".'    <var name="method">parse</var>'."\r\n".'</action>',
        'gui_data'     => 'a:5:{s:6:"export";a:1:{s:13:"add_url_field";s:1:"0";}s:6:"import";a:2:{s:17:"number_of_records";s:1:"1";s:17:"decimal_separator";s:1:".";}s:5:"parse";a:7:{s:23:"update_customer_address";s:5:"false";s:14:"create_invoice";s:5:"false";s:15:"create_shipment";s:5:"false";s:4:"type";s:3:"csv";s:12:"single_sheet";s:0:"";s:9:"delimiter";s:1:",";s:7:"enclose";s:1:""";}s:7:"unparse";a:4:{s:9:"date_from";s:0:"";s:7:"date_to";s:0:"";s:11:"recordlimit";s:1:"0";s:22:"filter_by_order_status";s:0:"";}s:4:"file";a:8:{s:4:"type";s:4:"file";s:8:"filename";s:18:"import_orders.csv";s:4:"path";s:10:"var/export";s:4:"host";s:0:"";s:4:"user";s:0:"";s:8:"password";s:0:"";s:9:"file_mode";s:1:"2";s:7:"passive";s:0:"";}}',
        'direction'    => 'import',
        'entity_type'  => 'order',
        'store_id'     => 0,
        'data_transfer'=> 'interactive',
        'is_intersec' => 1
    ),
    array(
        'name'         => 'Import Orders (XML)',
        'actions_xml'  => '<action type="dataflow/convert_parser_xml_excel" method="parse">'."\r\n".'    <var name="single_sheet"><![CDATA[]]></var>'."\r\n".'    <var name="fieldnames">true</var>'."\r\n".'    <var name="store"><![CDATA[0]]></var>'."\r\n".'    <var name="number_of_records">1</var>'."\r\n".'    <var name="decimal_separator"><![CDATA[.]]></var>'."\r\n".'    <var name="update_orders"><![CDATA[false]]></var>'."\r\n".'    <var name="create_invoice"><![CDATA[false]]></var>'."\r\n".'    <var name="create_shipment"><![CDATA[false]]></var>'."\r\n".'    <var name="adapter">commerceextensions_orderimportexport/convert_adapter_importorders</var>'."\r\n".'    <var name="method">parse</var>'."\r\n".'</action>',
        'gui_data'     => 'a:5:{s:6:"export";a:1:{s:13:"add_url_field";s:1:"0";}s:6:"import";a:2:{s:17:"number_of_records";s:1:"1";s:17:"decimal_separator";s:1:".";}s:5:"parse";a:7:{s:23:"update_customer_address";s:5:"false";s:14:"create_invoice";s:5:"false";s:15:"create_shipment";s:5:"false";s:4:"type";s:9:"excel_xml";s:12:"single_sheet";s:0:"";s:9:"delimiter";s:1:",";s:7:"enclose";s:1:""";}s:7:"unparse";a:4:{s:9:"date_from";s:0:"";s:7:"date_to";s:0:"";s:11:"recordlimit";s:1:"0";s:22:"filter_by_order_status";s:0:"";}s:4:"file";a:8:{s:4:"type";s:4:"file";s:8:"filename";s:18:"import_orders.xml";s:4:"path";s:10:"var/export";s:4:"host";s:0:"";s:4:"user";s:0:"";s:8:"password";s:0:"";s:9:"file_mode";s:1:"2";s:7:"passive";s:0:"";}}',
        'direction'    => 'import',
        'entity_type'  => 'order',
        'store_id'     => 0,
        'data_transfer'=> 'interactive',
        'is_intersec' => 1
    ),
    array(
        'name'         => 'Export Orders (CSV)',
        'actions_xml'  => '<action type="commerceextensions_orderimportexport/convert_adapter_exportorders" method="load">'."\r\n".'    <var name="store"><![CDATA[0]]></var>'."\r\n".'    <var name="filter/adressType"><![CDATA[default_billing]]></var>'."\r\n".'</action>'."\r\n\r\n".'<action type="commerceextensions_orderimportexport/convert_parser_exportorders" method="unparse">'."\r\n".'    <var name="store"><![CDATA[0]]></var>'."\r\n".'    <var name="recordlimit"><![CDATA[9999999999999]]></var>'."\r\n".'    <var name="export_product_pricing"><![CDATA[false]]></var>'."\r\n".'</action>'."\r\n\r\n".'<action type="dataflow/convert_mapper_column" method="map">'."\r\n".'</action>'."\r\n\r\n".'<action type="dataflow/convert_parser_csv" method="unparse">'."\r\n".'    <var name="delimiter"><![CDATA[,]]></var>'."\r\n".'    <var name="enclose"><![CDATA["]]></var>'."\r\n".'    <var name="fieldnames">true</var>'."\r\n".'</action>'."\r\n\r\n".'<action type="dataflow/convert_adapter_io" method="save">'."\r\n".'    <var name="type">file</var>'."\r\n".'    <var name="path">var/export</var>'."\r\n".'    <var name="filename"><![CDATA[export_orders.csv]]></var>'."\r\n".'</action>',
        'gui_data'     => 'a:5:{s:6:"export";a:1:{s:13:"add_url_field";s:1:"0";}s:6:"import";a:2:{s:17:"number_of_records";s:1:"1";s:17:"decimal_separator";s:1:".";}s:5:"parse";a:7:{s:23:"update_customer_address";s:5:"false";s:14:"create_invoice";s:5:"false";s:15:"create_shipment";s:5:"false";s:4:"type";s:3:"csv";s:12:"single_sheet";s:0:"";s:9:"delimiter";s:1:",";s:7:"enclose";s:1:""";}s:7:"unparse";a:4:{s:9:"date_from";s:0:"";s:7:"date_to";s:0:"";s:11:"recordlimit";s:13:"9999999999999";s:22:"filter_by_order_status";s:0:"";}s:4:"file";a:8:{s:4:"type";s:4:"file";s:8:"filename";s:17:"export_orders.csv";s:4:"path";s:10:"var/export";s:4:"host";s:0:"";s:4:"user";s:0:"";s:8:"password";s:0:"";s:9:"file_mode";s:1:"2";s:7:"passive";s:0:"";}}',
        'direction'    => 'export',
        'entity_type'  => 'order',
        'store_id'     => 0,
        'data_transfer'=> 'file',
        'is_intersec' => 1
    ),
    array(
        'name'         => 'Export Orders (XML)',
        'actions_xml'  => '<action type="commerceextensions_orderimportexport/convert_adapter_exportorders" method="load">'."\r\n".'    <var name="store"><![CDATA[0]]></var>'."\r\n".'<var name="filter/adressType"><![CDATA[default_billing]]></var>'."\r\n".'</action>'."\r\n\r\n".'<action type="commerceextensions_orderimportexport/convert_parser_exportorders" method="unparse">'."\r\n".'    <var name="store"><![CDATA[0]]></var>'."\r\n".'    <var name="recordlimit"><![CDATA[9999999999999]]></var>'."\r\n".'    <var name="export_product_pricing"><![CDATA[false]]></var>'."\r\n".'</action>'."\r\n\r\n".'<action type="dataflow/convert_mapper_column" method="map">'."\r\n".'</action>'."\r\n\r\n".'<action type="dataflow/convert_parser_xml_excel" method="unparse">'."\r\n".'    <var name="single_sheet"><![CDATA[]]></var>'."\r\n".'    <var name="fieldnames">true</var>'."\r\n".'</action>'."\r\n\r\n".'<action type="dataflow/convert_adapter_io" method="save">'."\r\n".'    <var name="type">file</var>'."\r\n".'    <var name="path">var/export</var>'."\r\n".'    <var name="filename"><![CDATA[export_orders.xml]]></var>'."\r\n".'</action>',
        'gui_data'     => 'a:5:{s:6:"export";a:1:{s:13:"add_url_field";s:1:"0";}s:6:"import";a:2:{s:17:"number_of_records";s:1:"1";s:17:"decimal_separator";s:1:".";}s:5:"parse";a:7:{s:23:"update_customer_address";s:5:"false";s:14:"create_invoice";s:5:"false";s:15:"create_shipment";s:5:"false";s:4:"type";s:9:"excel_xml";s:12:"single_sheet";s:0:"";s:9:"delimiter";s:1:",";s:7:"enclose";s:1:""";}s:7:"unparse";a:4:{s:9:"date_from";s:0:"";s:7:"date_to";s:0:"";s:11:"recordlimit";s:13:"9999999999999";s:22:"filter_by_order_status";s:0:"";}s:4:"file";a:8:{s:4:"type";s:4:"file";s:8:"filename";s:17:"export_orders.xml";s:4:"path";s:10:"var/export";s:4:"host";s:0:"";s:4:"user";s:0:"";s:8:"password";s:0:"";s:9:"file_mode";s:1:"2";s:7:"passive";s:0:"";}}',
        'direction'    => 'export',
        'entity_type'  => 'order',
        'store_id'     => 0,
        'data_transfer'=> 'file',
        'is_intersec' => 1
    )
);

foreach ($dataflowData as $profileData)
{
    Mage::getModel('commerceextensions_orderimportexport/profile')->setData($profileData)->save();
}

$installer->endSetup();