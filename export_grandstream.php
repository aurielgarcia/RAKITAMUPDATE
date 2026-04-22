<?php
require_once 'db_connect.php';

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=grandstream.xml");

$sql = "SELECT firstname, lastname, phonenumber FROM dbo.grandstream_entry";
$stmt = sqlsrv_query($conn, $sql);

// Build XML using SimpleXML first
$xml = new SimpleXMLElement('<AddressBook/>');

// Groups
$group1 = $xml->addChild('pbgroup');
$group1->addChild('id', 1);
$group1->addChild('name', 'Default');
$group1->addChild('ringtones', 'default ringtone');

$group2 = $xml->addChild('pbgroup');
$group2->addChild('id', 2);
$group2->addChild('name', 'Blacklist');
$group2->addChild('ringtones', 'default ringtone');

// Contacts
$id = 1;

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    $contact = $xml->addChild('Contact');
    $contact->addChild('id', $id++);
    $contact->addChild('FirstName', $row['firstname']);
    $contact->addChild('LastName', $row['lastname']);
    $contact->addChild('RingtoneUrl', 'default ringtone');
    $contact->addChild('Frequent', 0);

    $phone = $contact->addChild('Phone');
    $phone->addAttribute('type', 'Work');
    $phone->addChild('phonenumber', $row['phonenumber']);
    $phone->addChild('accountindex', 1);

    $contact->addChild('Primary', 0);
    $contact->addChild('Mail');
    $contact->addChild('Department');
}

// 🔥 Convert to DOM for formatting
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;

// Load SimpleXML into DOM
$dom->loadXML($xml->asXML());

// Output formatted XML
echo $dom->saveXML();
?>