<?php

/******************************************************************************************************************\
 *File:    request.php                                                                                             *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofmasters CMS (Customer Management System)                                                            *
 *Date:    August 8th, 2019                                                                                        *
 *Purpose: This file will handle received request data from the front end                                          *
\******************************************************************************************************************/

//variable to store more detailed messages to frontend (JSON)
$message = '';

//get the endpoint requested
$request = $_SERVER['PATH_INFO'];
$post_body = json_decode(file_get_contents('php://input'));

switch ($request) {
    case '/authenticate':
        //verify user login
        Authentication::verify_user($post_body);
        break;
    case '/register':
        //register new user
        Registration::register_user($post_body);
        break;
    case '/confirm_registration':
        //this route is called when the user clicks on the registration URL within the confirmation email
        Registration::confirm_registration($post_body);
        break;
    case '/contact':
        ContactMessage::process_contact_message($post_body);
        break;
    case '/delete_message':
        Message::delete_messsage($post_body);
        break;
    case '/send_new_message':
    case '/send_message':
        Message::process_message($post_body);
        break;
    case '/get_received_messages':
        Message::retrieve_messages_received($post_body);
        break;
    case '/get_sent_messages':
        Message::retrieve_messages_sent($post_body);
        break;
    case '/get_message_users':
        $admins = UserDB::get_admin_users();
        $message = json_encode(array('all_admins' => $admins), JSON_PRETTY_PRINT);
        exit($message);
        break;
    case '/get_all_customers':
        //admin is calling this function to view all customers
        $customers = UserDB::get_all_customers();
        $message = json_encode(array('all_customers' => $customers), JSON_PRETTY_PRINT);
        exit($message);
        break;
    case '/get_all_jobs':
        //admin is calling this function to view all jobs
        Jobs::get_all_jobs_admin();
        break;
    case '/get_all_open_jobs':
        //admin is calling this function to view all open jobs
        Jobs::get_all_jobs_open_admin();
        break;
    case '/get_all_completed_jobs':
        //admin is calling this function to view all completed jobs
        Jobs::get_all_jobs_completed_admin();
        break;
    case '/get_open_jobs':
        Jobs::get_all_jobs_open_user($post_body);
        break;
    case '/get_completed_jobs':
        Jobs::get_all_jobs_completed_user($post_body);
        break;
    case '/save_job':
        Jobs::save_new_job($post_body);
        break;
    case '/reset_password':
        Account::account_password_reset($post_body);
        break;
    case '/update_address':
        Account::account_address_change($post_body);
        break;
    case '/delete_account':
        Account::delete_account($post_body);
        break;
}
