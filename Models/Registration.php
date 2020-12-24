<?php

/******************************************************************************************************************\
 *File:    Registration.php                                                                                        *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    April 10th, 2020                                                                                        *
 *Purpose: This class will handle user registration                                                                *
\******************************************************************************************************************/

use PHPMailer\PHPMailer\Exception;

class Registration
{
    //process the new user registration
    public static function register_user($request_input)
    {

        //extract user info
        $fname = htmlspecialchars($request_input->firstName);
        $lname = htmlspecialchars($request_input->lastName);
        $phone = htmlspecialchars($request_input->phone);
        $email = htmlspecialchars($request_input->email);
        $password = $request_input->password;
        $userAvatarText = htmlspecialchars($request_input->userAvatar);

        //first thing's first--check if user has `isDeleted` marked as `1` in DB
        if (UserDB::account_closed($email)) {
            //exit script; user is not allowed to access account after having deleted it
            $message = json_encode(array('verification' => "Failed", 'reasoning' => ["That email address cannot be used to login or register, sorry."]), JSON_PRETTY_PRINT);
            exit($message);
        }

        //get address information
        $address_name = htmlspecialchars($request_input->addressName);
        $address_city = htmlspecialchars($request_input->addressCity);
        $address_state = htmlspecialchars($request_input->addressState);
        $address_zip = htmlspecialchars($request_input->addressZip);

        //create associative array to store input values for validation
        $input_array = array('First Name' => $fname, 'Last Name' => $lname, 'Phone Number' => $phone, 'Email Address' => $email, 'Password' => $password, 'Address Name' => $address_name, 'Address City' => $address_city, 'Address State' => $address_state, 'Address ZIP' => $address_zip);
        $validation_result = Validation::is_valid($input_array);
        if (count($validation_result) > 0) {
            $message = json_encode(array('verification' => 'Failed', 'reasoning' => $validation_result), JSON_PRETTY_PRINT);
            exit($message);
        } else {
            //get PHPMailer config instance
            $mail = PHPMailerInit::set_email_config();
            //set up hash option (salt)
            $option = ['cost' => 13];
            $hash = password_hash($password, PASSWORD_BCRYPT, $option);

            //get activation secret using the function in Confirmation.php
            $activation_secret = generate_security_code();
            $activation_link = "https://codymorrisexteriors.com/confirm_registration?code=" . $activation_secret . "?email=" . $email;
            try {
                //lets the user know we are the ones emailing
                $mail->setFrom('roofmastersdevteam@gmail.com', 'Cody Morris Exteriors Developer');
                //who will receiving the email
                $mail->addAddress($email);
                //create reply-to email address
                $mail->addReplyTo('roofmastersdevteam@gmail.com', 'Cody Morris Exteriors Developer');
                $mail->Subject = 'Please confirm your registration.';
                $mail->AltBody = 'This is the email body in plain text.';
                $email_message = file_get_contents("registration_template.php");
                $email_message = str_replace("%first_name%", $fname, $email_message);
                $email_message = str_replace("%activation_link%", $activation_link, $email_message);
                $mail->msgHTML($email_message);
                $mail->isHTML(true);
                $mail->CharSet = "UTF-8";
                $mail->send();
            } catch (Exception $e) {
                $email_result = ["Uh oh. " . $e->errorMessage()];
                $message = json_encode(array('email_status' => 'Failed', 'reasoning' => $email_result), JSON_PRETTY_PRINT);
                exit($message);
            }
            UserDB::add_user($fname, $lname, $phone, $email, $hash, $activation_secret, $userAvatarText);
            $user_id = UserDB::get_user_ID($email);
            $new_address_id = UserDB::add_address($user_id, $address_name, $address_city, $address_state, $address_zip);
            $_SESSION['userID'] = $user_id;
            $_SESSION['email_address_being_registered'] = $email;
            exit;
        }
    }

    //confirm user registration
    public static function confirm_registration($request_input)
    {
        $email = htmlspecialchars($request_input->emailAddress);
        if ($email !== "") {
            //make sure the user hasn't already confirmed first as anyone can access the `confirm_registration` view
            $registered = UserDB::is_registered($email);
            if (!$registered) {
                $error_array = ["That email address is invalid."];
                $message = json_encode(array('registration_verification' => 'Failed', 'reasoning' => $error_array), JSON_PRETTY_PRINT);
            } else {
                //before proceeding, check if the email address being used has already been activated
                $already_activated = UserDB::is_activated($email);
                if (!$already_activated) {
                    //get the registration code
                    $registration_code = htmlspecialchars($request_input->registrationCode);
                    //check if entered code matches that stored in the database for the username entered
                    $stored_secret = UserDB::get_activation_secret($email);
                    if ($registration_code !== $stored_secret) {
                        $error_array = ["Verification failed. Please verify you have entered the code we emailed you and try again."];
                        //password does not match--exit script and redirect to login
                        $message = json_encode(array('registration_verification' => 'Failed', 'reasoning' => $error_array), JSON_PRETTY_PRINT);
                    } else {
                        $message = json_encode(array('registration_verification' => 'Passed'), JSON_PRETTY_PRINT);
                        //activate the user's account
                        UserDB::activate_user_account($email);
                    }
                } else {
                    $error_array = ["You've already verified your email address. Please log in."];
                    $message = json_encode(array('registration_verified_already' => 'Already done', 'reasoning' => $error_array), JSON_PRETTY_PRINT);
                }
            }
            exit($message);
        }
    }
}
