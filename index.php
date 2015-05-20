<?php
/**
 * An example index.php file.
 *
 * Please enter your SOAP endpoint, API login, and password.
 */
include 'dmc.php';

$soap_url = 'https://sslc.teradatadmc.com/[your instance]/api/soap/v2/?wsdl';
$soap_settings = [
    'login' => '',
    'password' => '',
];
$fault_trace = false;
$benchmark = true;

$dmc = new DMC( $soap_url, $soap_settings, $fault_trace, $benchmark );

$dmc->dmcinfo();
