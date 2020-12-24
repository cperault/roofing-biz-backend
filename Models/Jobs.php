<?php

/******************************************************************************************************************\
 *File:    Jobs.php                                                                                                *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    April 10th, 2020                                                                                        *
 *Purpose: This class will handle all things related to jobs                                                       *
\******************************************************************************************************************/

class Jobs
{
    //handle retrieval of all jobs, including open and completed for all users - admin action
    public static function get_all_jobs_admin()
    {
        $jobs = JobsDB::get_all_jobs();
        $message = json_encode(array('all_jobs' => $jobs), JSON_PRETTY_PRINT);
        exit($message);
    }
    //handle retrieval of all jobs by open status - admin action 
    public static function get_all_jobs_open_admin()
    {
        $jobs = JobsDB::get_all_open_jobs();
        $message = json_encode(array('all_jobs' => $jobs), JSON_PRETTY_PRINT);
        exit($message);
    }
    //handle retrieval of all jobs by completed status - admin action
    public static function get_all_jobs_completed_admin()
    {
        $jobs = JobsDB::get_all_completed_jobs();
        $message = json_encode(array('all_jobs' => $jobs), JSON_PRETTY_PRINT);
        exit($message);
    }

    //handle retrieval of all open jobs- user action
    public static function get_all_jobs_open_user($request_input)
    {
        //get user ID from request
        $user_id = htmlspecialchars($request_input->userID);
        //get all open jobs by user ID
        $jobs = JobsDB::get_open_jobs($user_id);
        $message = json_encode(array('open_jobs' => $jobs), JSON_PRETTY_PRINT);
        exit($message);
    }
    //handle retrieval of all jobs by completed status - user action
    public static function get_all_jobs_completed_user($request_input)
    {
        //get user ID from request
        $user_id = htmlspecialchars($request_input->userID);
        //get all completed jobs by user ID
        $jobs = JobsDB::get_completed_jobs($user_id);
        $message = json_encode(array('completed_jobs' => $jobs), JSON_PRETTY_PRINT);
        exit($message);
    }

    //handle new job creation
    public static function save_new_job($request_input)
    {
        //get data from request ($user_id, $job_title, $job_description, $job_date_submitted, $job_type)
        $user_id = htmlspecialchars($request_input->userID);
        $job_description = htmlspecialchars($request_input->jobDescription);
        $job_date_submitted = htmlspecialchars($request_input->jobDateSubmitted);
        $job_type = (array) $request_input->jobType;
        $job_title = implode(", ", $job_type);
        $job_address = htmlspecialchars($request_input->addressSelected);
        $job_address_id = htmlspecialchars($request_input->addressID);
        //validate the new job form data
        $new_job_data = array('Job Description' => $job_description, 'checkbox' => $job_type, 'Job Address' => $job_address);
        $new_job_validation_result = Validation::is_valid($new_job_data, $user_id, $job_address_id);
        if (count($new_job_validation_result) > 0) {
            $message = json_encode(array('validation' => "Failed", 'reasoning' => $new_job_validation_result), JSON_PRETTY_PRINT);
            exit($message);
        } else {
            $job = "";
            //prepare the SQL update statement that will be executed after a job is saved; this logic below will check if more or one columns needs to be set
            foreach ($job_type as $j) {
                if (count($job_type) > 1) {
                    $job .= "is" . $j . " = 1, ";
                } else {
                    $job .= "is" . $j . " = 1";
                }
            }
            //remove trailing comma if $job_type is more than one type
            if (count($job_type) > 1) {
                //remove whitespace and then do the substring method to remove `,` from end of the `$job` string
                $job = substr(trim($job), 0, -1);
            }
            //convert date received as mm-dd-yyyy to mysql date format yyyy-mm-dd
            $timestamp = strtotime($job_date_submitted);
            $job_date_formatted = date("Y-m-d", $timestamp);
            //get address ID
            JobsDB::save_new_job($user_id, $job_address_id, $job_title, $job_description, $job_date_formatted, $job);
        }
        exit;
    }
}
