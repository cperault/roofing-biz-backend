<?php

/******************************************************************************************************************\
 *File:    Message.php                                                                                             *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    April 10th, 2020                                                                                        *
 *Purpose: This class will handle messages between user(s) and business owner(s)                                   *
\******************************************************************************************************************/

class Message
{
    //handle user/owner messages
    public static function process_message($request_input)
    {
        //get data from request
        $recipient_id = htmlspecialchars($request_input->recipientID);
        $sender_id = htmlspecialchars($request_input->senderID);
        $sender_first_name = htmlspecialchars($request_input->senderFirstName);
        $sender_last_name = htmlspecialchars($request_input->senderLastName);
        $message_subject = htmlspecialchars($request_input->messageSubject);
        $message_content = htmlspecialchars($request_input->messageContent);
        $timestamp = date('Y-m-d h:i:s');
        //get email address from DB based on sender's ID
        $sender_email = UserDB::get_user_email($sender_id);

        //array to store input from contact message
        $contact_message_fields = array('Message Sender' => $sender_id, 'Message Recipient' => $recipient_id, 'Message Subject' => $message_subject, 'Message Body' => $message_content);

        $validation_result = Validation::is_valid($contact_message_fields);

        if ($validation_result) {
            $message = json_encode(array('validation_response' => 'Rejected', 'rejection_reason' => $validation_result), JSON_PRETTY_PRINT);
            exit($message);
        }

        //save the message
        MessageDB::save_message_registered($recipient_id, $sender_id, $sender_first_name, $sender_last_name, $sender_email, $message_subject, $message_content, $timestamp);
        exit;
    }

    //handle retrieval of messages received
    public static function retrieve_messages_received($request_input)
    {
        //get user ID from request
        $user_id = htmlspecialchars($request_input->userID);
        //get all messages that have been sent to designated user ID
        $messages = MessageDB::get_received_messages($user_id);
        $message = json_encode(array('all_messages' => $messages), JSON_PRETTY_PRINT);
        exit($message);
    }

    //handle retrieval of messages sent
    public static function retrieve_messages_sent($request_input)
    {
        //get user ID from request
        $user_id = htmlspecialchars($request_input->userID);
        //get all messages that have been sent by designated user ID
        $messages = MessageDB::get_sent_messages($user_id);
        $message = json_encode(array('all_messages' => $messages), JSON_PRETTY_PRINT);
        exit($message);
    }

    //handle deletion of a message by message ID
    public static function delete_messsage($request_input)
    {
        //get message ID from request
        $message_id = htmlspecialchars($request_input->messageID);
        //call the query method to delete a user message from MessageDB class
        MessageDB::delete_user_message($message_id);
        $message = json_encode(array('message_deletion_success' => "Message was successfully deleted."), JSON_PRETTY_PRINT);
        exit($message);
    }
}
