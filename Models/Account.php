<?php

/******************************************************************************************************************\
 *File:    Account.php                                                                                             *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    April 10th, 2020                                                                                        *
 *Purpose: This class will handle user account edits                                                               *
\******************************************************************************************************************/

class Account
{
    //handle account password reset
    public static function account_password_reset($request_input)
    {
        //even though client-side verification is done to ensure both passwords match, verify server-side as well
        $new_password = $request_input->new_password;
        $new_password_confirmed = $request_input->new_password_confirmed;
        //get email sent in from session
        $email_address = htmlspecialchars($request_input->email_address);
        if ($new_password !== $new_password_confirmed) {
            $message = json_encode(array('verification' => 'Failed', 'reasoning' => ["Both fields must match."]), JSON_PRETTY_PRINT);
        } else {
            $validation_array = array('Password' => $new_password);
            $validation_result = Validation::is_valid($validation_array);
            if (count($validation_result) > 0) {
                $message = json_encode(array('verification' => 'Failed', 'reasoning' => $validation_result), JSON_PRETTY_PRINT);
            } else {
                //validation passed, hash new password and then reset the user's password
                //set up hash option (salt)
                $option = ['cost' => 13];
                $hash = password_hash($new_password, PASSWORD_BCRYPT, $option);
                UserDB::update_password($email_address, $hash);
                $message = json_encode(array('verification' => "Password reset"), JSON_PRETTY_PRINT);
            }
        }
        exit($message);
    }

    //handle account address changes
    public static function account_address_change($request_input)
    {
        //get address data from the form
        $user_id = htmlspecialchars($request_input->userID);
        $address_name = htmlspecialchars($request_input->addressName);
        $address_city = htmlspecialchars($request_input->addressCity);
        $address_state = htmlspecialchars($request_input->addressState);
        $address_zip = htmlspecialchars($request_input->addressZip);
        $address_id = htmlspecialchars($request_input->addressID);

        //validate before proceeding
        $address_form_input = array('Address Name' => $address_name, 'Address City' => $address_city, 'Address State' => $address_state, 'Address ZIP' => $address_zip);
        $new_address_validation_result = Validation::is_valid($address_form_input);
        if (count($new_address_validation_result) > 0) {
            $message = json_encode(array('validation' => "Failed", 'reasoning' => $new_address_validation_result), JSON_PRETTY_PRINT);
        } else {
            //get addressID of the address which the user is updating
            if ($address_id === null) {
                //address will need to be added and then inserted into addresses table;
                UserDB::add_address($user_id, $address_name, $address_city, $address_state, $address_zip);
                //UserDB::update_user_address($user_id, $address_id, $new_address_id);
            } else {
                //update just the addresses record
                UserDB::update_address_information($address_id, $user_id, $address_name, $address_city, $address_state, $address_zip);
            }
        }
        exit($message);
    }

    //handle account deletion
    public static function delete_account($request_input)
    {
        //get request data
        $user_id = htmlspecialchars($request_input->userID);
        $email = htmlspecialchars($request_input->email);
        $password = $request_input->password;

        //validate input
        $delete_input = array('Delete Email' => $email, 'Delete Password' => $password);
        $delete_validation_result = Validation::is_valid($delete_input);
        if (count($delete_validation_result) > 0) {
            $message = json_encode(array('validation' => "Failed", 'reasoning' => $delete_validation_result), JSON_PRETTY_PRINT);
        } else {
            //get email by user_id and verify account belongs to request
            $email_address_from_id = UserDB::get_user_email($user_id);
            if ($email === $email_address_from_id) {
                //deactivate the account so that the user can no longer log in; admin will still be able to view data
                UserDB::remove_user($user_id);
                $message = json_encode(array('closed' => ["Your account has been closed. You will be redirected to the landing page now."]), JSON_PRETTY_PRINT);
            } else {
                $message = json_encode(array('validation' => "Failed", 'reasoning' => ["That email address does not match our records. Please try again."]), JSON_PRETTY_PRINT);
            }
        }
        exit($message);
    }
}
