<?php

/******************************************************************************************************************\
 *File:    PHPMailerInit.php                                                                                       *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    April 10th, 2020                                                                                        *
 *Purpose: This class will emailing                                                                                *
\******************************************************************************************************************/

//import the PHPMailer classes
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class PHPMailerInit
{
    //set and return the PHPMailer class instance
    public static function set_email_config()
    {
        /* PHPMailer setup */
        //instantiate PHPMailer object
        $mail = new PHPMailer(true); //true enables exception handling
        //SMTP server settings setup as shown in the library's documentation
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = PHPMAILER_EMAIL;                        // SMTP username
        $mail->Password   = PHPMAILER_APP_PASS;                     // App password to allow request to SMTP server
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 587;
        /* End of PHPMailer setup */
        return $mail;
    }

    public static function send_email($email, $activation_secret)
    {
        $mail = PHPMailerInit::set_email_config();
        //resend confirmation code to user in session
        try {
            //lets the user know we are the ones emailing
            $mail->setFrom('roofmastersdevteam@gmail.com', 'Cody Morris Exteriors Developer');
            //who will receiving the email
            $mail->addAddress($email);
            //create reply-to email addresss
            $mail->addReplyTo('roofmastersdevteam@gmail.com', 'Cody Morris Exteriors Developer');
            //set up email content
            $mail->isHTML(true);
            $mail->Subject = "Please confirm your registration.";
            $mail->Body = "It looks like your email address was never confirmed. To protect you, we have reset the confirmation code. Your new code is " . $activation_secret . " and will need to be entered on the confirmation page.";
            $mail->AltBody = "This is the email body in plain text.";
            $mail->send();
        } catch (Exception $e) {
            $reasoning_array = ["I'm sorry, there was an issue with completing your registration. Please try again in a few minutes."];
            $message = json_encode(array('email_status' => 'Failed', 'reasoning' => $reasoning_array), JSON_PRETTY_PRINT);
            exit($message);
        }
    }
}
