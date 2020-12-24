<?php

/******************************************************************************************************************\
 *File:    Confirmation.php                                                                                        *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    August 8th, 2019                                                                                        *
 *Purpose: This class will handle the activation secret for registration/authentication                            *
\******************************************************************************************************************/

//function to generate an encrypted code which will be emailed to the email address of the person registering
function generate_security_code()
{
    //salt
    $options = [
        'cost' => 14,
    ];
    //value to hash
    $pre_hash_value = rand(300, 5000);
    //hash the randomly generated value and use bcrypt with salt
    $security_code = password_hash($pre_hash_value, PASSWORD_BCRYPT, $options);
    return $security_code;
}
