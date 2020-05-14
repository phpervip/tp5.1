<?php
$client = new SoapClient('http://tp5.ccc/soap/ts2.wsdl');

print_r($client->__getFunctions());

echo "<br/>";

echo $client->sayHello('Avril Lavigne');

echo "<br/>";

echo $client->add(9,8);
