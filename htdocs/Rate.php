<?php

// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 8.0.0

require_once('../library/fedex-common.php5');

$newline = "<br />";
//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "../wsdl/RateService_v8.wsdl";

//Set commonly used variables in fedex-common.php5.  Set check to true.
if(setDefaults('check'))
{
	$key=setDefaults('key');
	$password=setDefaults('password');
	$shipAccount=setDefaults('shipaccount');
	$meter=setDefaults('meter');
	$billAccount=setDefaults('billaccount');
	$dutyAccount=setDefaults('dutyaccount');
}
//Set commonly used variables below.  Set check to false.
else
{
	$key='XXX';
	$password='YYY';
	$shipAccount='XXX';
	$meter='XXX';
	$billAccount='XXX';
	$dutyAccount='XXX';
}

ini_set("soap.wsdl_cache_enabled", "0");
 
$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

$request['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => $key, 'Password' => $password)); 
$request['ClientDetail'] = array('AccountNumber' => $shipAccount, 'MeterNumber' => $meter);
$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v8 using PHP ***');
$request['Version'] = array('ServiceId' => 'crs', 'Major' => '8', 'Intermediate' => '0', 'Minor' => '0');
$request['ReturnTransitAndCommit'] = true;
$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
$request['RequestedShipment']['ShipTimestamp'] = date('c');
$request['RequestedShipment']['ServiceType'] = 'PRIORITY_OVERNIGHT'; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
$request['RequestedShipment']['Shipper'] = array('Address' => array(
                                          'StreetLines' => array('10 Fed Ex Pkwy'), // Origin details
                                          'City' => 'Memphis',
                                          'StateOrProvinceCode' => 'TN',
                                          'PostalCode' => '38115',
                                          'CountryCode' => 'US'));
$request['RequestedShipment']['Recipient'] = array('Address' => array (
                                               'StreetLines' => array('13450 Farmcrest Ct'), // Destination details
                                               'City' => 'Herndon',
                                               'StateOrProvinceCode' => 'VA',
                                               'PostalCode' => '20171',
                                               'CountryCode' => 'US'));
$request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                        'Payor' => array('AccountNumber' => $billAccount,
                                                                     'CountryCode' => 'US'));
$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
$request['RequestedShipment']['PackageCount'] = '2';
$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY
$request['RequestedShipment']['RequestedPackageLineItems'] = array('0' => array('Weight' => array('Value' => 2.0,
                                                                                    'Units' => 'LB'),
                                                                                    'Dimensions' => array('Length' => 10,
                                                                                        'Width' => 10,
                                                                                        'Height' => 3,
                                                                                        'Units' => 'IN')),
                                                                   '1' => array('Weight' => array('Value' => 5.0,
                                                                                    'Units' => 'LB'),
                                                                                    'Dimensions' => array('Length' => 20,
                                                                                        'Width' => 20,
                                                                                        'Height' => 10,
                                                                                        'Units' => 'IN')));
try 
{
    $response = $client ->getRates($request);
        
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
    {
        printRequestResponse($client);
    }
    else
    {
        printError($response);
    } 
    
    writeToLog($client);    // Write to log file   

} catch (SoapFault $exception) {
   printFault($exception, $client);        
}

?>