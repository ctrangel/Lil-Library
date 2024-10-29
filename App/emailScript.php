<?php
session_start();
require 'vendor/autoload.php'; //Necessary for composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if(isset($_POST['signUpBtn']))
{
    $to = $_POST['to_email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $attachment = $_FILES['attachment']['tmp_name'];

    $emailAttachment = null;

    if ($attachment)
    {
        $attachmentDir = 'attachment/';
        $emailAttachment = $attachmentDir. $_FILES['attachment']['name'];
        /**
         * Add file type validation
         */
        $fileExtension = pathinfo($emailAttachment,PATHINFO_EXTENSION);
        if(!in_array($fileExtension, array('jpg', 'png', 'jpeg', 'php'))){
            $_SESSION['error_message'] = "Invalid file type: Only jpg, png, jpegs";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        move_uploaded_file($attachment, $emailAttachment);
    }

    newsSignup($to, $subject, $message, $emailAttachment);

    @unlink($emailAttachment);
}


/**
 * Sends email with attachment
 * @param $to
 * @param $subject
 * @param $message
 * @param $emailAttachment
 */


//$to = "johnszoszorekr8@gmail.com"; //Testing Email
//$subject = "Lil-Library NewsLetter";


//$message= file_get_contents("newsletterTemplate.php");

//Sending the Email Message
//$send = mail($to, $subject, $message);

//echo ($send ? "You have been sent a newsletter email" : "Couldn't send email");



function newsSignup($to, $subject, $message, $emailAttachment): void {
    try {
        $mail = new PHPMailer(true);
        $mail->SMPTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'johnszoszorekr8@gmail.com';
        $mail->Password = 'bbnj cjoo fmub wftl'; // <-- which we generated from step2
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('johnszoszorekr8@gmail.com', 'Coding Birds'); //<-- 2nd param is optional
        $mail->addAddress($to, 'Ankit'); //<-- 2nd param is optional
        $mail->isHTML(false); //<-- make it true if sending HTML content as message
        $mail->Subject = $subject;
        $mail->Body = $message;

        if($emailAttachment) {
            $mail->addAttachment($emailAttachment);
        }
        $mail->send();
        $_SESSION['news_sent'] = "NewsLetter Has Been Sent";
    }catch (Exception $e){
        $_SESSION['error_message'] = "NewsLetter Not Sent";
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    }


// $testMessage = "Test Message";

/*

if(mail("johnszoszorekr8@gmail.com", "new subject", $testMessage))
{
    echo"Email Sent";
} else {
    echo"could not send email";
}
*/

?>