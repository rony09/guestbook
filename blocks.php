<?php
/* 
   To use this file log in to admin and go to include code
   Grab the include code but change gbinclude.php to blocks.php
   Now place that code where you want the block count to be displayed.
*/
include './admin/config.inc.php';
include './admin/version.php';
include './lib/mysql.class.php';

define('LAZ_TABLE_PREFIX', $table_prefix);

$db = new gbook_sql();

$db->connect();

$stats = $db->fetch_array($db->query('SELECT block_count, offset FROM ' . LAZ_TABLE_PREFIX . '_config'));

date_default_timezone_set($stats['offset']);

$stats = @unserialize($stats['block_count']);

echo $stats[0] . ' spam entries blocked since ' . date('d/m/y', $stats[-1]);

/*
   To change how the date is displayed edit the date
   Go to http://php.net/date to see the various letters you can use for formatting
   
   To display the details of the blocking you can use $stats[x] changing x to a number from this list

   -1 - Timestamp of when spam block count was started/reset
    0  - Total count
    1  - Filled in the Honeypot
    2  - You have banned their IP
    3  - They didn't fill in the anti bot test
    4  - They got the anti bot test wrong
    5  - No timehash
    6  - They failed the header check
    7  - Post contained a blocked word
    8  - Post contained to many URLs
    9  - Stop Forum Spam thinks they are a spammer
    
    But I dont recommend displaying anyting other than the total count and the date
*/

$db->close_db();

?>