<?php
if (!isset($_SESSION)) {
    session_start();
}
/******************************************************************************************************************\
 *File:    index.php                                                                                               *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofing Biz Backend                                                            *
 *Date:    August 8th, 2019                                                                                        *
 *Purpose: This is where all files will be loaded before the routing (request.php) file is loaded                  *
\******************************************************************************************************************/

//set server locale
date_default_timezone_set('America/Chicago');
//load the autoloader from Composer
require_once('vendor/autoload.php');
//load HTTP header values
require_once('Models/HTTPHeaders.php');

//PHPMailer env var constants
define('PHPMAILER_EMAIL', getenv('phpmailer_email'));
define('PHPMAILER_APP_PASS', getenv('phpmailer_app_pass'));

//load PHPMailer init file
require_once('Models/PHPMailerInit.php');
//load Authentication model
require_once('Models/Authentication.php');
//load Registration model
require_once('Models/Registration.php');
//load ContactMessage model
require_once('Models/ContactMessage.php');
//load Message model
require_once('Models/Message.php');
//load Jobs model
require_once('Models/Jobs.php');
//load Account model
require_once('Models/Account.php');

//DB env var constants
define('DB_DSN', getenv('db_dsn'));
define('DB_UN', getenv('db_un'));
define('DB_UP', getenv('db_up'));

//load the DB files
require_once('DB/database.php');
require_once('DB/User.php');
require_once('DB/UserDB.php');
require_once('DB/MessageDB.php');
require_once('DB/JobsDB.php');

//load helper functions
require_once('Models/Validation.php');
require_once('Models/Confirmation.php');

//load request routing file
require_once('request.php');
