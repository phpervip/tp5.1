<?php


$client = new SoapClient(null, array(
    'location'=>'http://tp5.ccc/soap/ts1.php',
    'uri'=>'sampleA'
));

echo $client->sayHi('Taylor,Swift');
echo "<br/>";
echo $client->add(1,2);
