<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Mon, 29 April 2013 20:26:33
 * ----------------------------------------------
 */
 
/* database settings */

$GB_DB['dbName'] = ''; // The name of your MySQL Database
$GB_DB['host']   = 'localhost'; // The server your database is on. localhost is usualy correct
$GB_DB['user']   = ''; // Your MySQL username
$GB_DB['pass']   = ''; // Your MySQL password

// You can change this if some of the database table names are already used
// or if you want to host more than one guestbook on one database
$table_prefix = 'book';

// Put your email address here to receive email reports of any errors
$TEC_MAIL  = 'you_at_your_domain_dot_com';

// If you are using the guestbook as a module in POST-Nuke 0.x or PHP-Nuke 5.x or later set this to true
define('IS_MODULE', false);

// 
// Do not edit below this line unless you know what you are doing
//

$DB_CLASS  = 'mysql.class.php'; // Change to mysqli.class.php to use mysqli_* functions instead of mysql_* functions

// Don't worry about there being no closing for PHP as it's not really needed and my leaving it out
// should prevent any whitespace issues after people have edited this file