<?php
require_once 'db_connect.php';

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=grandstream.xml");

// Query only what you need
$sql = "SELECT firstname, lastname, phonenumber FROM dbo.grandstream_entry";
$stmt = sqlsrv_query($conn, $sql);

// Create XML root
$xml = new SimpleXMLElement('<AddressBook/>');

// ✅ Add version
$xml->addChild('version', '1');

// Loop data
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    $contact = $xml->addChild('Contact');

    $contact->addChild('FirstName', htmlspecialchars($row['firstname']));
    $contact->addChild('LastName', htmlspecialchars($row['lastname']));
    $contact->addChild('IsPrimary', 'false');
    $contact->addChild('Primary', '0');
    $contact->addChild('Frequent', '0');

    // Empty tag like <PhotoUrl/>
    $contact->addChild('PhotoUrl');

    // Phone block
    $phone = $contact->addChild('Phone');
    $phone->addAttribute('type', 'Work');
    $phone->addChild('phonenumber', $row['phonenumber']);
    $phone->addChild('accountindex', '1');
}

// ✅ Format output properly (important!)
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());

// ✅ Add standalone="no"
$dom->xmlStandalone = false;

echo $dom->saveXML();
?>