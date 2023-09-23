<?php
/**
 * This example shows sending a message using a local sendmail binary.
 */
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';

require_once("../../../wp-load.php");
//Create a new PHPMailer instance
$mail = new PHPMailer;
// Set PHPMailer to use the sendmail transport
$mail->isSendmail();
//Set who the message is to be sent from
$mail->setFrom('support@regulusproject.com', 'First Last');
//Set an alternative reply-to address
$mail->addReplyTo('support@regulusproject.com', 'First Last');
//Set who the message is to be sent to
$mail->addAddress('coolrmails@gmail.com', 'Pallab Sardar');
//Set the subject line
$mail->Subject = 'PHPMailer sendmail test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
//Replace the plain text body with one created manually
$mail->AltBody = 'altbody';
//Attach an image file
//$mail->addAttachment('image.jpg');
$url = 'http://bcloa.gr8tforms.com/wp-content/plugins/bclog-es/formpdf.php?fid=8';
$mail->addStringAttachment(file_get_contents($url), 'REEF.pdf');
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?>