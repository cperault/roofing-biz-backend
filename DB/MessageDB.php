<?php

/******************************************************************************************************************\
 *File:    MessageDB.php                                                                                           *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    October 7th, 2019                                                                                       *
 *Purpose: This class will store all queries relating to messages                                                  *
\******************************************************************************************************************/
class MessageDB
{
    //query to save messages for registered users
    public static function save_message_registered($recipient_id, $sender_id, $sender_first_name, $sender_last_name, $sender_email, $message_subject, $message_content, $message_timestamp)
    {
        try {
            $db = Database::getDB();
            $query = 'INSERT INTO messages (recipientID,senderID,senderFirstName,senderLastName,senderEmail,messageSubject,messageContent,messageTimeStamp)
                          VALUES (:recipient_id,:sender_id,:sender_first_name,:sender_last_name,:sender_email,:message_subject,:message_content,:messageTimeStamp)';
            $statement = $db->prepare($query);
            $statement->bindValue(':recipient_id', $recipient_id);
            $statement->bindValue(':sender_id', $sender_id);
            $statement->bindValue(':sender_first_name', $sender_first_name);
            $statement->bindValue(':sender_last_name', $sender_last_name);
            $statement->bindValue(':sender_email', $sender_email);
            $statement->bindValue(':message_subject', $message_subject);
            $statement->bindValue(':message_content', $message_content);
            $statement->bindValue(':messageTimeStamp', $message_timestamp);
            $statement->execute();
            $statement->closeCursor();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    //query to save messages from contact form
    public static function save_contact_form_message($sender_first_name, $sender_last_name, $sender_email, $message_subject, $message_content, $message_timestamp)
    {
        $db = Database::getDB();
        $query = 'INSERT INTO contact (senderFirstName,senderLastName,senderEmail,messageSubject,messageContent,messageTimeStamp)
                      VALUES (:sender_first_name,:sender_last_name,:sender_email,:message_subject,:message_content,:messageTimeStamp)';
        $statement = $db->prepare($query);
        $statement->bindValue(':sender_first_name', $sender_first_name);
        $statement->bindValue(':sender_last_name', $sender_last_name);
        $statement->bindValue(':message_subject', $message_subject);
        $statement->bindValue(':sender_email', $sender_email);
        $statement->bindValue(':message_content', $message_content);
        $statement->bindValue(':messageTimeStamp', $message_timestamp);
        $statement->execute();
        $statement->closeCursor();
    }

    //query to get all received messages
    public static function get_received_messages($recipient_id)
    {
        $db = Database::getDB();
        $query = 'SELECT * FROM messages WHERE recipientID = :recipient_id ORDER BY messageTimeStamp DESC';
        $statement = $db->prepare($query);
        $statement->bindValue(':recipient_id', $recipient_id);
        $statement->execute();
        $messages = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $messages;
    }

    //query to get all sent messages
    public static function get_sent_messages($sender_id)
    {
        $db = Database::getDB();
        $query = 'SELECT * FROM messages WHERE senderID = :sender_id ORDER BY messageTimeStamp DESC';
        $statement = $db->prepare($query);
        $statement->bindValue(':sender_id', $sender_id);
        $statement->execute();
        $messages = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $messages;
    }

    //query to delete a message
    public static function delete_user_message($message_id)
    {
        $db = Database::getDB();
        $query = 'DELETE FROM messages WHERE messageID = :message_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':message_id', $message_id);
        $statement->execute();
        $statement->closeCursor();
    }
}
