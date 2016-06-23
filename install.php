<?php 
/////////////////////////////////////////
// Lazarus Installer/Smartupdater v1   //
// Mon, 4 May 2015 14:50:38 GMT        //
/////////////////////////////////////////

$lazVersion = '1.25b1';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <title>Lazarus Guestbook Installer/Updater</title>
  <style type="text/css">
  <!--
  body { font: 14px Arial,Helvetica,sans-serif; text-align: center; background-color: #FFFFFF; }
  h1 { font-size: 20px; text-align: center; background-color: #F8F8F8; border: 1px solid #A2A2A2; color: #646464; padding: 5px; margin: 0; width: 760px; }
  input { border: 1px solid #A2A2A2; font-size: 12px; width: 300px; border-radius: 5px; padding: 2px; }
  textarea { border: 1px solid #A2A2A2; }
  #choices, #install, #update { border: 1px solid #A2A2A2; color: #646464; background: #F8F8F8; width: 760px; text-align: center; padding: 5px; margin: 5px 0 0 0; display: block; }
  #update p, #install p { margin: 10px; }
  #container { padding: 0; text-align: center; margin: 0 auto 0 auto; width: 760px; }
  #container a { text-decoration: none; font-weight: bold; }
  #container a:hover { text-decoration: underline; }
  #footer { text-align: center; background-color: #F8F8F8; border: 1px solid #A2A2A2; color: #646464; font-size: 10px; margin: 20px 0 0 0; padding: 0 5px; width: 760px; }
  -->
  </style>
  </head>
  <body><div id="container">
<h1>L A Z A R U S &nbsp; G U E S T B O O K</h1>

<?php
//
// lets show any errors we get in PHP
//

ini_set ('display_errors', 1);
error_reporting (E_ALL & ~E_NOTICE);

include './admin/config.inc.php';
include './admin/version.php';
include './lib/' . $DB_CLASS;

define('LAZ_TABLE_PREFIX', $table_prefix);

$db = new gbook_sql();

$db->connect();

define('IS_INSTALLER', true);

// Check if tables already exist
$dbcheck = @$db->fetch_array($db->query('SELECT username, session FROM '.LAZ_TABLE_PREFIX.'_auth'));

// Just a counter
$querycount = 0;
$needbaseurl = 0;
$needlazurl = 0;
$max_url_set = 1;

//
// Create our list of required fields and their types ready to go in a query
//

$book_auth = array(
  'ID'         => 'ADD ID smallint(5) NOT NULL auto_increment',
  'username'   => 'ADD username varchar(60) NOT NULL default ""',
  'password'   => 'ADD password varchar(60) NOT NULL default ""',
  'session'    => 'ADD session varchar(32) NOT NULL default ""',
  'last_visit' => 'ADD last_visit int(11) NOT NULL'
);

$book_ban = array(
  'ban_ip'    => 'ADD ban_ip varchar(32) NOT NULL default ""',
  'timestamp' => 'ADD timestamp int(11) NOT NULL default 0'
);

$book_com = array(
  'com_id'      => 'ADD com_id int(11) NOT NULL auto_increment',
  'id'          => 'ADD id int(11) NOT NULL default 0',
  'name'        => 'ADD name varchar(150) NOT NULL default ""',
  'email'       => 'ADD email varchar(150) NOT NULL default ""',
  'comments'    => 'ADD comments text NOT NULL',
  'host'        => 'ADD host varchar(255) NOT NULL default ""',
  'timestamp'   => 'ADD timestamp int(11) NOT NULL default 0',
  'comaccepted' => 'ADD comaccepted smallint(1) NOT NULL default 1',
  'ip'          => 'ADD ip varchar(16) NOT NULL default ""'
);

$book_config = array(
  'config_id'             => 'ADD config_id smallint(4) NOT NULL auto_increment',
  'agcode'                => 'ADD agcode smallint(1) NOT NULL default 0',
  'book_name'             => 'ADD book_name varchar(255) NOT NULL default "<h3>Guestbook</h3>"',
  'allow_html'            => 'ADD allow_html smallint(1) NOT NULL default 0',
  'offset'                => 'ADD offset varchar(40) NOT NULL default "UTC"',
  'smilies'               => 'ADD smilies smallint(1) NOT NULL default 1',
  'dformat'               => 'ADD dformat varchar(6) NOT NULL default ""',
  'tformat'               => 'ADD tformat varchar(4) NOT NULL default "24hr"',
  'admin_mail'            => 'ADD admin_mail varchar(200) NOT NULL default ""',
  'book_mail'             => 'ADD book_mail varchar(50) NOT NULL default ""',
  'always_bookemail'      => 'ADD always_bookemail smallint(1) NOT NULL default 0',
  'notify_private'        => 'ADD notify_private smallint(1) NOT NULL default 0',
  'notify_admin'          => 'ADD notify_admin smallint(1) NOT NULL default 0',
  'notify_guest'          => 'ADD notify_guest smallint(1) NOT NULL default 0',
  'notify_mes'            => 'ADD notify_mes text NOT NULL',
  'entries_per_page'      => 'ADD entries_per_page int(6) NOT NULL default 10',
  'show_ip'               => 'ADD show_ip smallint(1) NOT NULL default 0',
  'pbgcolor'              => 'ADD pbgcolor varchar(20) NOT NULL default "#FFFFFF"',
  'text_color'            => 'ADD text_color varchar(20) NOT NULL default "#000000"',
  'link_color'            => 'ADD link_color varchar(20) NOT NULL default "#006699"',
  'width'                 => 'ADD width varchar(4) NOT NULL default "95%"',
  'tb_font_1'             => 'ADD tb_font_1 varchar(7) NOT NULL default "11px"',
  'tb_font_2'             => 'ADD tb_font_2 varchar(7) NOT NULL default "10px"',
  'font_face'             => 'ADD font_face varchar(60) NOT NULL default "Verdana, Arial, Helvetica, sans-serif"',
  'tb_hdr_color'          => 'ADD tb_hdr_color varchar(20) NOT NULL default "#EAF3FA"',
  'tb_bg_color'           => 'ADD tb_bg_color varchar(20) NOT NULL default "#BBD8E7"',
  'tb_text'               => 'ADD tb_text varchar(20) NOT NULL default "#0A3E75"',
  'tb_color_1'            => 'ADD tb_color_1 varchar(20) NOT NULL default "#FFFFFF"',
  'tb_color_2'            => 'ADD tb_color_2 varchar(20) NOT NULL default "#EDFBFC"',
  'lang'                  => 'ADD lang varchar(30) NOT NULL default "english"',
  'min_text'              => 'ADD min_text smallint(4) NOT NULL default 6',
  'max_text'              => 'ADD max_text int(6) NOT NULL default 1500',
  'max_word_len'          => 'ADD max_word_len smallint(4) NOT NULL default 80',
  'comment_pass'          => 'ADD comment_pass varchar(50) NOT NULL default ""',
  'need_pass'             => 'ADD need_pass smallint(1) NOT NULL default 0',
  'censor'                => 'ADD censor smallint(1) NOT NULL default 3',
  'flood_check'           => 'ADD flood_check smallint(1) NOT NULL default 0',
  'banned_ip'             => 'ADD banned_ip smallint(1) NOT NULL default 0',
  'flood_timeout'         => 'ADD flood_timeout smallint(5) NOT NULL default 0',
  'allow_icq'             => 'ADD allow_icq smallint(1) NOT NULL default 0',
  'allow_aim'             => 'ADD allow_aim smallint(1) NOT NULL default 0',
  'allow_gender'          => 'ADD allow_gender smallint(1) NOT NULL default 0',
  'allow_img'             => 'ADD allow_img smallint(1) NOT NULL default 0',
  'max_img_size'          => 'ADD max_img_size int(10) NOT NULL default 0',
  'img_width'             => 'ADD img_width smallint(5) NOT NULL default 0',
  'img_height'            => 'ADD img_height smallint(5) NOT NULL default 0',
  'thumbnail'             => 'ADD thumbnail smallint(1) NOT NULL default 0',
  'thumb_min_fsize'       => 'ADD thumb_min_fsize int(10) NOT NULL default 0',
  'encrypt_email'         => 'ADD encrypt_email smallint(1) NOT NULL default 1',
  'allow_yahoo'           => 'ADD allow_yahoo smallint(1) NOT NULL default 0',
  'allow_skype'           => 'ADD allow_skype smallint(1) NOT NULL default 0',
  'allow_msn'             => 'ADD allow_msn smallint(1) NOT NULL default 0',
  'allow_private'         => 'ADD allow_private smallint(1) NOT NULL default 1',
  'allowed_tags'          => 'ADD allowed_tags varchar(60) NOT NULL default ""',
  'require_checking'      => 'ADD require_checking smallint(1) NOT NULL default 0',
  'require_email'         => 'ADD require_email smallint(1) NOT NULL default 3',
  'html_email'            => 'ADD html_email smallint(1) NOT NULL default 0',
  'require_comchecking'   => 'ADD require_comchecking smallint(1) NOT NULL default 0',
  'notify_admin_com'      => 'ADD notify_admin_com smallint(1) NOT NULL default 0',
  'allow_imgagcode'       => 'ADD allow_imgagcode smallint(1) NOT NULL default 0',
  'allow_emailagcode'     => 'ADD allow_emailagcode smallint(1) NOT NULL default 0',
  'allow_urlagcode'       => 'ADD allow_urlagcode smallint(1) NOT NULL default 0',
  'allow_flashagcode'     => 'ADD allow_flashagcode smallint(1) NOT NULL default 0',
  'allow_url'             => 'ADD allow_url smallint(1) NOT NULL default 1',
  'antibottest'           => 'ADD antibottest smallint(1) NOT NULL default 0',
  'bottestquestion'       => 'ADD bottestquestion varchar(50) NOT NULL default ""',
  'bottestanswer'         => 'ADD bottestanswer varchar(20) NOT NULL default ""',
  'use_regex'             => 'ADD use_regex smallint(1) NOT NULL default 0',
  'post_time_min'         => 'ADD post_time_min smallint(2) NOT NULL default 10',
  'post_time_max'         => 'ADD post_time_max smallint(5) NOT NULL default 300',
  'hide_comments'         => 'ADD hide_comments smallint(1) NOT NULL default 1',
  'allow_loc'             => 'ADD allow_loc smallint(1) NOT NULL default 1',
  'com_question'          => 'ADD com_question varchar(50) NOT NULL default ""',
  'antispam_word'         => 'ADD antispam_word varchar(10) NOT NULL default "'.substr(md5(time()), 0, 5).'"',
  'charset'               => 'ADD charset varchar(20) NOT NULL default "utf-8"',
  'captcha_noise'         => 'ADD captcha_noise smallint(1) NOT NULL default 1',
  'captcha_grid'          => 'ADD captcha_grid smallint(1) NOT NULL default 1',
  'captcha_grey'          => 'ADD captcha_grey smallint(1) NOT NULL default 1',
  'captcha_greytext'      => 'ADD captcha_greytext smallint(1) NOT NULL default 1',
  'ad_pos'                => 'ADD ad_pos varchar(5) NOT NULL default 0',
  'ad_code'               => 'ADD ad_code text NOT NULL',
  'agcode_img_width'      => 'ADD agcode_img_width smallint(5) NOT NULL default 300',
  'agcode_img_height'     => 'ADD agcode_img_height smallint(5) NOT NULL default 100',
  'disablecomments'       => 'ADD disablecomments smallint(1) NOT NULL default 0',
  'base_url'              => 'ADD base_url varchar(200) NOT NULL default ""',
  'allow_search'          => 'ADD allow_search smallint(1) NOT NULL default 0',
  'laz_version'           => 'ADD laz_version varchar(8) NOT NULL default ""',
  'max_url'               => 'ADD max_url int(2) NOT NULL default 99',
  'smarttime'             => 'ADD smarttime smallint(1) NOT NULL default 1',
  'search_bg_color'       => 'ADD search_bg_color varchar(20) NOT NULL default "#FFFFFF"',
  'search_font_color'     => 'ADD search_font_color varchar(20) NOT NULL default "#000000"',
  'always_flash'          => 'ADD always_flash smallint(1) NOT NULL default 0',
  'permalinks'            => 'ADD permalinks smallint(1) NOT NULL default 0',
  'captcha_width'         => 'ADD captcha_width int(3) NOT NULL default 350',
  'captcha_height'        => 'ADD captcha_height int(3) NOT NULL default 100',
  'captcha_trans'         => 'ADD captcha_trans smallint(1) NOT NULL default 1',
  'external_css'          => 'ADD external_css smallint(1) NOT NULL default 0',
  'laz_url'               => 'ADD laz_url varchar(200) NOT NULL default ""', 
  'use_gravatar'          => 'ADD use_gravatar smallint(1) NOT NULL default 1',
  'check_headers'         => 'ADD check_headers smallint(1) NOT NULL default 1',
  'errorbox_border_color' => 'ADD errorbox_border_color varchar(20) NOT NULL default "#DD0000"',
  'errorbox_border_width' => 'ADD errorbox_border_width smallint(1) NOT NULL default 2',
  'errorbox_border_style' => 'ADD errorbox_border_style varchar(10) NOT NULL default "dashed"',
  'errorbox_font_color'   => 'ADD errorbox_font_color varchar(20) NOT NULL default "#000000"',
  'errorbox_back_color'   => 'ADD errorbox_back_color varchar(20) NOT NULL default "#FFFFFF"',
  'input_error_color'     => 'ADD input_error_color varchar(20) NOT NULL default "#FFC0CB"',
  'laz_top_font_color'    => 'ADD laz_top_font_color varchar(20) NOT NULL default "#000000"',
  'laz_top_num_color'     => 'ADD laz_top_num_color varchar(20) NOT NULL default "#DD0000"',
  'top_link_color'        => 'ADD top_link_color varchar(20) NOT NULL default "#006699"',
  'block_count'           => 'ADD block_count varchar(255) NOT NULL default "a:11:{i:-1;i:' . time() . ';i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;}"',
  'smtp_server'           => 'ADD smtp_server varchar(100) NOT NULL default ""',
  'smtp_port'             => 'ADD smtp_port int(5) NOT NULL default 25',
  'smtp_username'         => 'ADD smtp_username varchar(100) NOT NULL default ""',
  'smtp_password'         => 'ADD smtp_password varchar(100) NOT NULL default ""',
  'count_blocks'          => 'ADD count_blocks smallint(1) NOT NULL default 1',
  'mail_type'             => 'ADD mail_type smallint(1) NOT NULL default 1',
  'mailSSL'               => 'ADD mailSSL smallint(1) NOT NULL default 0',
  'solve_media'           => 'ADD solve_media smallint(1) NOT NULL default 0',
  'sfs_confidence'        => 'ADD sfs_confidence smallint(3) NOT NULL default 50',
  'honeypot'              => 'ADD honeypot smallint(1) NOT NULL default 1',
  'included'              => 'ADD included smallint(1) NOT NULL default 0'
);

$book_data = array(
  'id'       => 'ADD id int(11) NOT NULL auto_increment',
  'name'     => 'ADD name varchar(150) NOT NULL default ""',
  'gender'   => 'ADD gender char(1) NOT NULL default ""',
  'email'    => 'ADD email varchar(150) NOT NULL default ""',
  'url'      => 'ADD url varchar(150) NOT NULL default ""',
  'date'     => 'ADD date int(11) NOT NULL default 0',
  'location' => 'ADD location varchar(50) NOT NULL default ""',
  'host'     => 'ADD host varchar(255) NOT NULL default ""',
  'browser'  => 'ADD browser varchar(255) NOT NULL default ""',
  'comment'  => 'ADD comment text NOT NULL',
  'icq'      => 'ADD icq int(11) NOT NULL default 0',
  'aim'      => 'ADD aim varchar(70) NOT NULL default ""',
  'msn'      => 'ADD msn varchar(70) NOT NULL default ""',
  'yahoo'    => 'ADD yahoo varchar(40) NOT NULL default ""',
  'skype'    => 'ADD skype varchar(40) NOT NULL default ""',
  'accepted' => 'ADD accepted smallint(1) NOT NULL default 1',
  'ip'       => 'ADD ip varchar(16) NOT NULL default ""'
);

$book_private = array(
  'id'       => 'ADD id int(11) NOT NULL auto_increment',
  'name'     => 'ADD name varchar(150) NOT NULL default ""',
  'gender'   => 'ADD gender char(1) NOT NULL default ""',
  'email'    => 'ADD email varchar(150) NOT NULL default ""',
  'url'      => 'ADD url varchar(150) NOT NULL default ""',
  'date'     => 'ADD date int(11) NOT NULL default 0',
  'location' => 'ADD location varchar(50) NOT NULL default ""',
  'host'     => 'ADD host varchar(255) NOT NULL default ""',
  'browser'  => 'ADD browser varchar(255) NOT NULL default ""',
  'comment'  => 'ADD comment text NOT NULL',
  'icq'      => 'ADD icq int(11) NOT NULL default 0',
  'aim'      => 'ADD aim varchar(70) NOT NULL default ""',
  'msn'      => 'ADD msn varchar(70) NOT NULL default ""',
  'yahoo'    => 'ADD yahoo varchar(40) NOT NULL default ""',
  'skype'    => 'ADD skype varchar(40) NOT NULL default ""',
  'accepted' => 'ADD accepted smallint(1) NOT NULL default 1',
  'ip'       => 'ADD ip varchar(16) NOT NULL default ""'
);

$book_ip = array(
  'guest_ip'  => 'ADD guest_ip varchar(15) NOT NULL default ""',
  'timestamp' => 'ADD timestamp int(11) NOT NULL default 0'
);

$book_pics = array(
  'msg_id'     => 'ADD msg_id int(11) NOT NULL default 0',
  'book_id'    => 'ADD book_id int(11) NOT NULL default 0',
  'p_filename' => 'ADD p_filename varchar(100) NOT NULL default ""',
  'p_size'     => 'ADD p_size int(11) unsigned NOT NULL default 0',
  'width'      => 'ADD width int(11) unsigned NOT NULL default 0',
  'height'     => 'ADD height int(11) unsigned NOT NULL default 0'
);

$book_smile = array(
  'id'         => 'ADD id int(11) NOT NULL auto_increment',
  's_code'     => 'ADD s_code varchar(20) NOT NULL default ""',
  's_filename' => 'ADD s_filename varchar(60) NOT NULL default ""',
  's_emotion'  => 'ADD s_emotion varchar(60) NOT NULL default ""',
  'width'      => 'ADD width smallint(6) unsigned NOT NULL default 0',
  'height'     => 'ADD height smallint(6) unsigned NOT NULL default 0'
);

$book_words = array(
  'word' => 'ADD word varchar(30) NOT NULL default ""',
  'type' => 'ADD type smallint(1) NOT NULL default 1'
);

// 
// Lets attach the real name of the tables to the arrays
//

$dbtables = array(
  LAZ_TABLE_PREFIX.'_auth'    => $book_auth, 
  LAZ_TABLE_PREFIX.'_ban'     => $book_ban, 
  LAZ_TABLE_PREFIX.'_com'     => $book_com, 
  LAZ_TABLE_PREFIX.'_config'  => $book_config, 
  LAZ_TABLE_PREFIX.'_data'    => $book_data, 
  LAZ_TABLE_PREFIX.'_private' => $book_private, 
  LAZ_TABLE_PREFIX.'_ip'      => $book_ip, 
  LAZ_TABLE_PREFIX.'_pics'    => $book_pics, 
  LAZ_TABLE_PREFIX.'_smilies' => $book_smile, 
  LAZ_TABLE_PREFIX.'_words'   => $book_words
);

//
// Now we create the functions we will need
//

// My function to retrieve the list of fields in a table
function fetch_fields($table = '')
{
  global $book_config, $db;
  if ($table == '')
  {
    die ('No table has been specified');
  }
  
  $sql = 'SHOW COLUMNS FROM ' . $table; // Our query to retrieve the field data
  $result = $db->query($sql);
  $fieldarray = array();  // Just initialise the array
  $thekey = ''; // and the variable for the field name
  
  for ($i = 0; $i < $db->num_rows($result); $i++)
  {
     $row_array = $db->fetch_row($result);
     
     if ($row_array[0] == 'LAST_VISIT')
     {
       $row_array[0] = 'last_visit';
     }
     
     $fieldarray[$row_array[0]] = $row_array[1];
  }
  return $fieldarray;
}

// Function to compare the existing fields with those we want to add
function check_fields($tablearray = '', $table = '')
{
  global $querycount, $max_url_set;
  $querycount = count($table);
  if ($table == '') // $table is the table we are checking against such as book_auth
  {
    die ('No table has been specified');
  }
    
  if (!is_array($tablearray))
  {
    die ('It is not an array');
  }
    
  // $tablearray is the array returned by the fetch_fields function  
  foreach ($tablearray as $fieldname => $fieldtype)
  {
    if(isset($table[$fieldname]))
    {
      $queryfieldtype = explode(' ' , $table[$fieldname]); // All we want is the fieldtype so extract it
      if($queryfieldtype[2] == $fieldtype || $queryfieldtype[2] . ' unsigned' == $fieldtype)
      {
        if ($fieldname == 'max_url') 
        {
          $max_url_set = 0;
        }
        unset($table[$fieldname]);
        $querycount--;
      }
      else
      {
        // If the field exists but is not the correct type we need to change it not add it
        $table[$fieldname] = str_replace('ADD ', 'CHANGE ' . $fieldname . ' ', $table[$fieldname]);
      }
    }
  }
  return $table;
}

function check_index($table = '')
{
  global $querycount, $dbc;
  if ($table == '') // $table is the table we are checking against such as book_auth
  {
    die ('No table has been specified');
  }
  $result = $db->query('SHOW INDEX FROM '.$table);
  $indexquery = 'ADD INDEX (`id`)';
  while ($row = $db->fetch_row($result))
  {
    $indexquery = ($row[4] == 'id') ? '' : $indexquery;
  }
  if ($indexquery != '')
  {
    $querycount++;
  }
  return $indexquery;
}

$domain = '';
$directory = '';
if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']))
{
  $domain = $_SERVER['HTTP_HOST'];
}
elseif (isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME']))
{
  $domain = $_SERVER['SERVER_NAME'];
}

if (isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF']))
{
  $directory = $_SERVER['PHP_SELF'];
}
elseif (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']))
{
  $directory = $_SERVER['REQUEST_URI'];
} 
$baseurl = 'http://' . $domain . dirname($directory);

if (empty($_GET['action']))
{

  if(empty($dbcheck))
  {
?>
    <div id="install">
      <strong>LAZARUS INSTALLER</strong>
      <form action="?action=install" method="post">
      <p>This will create the necessary tables in your database for Lazarus to work.</p>
      <p><b>Your base url:</b><br />
      <input type="text" name="base_url" size="30" value="<?php echo $baseurl; ?>" /></p>
      <p><b>Your guestbook url:</b><br />
      <input type="text" name="laz_url" size="30" value="<?php echo $baseurl; ?>" /></p>
      <p>The <strong>base url</strong> is the web address of your Lazarus folder without the trailing slash (eg. http://yoursite.com/lazarusgb). This script has guessed what this may be and filled it in but you may need to correct this. If you are using Lazarus as a Nuke module the base url needs to be set to the address of your Nuke folder and not the address of the Lazarus folder.</p>
      <p>The <strong>guestbook url</strong> is the web address people use to visit your guestbook. If you are using the guestbook as it is then this will match the base url above. If you are using the guestbook as a module or included into your website then set this to the correct web address.</p>
      <p><b>Set username for admin:</b><br />
      <input type="text" name="username" size="30" /><br />
      <b>Set password for admin:</b><br />
      <input type="password" name="password" size="30" /></p>
      <input type="submit" value="RUN INSTALLER" />
      </form>
    </div>
<?php
  }
  else
  {
?>
    <div id="update">
      <strong>LAZARUS SMARTUPDATER</strong>
      <form action="" method="get">
      <p>The smartupdater allows you to update the database of any version of Lazarus or Advanced Guestbook (2.3 and over) to that used by the current version of Lazarus.</p>
      <p>It does this by comparing your existing database tables with those used by Lazarus and then making the changes where necessary.</p>
      <p>Once it has finished making any changes required it will optimise the guestbooks tables which can help improve performance.</p>
      <p><strong>Please note that if you have a database with a lot of entries it may take a while for the script to make any changes so be patient if nothing happens immediately.</strong></p>
      <input type="hidden" name="action" value="update" />
      <input type="submit" value="RUN SMARTUPDATER" />
      </form>
    </div>
<?php
  } 
}
elseif (($_GET['action'] == 'install') && empty($dbcheck))
{
  echo '<table border="0" align="center">';
  foreach ($dbtables as $table => $fields) 
  {
    $dbtables[$table] = str_replace('ADD ', '', $dbtables[$table]);
    switch ($table) 
    {
    case LAZ_TABLE_PREFIX.'_auth':
      $dbtables[$table][] = 'PRIMARY KEY (ID)';
      break; 
    case LAZ_TABLE_PREFIX.'_com':
      $dbtables[$table][] = 'INDEX (id)';
      $dbtables[$table][] = 'PRIMARY KEY (com_id)';
      break;
    case LAZ_TABLE_PREFIX.'_config':
      $dbtables[$table][] = 'PRIMARY KEY (config_id)';
      break; 
    case LAZ_TABLE_PREFIX.'_data':
      $dbtables[$table][] = 'PRIMARY KEY (id)';
      break; 
    case LAZ_TABLE_PREFIX.'_private':
      $dbtables[$table][] = 'PRIMARY KEY (id)';
      break;       
    case LAZ_TABLE_PREFIX.'_ip':
      $dbtables[$table][] = 'KEY guest_ip (guest_ip)';
      break;
    case LAZ_TABLE_PREFIX.'_pics':
      $dbtables[$table][] = 'KEY msg_id (msg_id)';
      $dbtables[$table][] = 'KEY book_id (book_id)';
      break;       
    case LAZ_TABLE_PREFIX.'_smilies':
      $dbtables[$table][] = 'PRIMARY KEY ip (id)';
      break;                              
    default:
    	
    	break;
    }
    
    $dbtables[$table] = 'CREATE TABLE '.$table.' ('.implode(",\n ", $dbtables[$table]).')';
    echo "<tr>\n<td style=\"text-align:left\">Creating table ".$table."</td>\n";
    if ($db->query($dbtables[$table]))
	  {
      echo '<td style="color:#008000;font-weight:bold;padding-left:10px;">DONE</td>';
    }
    else
    {
      echo '<td style="color:#AA0000;font-weight:bold;padding-left:10px;">ERROR!</td>';
      echo "\n</tr>\n<tr>\n<td colspan=\"3\" style=\"border: 1px dashed #A00; background: #FFC0CB;\"><pre style=\"text-align:left;\">Error: ".mysql_error()."\nQuery: ".$dbtables[$table].'</pre></td>';
    }
    echo "</tr>\n";
  }
  echo "</table>\n<p>Populating Tables</p>\n<table border=\"0\" align=\"center\">\n";
  $username = (!empty($_POST['username'])) ? $_POST['username'] : 'test';
  $password = (!empty($_POST['password'])) ? $_POST['password'] : '123';
  $base_url = (!empty($_POST['base_url'])) ? $_POST['base_url'] : $baseurl;
  $laz_url  = (!empty($_POST['laz_url']))  ? $_POST['laz_url']  : $baseurl;
  
  if (!get_magic_quotes_gpc())
  {
    addslashes($username);
    addslashes($password);
    addslashes($base_url);
    addslashes($laz_url);
  }
  
//
// Create our queries for insertion
//

    $use_captcha = (extension_loaded('gd')) ? 2 : 1;
    $user_id = mt_rand(1,999);
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_auth VALUES (".$user_id.",'".$username."',PASSWORD('".$password."'),'cd2c6d5e457641991d52da8fb6d87c08',1013100791)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_ban VALUES ('123.123.123.123', 0)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_config VALUES (1, 1, 'Guestbook', 1, 'UTC', 1, 'Euro', '24hr', 'root@localhost', '', 0, 0, 0, 0, 'Thank you for signing the guestbook.', 10, 1, '#FFFFFF', '#000000', '#006699', '95%', '11px', '10px', 'Verdana, Arial, Helvetica, sans-serif', '#EAF3FA', '#BBD8E7', '#0A3E75', '#FFFFFF', '#EDFBFC', 'english', 6, 1500, 80, 'comment', ".$use_captcha.", 1, 1, 1, 300, 1, 1, 1, 0, 120, 60, 60, 1, 12, 1, 1, 1, 1, 1, 'b,i,u', 0, 3, 0, 0, 0, 1, 1, 1, 1, 1, ".$use_captcha.", 'What colour is the sky?', 'blue', 0, 10, 300, 1, 1, '', '".substr(md5(time()), 0, 5)."', 'utf-8', 1, 1, 1, 1, 0, '', 300, 100, 0, '".$base_url."', 0, '".$lazVersion."', 99, 1, '#FFFFFF', '#000000', 0, 0, 400, 100, 1, 0, '".$laz_url."', 1, 1, '#DD0000', 2, 'dashed', '#000000', '#FFFFFF', '#FFC0CB', '#000000', '#DD0000', '#006699', 'a:11:{i:-1;i:" . time() . ";i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;}', '', 25, '', '', 1, 1, 0, 0, 50, 1, 0)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_words VALUES ('fuck', 1)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (1, ':-)', 'a1.gif', 'smile', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (2, ':-(', 'a2.gif', 'frown', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (3, ';-)', 'a3.gif', 'wink', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (4, ':o', 'a4.gif', 'embarrassment', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (5, ':D', 'a5.gif', 'big grin', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (6, ':p', 'a6.gif', 'razz (stick out tongue)', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (7, ':cool:', 'a7.gif', 'cool', 21, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (8, ':rolleyes:', 'a8.gif', 'roll eyes (sarcastic)', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (9, ':mad:', 'a9.gif', '#@*%!', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (10, ':eek:', 'a10.gif', 'eek!', 15, 15)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (11, ':confused:', 'a11.gif', 'confused', 15, 22)";
    $tbl_data[] = "INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (id, s_code, s_filename, s_emotion, width, height) VALUES (12, ':lol:', 'lol.gif', 'lol', 15, 15)";  
//
// Ok now we've made our queries
// Lets create the tables
//
  $errors = '';
  $success_count = 0;
  for ($i=0;$i<sizeof($tbl_data);$i++) 
  {
    if ($db->query($tbl_data[$i]))
    {
      $success_count++;
    }
    else
    {
      $errors .= 'Error: '.mysql_error()."\nQuery: ".$query."\n\n";
    }
  }
  if ($errors != '')
  {
    echo ('The installer has encountered some errors whilst trying to populate the tables.<br>
        The errors and the queries that generated them are listed below<br>
        <textarea name="errors" cols="68" rows="11">'.$errors.'</textarea>
        <h3 style="color:rgb(255,0,0); border: 3px dotted rgb(255,0,0);">THIS FILE SHOULD BE DELETED FOR SECURITY REASONS</h3>');    
  }
  else
  {
?>
<p style="width:100%;text-align: center;">Lazarus has been successsfully installed.</p>
<p>Login to the <a href="admin.php">ADMIN</a> section. If you did not specify a username and password on the previous page 
they will of been set to the defaults of <b>test</b> and <b>123</b></p>
<h3 style="color:rgb(255,0,0); border: 3px dotted rgb(255,0,0); text-align: center;">NOW DELETE THIS FILE FOR SECURITY REASONS</h3> 
<?php 
  }
}
elseif (($_GET['action'] == 'install') && !empty($dbcheck))
{
  echo '<h2>Errrrr what ya think ya doing?</h2>';
}
elseif ($_GET['action'] == 'update')
{
  $thetables = ''; // This is just used to make a list of the real table names for the optimising

  echo '<table border="0" align="center">';

//
// Lets run through the tables to see whats what
//

  foreach ($dbtables as $tablename => $tablefields) 
  {
    $thetables[] = '`'.$tablename.'`';
    echo "<tr>\n<td style=\"text-align:left\">Checking ".$tablename."</td>\n";
  	$existingfields = fetch_fields($tablename); // get a list of the fields that already exist in the database.
  	$queryfields = check_fields($existingfields, $tablefields); // Remove any existing fields from our query and check that the others are the right type

//
// Check if we have already set the index for the comments table and if not then set it
//

    if ($tablename == $LAZ_TABLE_PREFIX.'_com')
    {
      $needsindex = check_index(LAZ_TABLE_PREFIX.'_com');
      if ($needsindex != '')
      {
        $queryfields[] = $needsindex;
      }
    }
	  
//
// Lets report what we are about to do
//
	  
    echo '<td style="font-weight:bold;padding-left:10px;">'.$querycount.' ';
    echo ($querycount == 1) ? 'change': 'changes';
    echo " required </td>\n";
    if (!empty($queryfields))
    {
      echo 'updating ... ';
      if (isset($queryfields['base_url']))
      {
        $needbaseurl = 1;
      }
      if (isset($queryfields['laz_url']))
      {
        $needlazurl = 1;
      }      
      $thequery = implode(', ', $queryfields);     
      $thequery = 'ALTER TABLE '. $tablename . ' ' . $thequery;
      if ($db->query($thequery))
      {
        echo '<td style="color:#008000;font-weight:bold;padding-left:10px;">DONE</td>';
      }
      else
      {
        echo '<td style="color:#AA0000;font-weight:bold;padding-left:10px;">ERROR!</td>';
        echo "\n</tr>\n<tr>\n<td colspan=\"3\" style=\"border: 1px dashed #A00; background: #FFC0CB;\"><pre style=\"text-align:left;\">Error: ".mysql_error()."\nQuery: ".$thequery.'</pre></td>';
      }
    }
    else
    {
      echo '<td style="color:#008000;font-weight:bold;padding-left:10px;">skipping</td>';
    }
    echo "\n</tr>\n";
    $querycount = 0;
  }
  echo "</table>\n<br>\n<br>\n";

//
// If we had to add the base_url field then we need to also set it
//  

  if ($needbaseurl)
  {
    if($GB_PG['base_url'] != '') // Have the supplied a base_url in the config.inc.php file?
    { 
      $query = 'UPDATE '.LAZ_TABLE_PREFIX.'_config set base_url="'.$GB_PG['base_url'].'" WHERE (config_id = 1)';
      $db->query($query);
      echo '<p>The base url is now located in General Settings. It has automatically been set to the base_url you have in your config.inc.php file.</p>';
    }
    else // If not then use the url of this script to guess the base url.
    { 
      $query = 'UPDATE '.LAZ_TABLE_PREFIX.'_config set base_url="'.$baseurl.'" WHERE (config_id = 1)';
      $db->query($query);
      echo '<p>The base url is now located in General Settings. Since you had not specified a base_url in the config.inc.php file this script has guessed it based on the url of this script.<br />
      It is important that you log in and check that it is set right otherwise images and links may not work.</p>';
    }
  }
  
//
// If we had to add the laz_url field then we need to also set it
//  

  if ($needlazurl)
  {
    echo '<p>I have added a new setting called Guestbook URL. You will find it in the admin and it needs to be set to ensure Lazarus works correctly.</p>';
  }  
  
//
// If just set max_url tell them to set it
//

  if ($max_url_set == 1)
  {
    echo '<p><strong>You will need to login to the admin section then goto General Settings and set the new feature <em>Max Url</em>.</strong></p>';
  } 
  
//
// Update the version number regardless
//
  
  echo '<div style="text-align:center;margin-bottom:30px;">Setting version number<br />';
  if ($db->query('UPDATE '.LAZ_TABLE_PREFIX.'_config set laz_version="'.$lazVersion.'" WHERE (config_id = 1)'))
  {
    echo '<span style="color:#008000;font-weight:bold;">Version number set to '.$lazVersion.'</span>';
  }
  else
  {
    echo '<span style="color:#AA0000;font-weight:bold;">ERROR!</span><br><pre>Error: '.mysql_error().'</pre>';
  }
  echo '</div>';
    
  
//
// Optimise our tables and report on the outcome
//  

  $thetables = implode(', ', $thetables);
  echo '<div style="text-align:center;margin-bottom:30px;">Now optimising the tables to improve performance<br />';
  if ($db->query('OPTIMIZE TABLE ' . $thetables))
  {
    echo '<span style="color:#008000;font-weight:bold;">TABLES OPTIMISED SUCCESSFULLY</span>';
  }
  else
  {
    echo '<span style="color:#AA0000;font-weight:bold;">ERROR!</span><br><pre>Error: '.mysql_error().'</pre>';
  } 
  echo '</div>';
  
//
// We have finished updating so lets report this fact
//  

  echo '<h4 style="margin:0;">Thats all folks!</h4>
  <small>(The updater has finished)</small>
  <h3 style="color:rgb(255,0,0); border: 3px dotted rgb(255,0,0);">NOW DELETE THIS FILE FOR SECURITY REASONS</h3>';
}

//
// And thats the end of our PHP
//

?>
<br clear="both" />
  <div id="footer">
    Powered by sweat and tears
  </div>
</div>
  </body>
</html>