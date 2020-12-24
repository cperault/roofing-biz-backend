<?php

/******************************************************************************************************************\
 *File:    JobsDB.php                                                                                             *
 *Author:  Christopher Perault                                                                                    *
 *Project: Roofing Biz Backend                                                           *
 *Date:    November 25th, 2019                                                                                    *
 *Purpose: This class will handle all queries relating to jobs                                                    *
\******************************************************************************************************************/

class JobsDB
{
    //query to get all open jobs by user ID
    public static function get_open_jobs($user_id)
    {
        $db = Database::getDB();
        $query = "SELECT *
                  FROM jobs
                  WHERE userID = :user_id AND jobStatus = 'open'";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $jobs = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    }

    //admin query to get all open jobs
    public static function get_all_open_jobs()
    {
        $db = Database::getDB();
        $query = "SELECT *
                  FROM jobs
                  WHERE jobStatus = 'open'";
        $statement = $db->prepare($query);
        $statement->execute();
        $jobs = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    }

    //query to get all completed jobs by user ID
    public static function get_completed_jobs($user_id)
    {
        $db = Database::getDB();
        $query = "SELECT *
                  FROM jobs
                  WHERE userID = :user_id AND jobStatus = 'completed'";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $jobs = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    }

    //admin query to get all completed jobs
    public static function get_all_completed_jobs()
    {
        $db = Database::getDB();
        $query = "SELECT *
                  FROM jobs
                  WHERE jobStatus = 'completed'";
        $statement = $db->prepare($query);
        $statement->execute();
        $jobs = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    }

    //query to save new job
    public static function save_new_job($user_id, $job_address_id, $job, $job_description, $job_date_submitted, $type)
    {
        $db = Database::getDB();
        $query = 'INSERT INTO jobs (userID,jobAddressID,jobTitle,jobDescription,jobDateSubmitted)
                  VALUES (:user_id,:jobAddressID,:job_title,:job_description,:job_date_submitted)';
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->bindValue(':jobAddressID', $job_address_id);
        $statement->bindValue(':job_title', $job);
        $statement->bindValue(':job_description', $job_description);
        $statement->bindValue(':job_date_submitted', $job_date_submitted);
        $statement->execute();
        $job_id = $db->lastInsertId();
        $statement->closeCursor();

        //update the job type column(s)
        JobsDB::add_job_type($job_id, $type);
    }

    //query to be called after a job is created to update which type of job it is
    public static function add_job_type($job_id, $type)
    {
        $db = Database::getDB();
        $query = "UPDATE jobs
                  SET $type WHERE jobID = :job_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':job_id', $job_id);
        $statement->execute();
    }

    //admin query to get all jobs
    public static function get_all_jobs()
    {
        $db = Database::getDB();
        $query = "SELECT addresses.addressName, jobs.jobTitle, jobs.jobDescription, jobs.jobDateSubmitted, jobs.jobDateCompleted, jobs.jobStatus
                  FROM jobs
                  JOIN addresses ON jobs.jobAddressID = addresses.addressID
                  ORDER BY jobs.jobDateSubmitted ASC";
        $statement = $db->prepare($query);
        $statement->execute();
        $jobs = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $jobs;
    }
}
