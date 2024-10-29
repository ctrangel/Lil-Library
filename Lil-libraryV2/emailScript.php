<?php
session_start();
require 'vendor/autoload.php'; //Necessary for composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if(isset($_POST['signUpBtn']))
{
    $to = $_POST['email'];
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
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'johnszoszorekr8@gmail.com';
        $mail->Password = 'bbnj cjoo fmub wftl'; // <-- Setup from google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('johnszoszorekr8@gmail.com', 'Lil-Library'); //<-- Lil-Library Header
        $mail->addAddress($to); //<-- 2nd param is optional
        $mail->isHTML(false); //<-- make it true if sending HTML content as message
        $mail->Subject = $subject;
        $mail->Body = $message;
        if($emailAttachment){
            $mail->addAttachment($emailAttachment);
        }
        $mail->send();
        $_SESSION['success_message'] = "Email Message has been sent successfully";
    }catch (Exception $e){
        $_SESSION['error_message'] =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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