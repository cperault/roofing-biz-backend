<?php

/******************************************************************************************************************\
 *File:    User.php                                                                                                *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    August 8th, 2019                                                                                        *
 *Purpose: This class will define User objects                                                                     *
\******************************************************************************************************************/

class User
{
    private $user_id;
    private $user_first_name;
    private $user_last_name;
    private $user_phone_number;
    private $user_email_address;
    private $user_role;
    private $user_address = array();


    public function __construct($user_id, $user_first_name, $user_last_name, $user_phone_number, $user_email_address, $user_role, $user_address)
    {
        $this->user_id = $user_id;
        $this->user_first_name = $user_first_name;
        $this->user_last_name = $user_last_name;
        $this->user_phone_number = $user_phone_number;
        $this->user_email_address = $user_email_address;
        $this->user_role = $user_role;
        $this->user_address = $user_address;
    }

    //getter and setter for the user ID
    public function getUser_id()
    {
        return $this->user_id;
    }
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;
    }

    //getter and setter for the first name
    public function getUser_first_name()
    {
        return $this->user_first_name;
    }
    public function setUser_first_name($user_first_name)
    {
        $this->user_first_name = $user_first_name;
    }

    //getter and setter for the last name
    public function getUser_last_name()
    {
        return $this->user_last_name;
    }
    public function setUser_last_name($user_last_name)
    {
        $this->user_last_name = $user_last_name;
    }

    //getter and setter for the phone number
    public function getUser_phone_number()
    {
        return $this->user_phone_number;
    }
    public function setUser_phone_number($user_phone_number)
    {
        $this->user_phone_number = $user_phone_number;
    }

    //getter and setter for the email address
    public function getEmail_address()
    {
        return $this->user_email_address;
    }
    public function setEmail_address($user_email_address)
    {
        $this->user_email_address = $user_email_address;
    }

    //getter and setter for the user role
    public function getUser_role()
    {
        return $this->user_role;
    }
    public function setUser_role($user_role)
    {
        $this->user_role = $user_role;
    }

    //getter and setter for the user address
    public function getUser_address()
    {
        return $this->user_address;
    }
    public function setUser_address($user_address)
    {
        $this->user_address = $user_address;
    }
}
