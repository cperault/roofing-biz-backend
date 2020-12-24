<?php

/******************************************************************************************************************\
 *File:    Authentication.php                                                                                      *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    April 10th, 2020                                                                                        *
 *Purpose: This class will handle user authentication                                                              *
\******************************************************************************************************************/
//load PHPMailer init file
require_once('Models/PHPMailerInit.php');
class Authentication
{
    //handle user verification
    public static function verify_user($request_input)
    {
        $email = htmlspecialchars($request_input->email);
        $password = $request_input->password;
        //first thing's first--check if user has `isDeleted` marked as `1` in DB
        if (UserDB::account_closed($email)) {
            //exit script; user is not allowed to access account after having deleted it
            $message = json_encode(array('verification' => "Failed", 'reasoning' => array("Login Email" => "That email address cannot be used to login or register, sorry."), JSON_PRETTY_PRINT));
            exit($message);
        }

        //associative array to store email and password credentials from the form
        $input_array = array('Login Email' => $email, 'Login Password' => $password);
        $validation_result = Validation::is_valid($input_array);
        if (count($validation_result) > 0) {
            //form failed validation
            $message = json_encode(array('verification' => 'Failed', 'reasoning' => $validation_result), JSON_PRETTY_PRINT);
            exit($message);
        } else {
            //get user's password from DB and then compare with entered password
            $stored_password = UserDB::get_password($email);
            if (password_verify($password, $stored_password)) {
                //check if user logging in has an activated account
                $activated = UserDB::is_activated($email);
                if (!$activated) {
                    //recreate secret code and resend confirmation email to email trying to log in
                    $activation_secret = generate_security_code();
                    //update user record
                    UserDB::change_secret($email, $activation_secret);
                    //resend confirmation code to user in session; if there's a failure, script will exit with unique message
                    PHPMailerInit::send_email($email, $activation_secret);
                    $message = json_encode(array('verification' => 'Inactive'), JSON_PRETTY_PRINT);
                    exit($message);
                } else {
                    //user has an activated account and password is correct
                    //get user info from DB and then return JSON to frontend with their info (name, email, phone)
                    $user_array = UserDB::get_user_info($email);
                    $message = json_encode(array('verification' => 'Password verified.', 'user' => $user_array), JSON_PRETTY_PRINT);
                    exit($message);
                }
            } else {
                $message = json_encode(array('verification' => 'Password does not match.'), JSON_PRETTY_PRINT);
                exit($message);
            }
        }
    }
}
