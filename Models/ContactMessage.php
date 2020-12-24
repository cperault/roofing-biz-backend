<?php

/******************************************************************************************************************\
 *File:    ContactMessage.php                                                                                      *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofmasters CMS (Customer Management System)                                                            *
 *Date:    April 10th, 2020                                                                                        *
 *Purpose: This class will handle contact messages                                                                 *
\******************************************************************************************************************/

class ContactMessage
{
    //process the contact message request
    public static function process_contact_message($request_input)
    {
        //get all data from the contact form
        $first_name = htmlspecialchars($request_input->contactFirstName);
        $last_name = htmlspecialchars($request_input->contactLastName);
        $email = htmlspecialchars($request_input->contactEmail);
        $message = htmlspecialchars($request_input->contactDescriptionText);
        $timestamp = date('Y-m-d h:i:s');

        //array to store input from contact message
        $contact_message_fields = array('First Name' => $first_name, 'Last Name' => $last_name, 'Contact Email' => $email, 'Message Body' => $message);

        $validation_result = Validation::is_valid($contact_message_fields);
        
        if ($validation_result) {
            $message = json_encode(array('validation_response' => 'Rejected', 'rejection_reason' => $validation_result), JSON_PRETTY_PRINT);
            exit($message);
        }

        //save message to DB with subject as "Contact form message"
        $subject = "Contact form message";
        //save message to the contact table
        MessageDB::save_contact_form_message($first_name, $last_name, $email, $subject, $message, $timestamp);
        exit;
    }
}
