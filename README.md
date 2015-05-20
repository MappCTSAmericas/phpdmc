## Introduction

Teradata provides programmatic access to our email marketing tool,
Digital Messaging Center via SOAP. All you need is a special API user
within your individual system to do the programing. Once you receive
your user data, you can access all of the functionality that is
available both in the API and your Digital Messaging Center system.

## Basic Use

To use this client to access your instance of Teradata's Digital
Messaging Center, include it in your script then instantiate it by
providing your SOAP endpoint and login credentials.

    <?php
    include '/path/to/dmc_soapclient.php';

    $soap_url = 'https://your.teradatadmcinstance.com/some_path/api/soap/v2?wsdl';
    $soap_settings = array(
     'login' => 'apiuser@yourdomain.com',
     'password' => 'verysecurepassword',
    );

    $dmc = new DMC_SoapClient( $soap_url, $soap_settings);
    ?>

Similarly, if you'd like to use this without providing your credentials
for each instance, you can update the class constructor to reflect your
login information.

    public function __construct( $soap_url = null, $soap_settings = null ) {
         $this->soap_url = 'https://sslc.teradatadmc.com/[some_path]/api/soap/v2?wsdl';
         $this->$soap_settings = array(
             'login' => 'apiuser@yourdomain.com',
             'password' => 'yourverysecurepassword',
             'trace' => true,
         );
    }

## More Information

Please refer to the included documentation for more information
regarding specific class methods and API functions.
