<?php

/******************************************************************************************************************\
 *File:    Validation.php                                                                                          *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    November 10th, 2019                                                                                     *
 *Purpose: This class will handle validation of forms throughout the site                                          *
\******************************************************************************************************************/

class Validation
{

    //function to validate proper email entry
    public static function is_valid_email($email = '')
    {
        //initial validator variable
        $valid = false;
        //remove illegal characters from the input
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        //validate the sanitized email address; returns true if filter_var passes validation of the email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valid = true;
        }
        return $valid;
    }
    //function to check if email is already in use
    public static function email_already_in_use($email = '')
    {
        //initial validator variable
        $valid = false;
        //check to make sure that the user hasn't already registered
        if (!UserDB::is_registered($email)) {
            $valid = true;
        }
        return $valid;
    }
    //function to validate proper phone number entry
    public static function is_valid_phone_number($phone = '')
    {
        //initial validator variable
        $valid = false;
        if (preg_match("/(\d{3}+\-\d{3}+\-\d{4}+)/", $phone)) {
            $valid = true;
        }

        return $valid;
    }
    //function to validate input is neither empty nor null
    public static function input_is_present($input = '')
    {
        $valid = false;
        if (trim($input) !== '') {
            $valid = true;
        }
        return $valid;
    }
    //function to validate input does not contain special characters
    public static function input_contains_special_characters($input = '')
    {
        $valid = false;
        if (preg_match('/[#$%^&*()+=\[\];,.\/{}|":<>!?~\\\\]/', $input) === 1) {
            $valid = true;
        }
        return $valid;
    }
    //function to validate input only contains uppercase/lowercase alphabet characters
    public static function is_valid_zip($input = '')
    {
        $valid = false;
        if (preg_match('/^\d{5}$/', $input) === 1) {
            $valid = true;
        }
        return $valid;
    }
    //function to validate input contains numerical characters
    public static function input_contains_numerical($input = '')
    {
        return preg_match('#[0-9]#', $input);
    }

    //function to validate state entered is a valid US state
    public static function state_exists($state)
    {
        $valid = false;
        //array of states abbreviated; this will be used to validate registration form `State` field
        $stateAbbreviations = array(
            'AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA',
            'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA',
            'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND',
            'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT',
            'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY'
        );

        if (in_array($state, $stateAbbreviations)) {
            $valid = true;
        }

        return $valid;
    }

    //function to validate number of chars in value
    public static function within_char_limit($input = '', $min = 0, $max = 0)
    {
        $valid = false;
        if (strlen($input) >= $min && strlen($input) <= $max) {
            $valid = true;
        }
        return $valid;
    }
    //function to validate each input value
    public static function is_valid($input = [], $user_id = 0, $job_address_id = 0)
    {
        //array to store validation result(s)
        $result = [];
        //iterate through the array of input values received
        foreach ($input as $key => $value) {
            if (!is_array($value)) {
                //length of input received
                $length = strlen($value);
            }

            switch ($key) {
                case 'Login Email':
                case 'Login Password':
                    //check username is not empty 
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required");
                    }
                    if (!Validation::within_char_limit($value, 10, 255)) {
                        $result[] = array($key => $key . " must be between 10 and 255 characters");
                    }
                    break;
                case 'First Name':
                case 'Last Name':
                    //check if first or last name is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required");
                    }
                    //first or last name must not exceed 50 characters
                    elseif (!Validation::within_char_limit($value, 1, 50)) {
                        $result[] = array($key => $key . " must be between 1 and 50 characters");
                    }
                    //first or last name cannot contain special characters
                    elseif (Validation::input_contains_special_characters($value)) {
                        $result[] = array($key => $key . " cannot contain special characters");
                    }
                    break;
                case 'Phone Number':
                    //check phone number is not empty 
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required");
                    } elseif (!Validation::is_valid_phone_number($value)) {
                        $result[] = array($key => $key . " is invalid");
                    }
                    break;
                case 'Message Sender':
                case 'Message Recipient':
                    //check that the client didn't edit the message before sending; recipient and sender IDs required before saving message
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required");
                    }
                    break;
                case 'Message Subject':
                    //check length requirements; subject not required, but cannot exceed 255 characters
                    if (!Validation::within_char_limit($value, 0, 255)) {
                        $result[] = array($key => $key . " cannot exceed 255 characters");
                    }
                    break;
                case 'Message Body':
                    //check that the contact message body is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key  . " is required");
                    }
                    break;
                case 'Contact Email':
                    //check that email address is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key  . " is required");
                    }
                    //validate proper email format
                    elseif (!Validation::is_valid_email($value)) {
                        $result[] = array($key => $key . " is invalid");
                    } elseif (!Validation::within_char_limit($value, 3, 255)) {
                        $result[] = array($key => $key . " must be between 3 and 255 characters");
                    }
                    break;
                case 'Email Address':
                    //check that email address is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key  . " is required");
                    }
                    //validate proper email format
                    elseif (!Validation::is_valid_email($value)) {
                        $result[] = array($key => $key . " is invalid");
                    } elseif (!Validation::email_already_in_use($value)) {
                        $result[] = array($key => $key . " provided has already been used for registration");
                    } elseif (!Validation::within_char_limit($value, 3, 255)) {
                        $result[] = array($key => $key . " must be between 3 and 255 characters");
                    }
                    break;
                case 'Password':
                    //check that password is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key  . " is required");
                    }
                    //check that password is between 10 and 100 characters
                    elseif (!Validation::within_char_limit($value, 10, 255)) {
                        $result[] = array($key => $key . " must be between 10 and 255 characters");
                    }
                    break;
                case 'Job Description':
                    //check that user has entered job description
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " cannot be empty");
                    } elseif (!Validation::within_char_limit($value, 10, 255)) {
                        $result[] = array($key => $key . " must be between 10 and 255 characters");
                    }
                    break;
                case 'checkbox':
                    //make sure a job checkbox was selected
                    if (count($value) <= 0) {
                        $result[] = array($key => "You must select at least one " . $key);
                    }
                    break;
                case 'Job Address':
                    //make sure user hasn't deselected address using dev tools/other hacky shit
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required.");
                    }
                    //make sure the address sent is one that belongs to them (this will only hit if they've tampered with form/request data)
                    elseif (!UserDB::user_has_address($value, $user_id, $job_address_id)) {
                        $result[] = array($key => "Address received in request does not match our records.");
                    } elseif (!Validation::within_char_limit($value, 1, 100)) {
                        $result[] = array($key => $key . " must be between 1 and 100 characters");
                    }
                    break;
                case 'Address Name':
                    //check that address name is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key  . " is required");
                    }
                    break;
                case 'Address City':
                    //check that city is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key  . " is required");
                    }
                    break;
                case 'Address State':
                    //check that state is not empty
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required");
                    } elseif (!Validation::state_exists($value)) {
                        $result[] = array($key => $key . " is not a state");
                    }
                    break;
                case 'Address ZIP':
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required");
                    } //zip must be only five numerical characters
                    elseif ($length < 5 || $length > 5) {
                        $result[] = array($key => $key . " must be five numbers in length");
                    }
                    //zip cannot contain letters
                    elseif (!Validation::is_valid_zip($value)) {
                        $result[] = array($key => $key . " must only contain numerical characters");
                    } elseif (Validation::input_contains_special_characters($value)) {
                        $result[] = array($key => $key . " cannot contain special characters");
                    }
                    break;
                case 'Delete Email':
                case 'Delete Password':
                    if (!Validation::input_is_present($value)) {
                        $result[] = array($key => $key . " is required");
                    }
                    break;
            }
        }
        return $result;
    }
}
