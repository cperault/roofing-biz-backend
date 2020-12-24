<?php

/******************************************************************************************************************\
 *File:    user_db.php                                                                                             *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofmasters CMS (Customer Management System)                                                            *
 *Date:    August 8th, 2019                                                                                        *
 *Purpose: This class will store all queries relating to users                                                     *
\******************************************************************************************************************/
class UserDB
{
    //query to add new registered user
    public static function add_user($first_name, $last_name, $phone_number, $email_address, $user_password, $activation_secret)
    {
        try {
            $db = Database::getDB();
            $query = 'INSERT into RMUsers (firstName, lastName, phoneNumber, emailAddress, userPassword, activationSecret)
                  VALUES (:first_name, :last_name, :phone_number, :email_address, :user_password, :activation_secret)';
            $statement = $db->prepare($query);
            $statement->bindValue(':first_name', $first_name);
            $statement->bindValue(':last_name', $last_name);
            $statement->bindValue(':phone_number', $phone_number);
            $statement->bindValue(':email_address', $email_address);
            $statement->bindValue(':user_password', $user_password);
            $statement->bindValue(':activation_secret', $activation_secret);
            $statement->execute();
            $user_id = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement->closeCursor();
            return $user_id['userID'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //query to get user's password by email address (for auth/verification)
    public static function get_password($email_address)
    {
        $db = Database::getDB();
        $query = 'SELECT userPassword
                  FROM RMUsers 
                  WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $password = $statement->fetch();
        $statement->closeCursor();
        return $password['userPassword'];
    }

    //query to get user's ID based on email
    public static function get_user_ID($email_address)
    {
        $db = Database::getDB();
        $query = 'SELECT userID
                  FROM RMUsers 
                  WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return $result['userID'];
    }

    //query to get user's email address based on ID
    public static function get_user_email($user_id)
    {
        $db = Database::getDB();
        $query = 'SELECT emailAddress
                  FROM rmusers 
                  WHERE userID = :user_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return $result['emailAddress'];
    }

    //query to get all info on a user
    public static function get_user_info($email_address)
    {
        $db = Database::getDB();
        $query = 'SELECT userID, firstName, lastName, phoneNumber, emailAddress, userRole
                  FROM rmusers
                  WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $user_object = $statement->fetchAll(PDO::FETCH_ASSOC);

        $user = [];
        foreach ($user_object as $value) {
            $user[] = array('userID' => $value['userID']);
            $user[] = array('firstName' => $value['firstName']);
            $user[] = array('lastName' => $value['lastName']);
            $user[] = array('phoneNumber' => $value['phoneNumber']);
            $user[] = array('emailAddress' => $value['emailAddress']);
            $user[] = array('userRole' => $value['userRole']);
        }

        //get address(es) for user and append to user array
        $query = 'SELECT addresses.addressID, addressName, addressCity, addressState, addressZip
                  FROM addresses
                  JOIN rmusers ON addresses.customerID = rmusers.userID
                  WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $user_object = $statement->fetchAll(PDO::FETCH_ASSOC);
        $addresses = [];

        foreach ($user_object as $value) {
            $addresses[] = array('userAddress' => array('addressID' => $value['addressID'], 'addressName' => $value['addressName'], 'addressCity' => $value['addressCity'], 'addressState' => $value['addressState'], 'addressZip' => $value['addressZip']));
        }

        array_push($user, $addresses);

        $statement->closeCursor();
        return $user;
    }

    //query to check if the email being used to register is already being used
    public static function is_registered($email_address)
    {
        $db = Database::getDB();
        $query = 'SELECT emailAddress FROM rmusers WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $user_exists = false;
        //check if record found, return true or false
        if ($statement->rowCount() >= 1) {
            $user_exists = true;
        }
        $statement->closeCursor();
        return $user_exists;
    }
    //query to retrieve activation secret by user username
    public static function get_activation_secret($email_address)
    {
        $db = Database::getDB();
        $query = 'SELECT activationSecret FROM rmusers WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $secret = $statement->fetch();
        return $secret['activationSecret'];
    }
    //query to check if user account is activated
    public static function is_activated($email_address)
    {
        $db = Database::getDB();
        $query = 'SELECT emailAddress FROM rmusers WHERE emailAddress = :email_address AND isActiveAccount = 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $account_activated = false;
        if ($statement->rowCount() >= 1) {
            $account_activated = true;
        }
        return $account_activated;
    }
    //query to activate account after successful verification via email
    public static function activate_user_account($email_address)
    {
        $db = Database::getDB();
        $query = 'UPDATE rmusers SET isActiveAccount = 1 WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $statement->closeCursor();
    }
    //query to update activation secret for user in instances of password reset/resending of confirmation code
    public static function change_secret($email_address, $new_secret)
    {
        $db = Database::getDB();
        $query = 'UPDATE rmusers SET activationSecret = :new_secret WHERE emailAddress = :email_address';
        $statement = $db->prepare($query);
        $statement->bindValue(':email_address', $email_address);
        $statement->bindValue(':new_secret', $new_secret);
        $statement->execute();
        $statement->closeCursor();
    }
    //query to update user's password
    public static function update_password($email_address, $new_password)
    {
        $db = Database::getDB();
        $query = "UPDATE rmusers SET userPassword = :new_password WHERE emailAddress = :email_address";
        $statement = $db->prepare($query);
        $statement->bindValue(':new_password', $new_password);
        $statement->bindValue(':email_address', $email_address);
        $statement->execute();
        $statement->closeCursor();
    }
    //query to add address alongside customer registration
    public static function add_address($customer_id, $address_name, $address_city, $address_state, $address_zip)
    {
        $db = Database::getDB();
        $query = "INSERT INTO addresses (customerID, addressName, addressCity, addressState, addressZip)
                  VALUES (:customer_id, :address_name, :address_city, :address_state, :address_zip)";
        $statement = $db->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->bindValue(':address_name', $address_name);
        $statement->bindValue(':address_city', $address_city);
        $statement->bindValue(':address_state', $address_state);
        $statement->bindValue(':address_zip', $address_zip);
        $statement->execute();
        //$id = $db->lastInsertId();
        $statement->closeCursor();
        //return $id;
    }

    //query to return array of users who are registered as admins
    public static function get_admin_users()
    {
        $db = Database::getDB();
        $query = "SELECT userID, firstName, lastName from rmusers WHERE userRole = 'admin'";
        $statement = $db->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    //query to verify address passed in belongs to user ID and address ID passed in (in this table, ID linked is called `customerID`)
    public static function user_has_address($address, $customer_id, $job_address_id)
    {
        $db = Database::getDB();
        $query = "SELECT addressID FROM addresses WHERE addressName = :address_name AND customerID = :customer_id AND addressID = :job_address_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':address_name', $address);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->bindValue(':job_address_id', $job_address_id);
        $statement->execute();
        $address_verified = false;
        if ($statement->rowCount() >= 1) {
            $address_verified = true;
        }
        return $address_verified;
    }

    //query to retrieve all customers who have created an account and confirmed their email address
    public static function get_all_customers()
    {
        $db = Database::getDB();
        $query = "SELECT userID, firstName, lastName, phoneNumber, emailAddress, userRole, addresses.addressID, addresses.addressName, addresses.addressCity, addresses.addressState, addresses.addressZip
                  FROM rmusers
                  JOIN addresses ON rmusers.userID = addresses.customerID
                  WHERE isActiveAccount = 1 AND userRole = 'customer'
                  GROUP BY firstName";
        $statement = $db->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    //query to update addressID from rmusers
    // public static function update_user_address($user_id, $new_address_id)
    // {
    //     $db = Database::getDB();
    //     $query = 'UPDATE rmusers SET addressID = :new_address_id WHERE userID = :user_id';
    //     $statement = $db->prepare($query);
    //     $statement->bindValue(':new_address_id', $new_address_id);
    //     $statement->bindValue(':user_id', $user_id);
    //     $statement->execute();
    // }

    //query to just update the information in the addresses table
    public static function update_address_information($address_id, $customer_id, $address_name, $address_city, $address_state, $address_zip)
    {
        $db = Database::getDB();
        $query = "UPDATE addresses SET addressName = :address_name, addressCity = :address_city, addressState = :address_state, addressZip = :address_zip WHERE addressID = :address_id AND customerID = :customer_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':address_name', $address_name);
        $statement->bindValue(':address_city', $address_city);
        $statement->bindValue(':address_state', $address_state);
        $statement->bindValue(':address_zip', $address_zip);
        $statement->bindValue(':address_id', $address_id);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $statement->closeCursor();
    }

    //query to update flag on user record in rmusers
    public static function remove_user($user_id)
    {
        $db = Database::getDB();
        $query = "UPDATE rmusers SET isDeleted = 1 WHERE userID = :user_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $statement->closeCursor();
    }

    //query to check status of account trying to log in
    public static function account_closed($email)
    {
        $db = Database::getDB();
        $query = "SELECT firstName FROM rmusers WHERE isDeleted = 1 AND emailAddress = :email";
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);

        $statement->execute();
        $account_is_closed = false;
        if ($statement->rowCount() >= 1) {
            $account_is_closed = true;
        }
        return $account_is_closed;
        $statement->closeCursor();
    }
}
