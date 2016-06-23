<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Thu, 26 March 2015 14:25:49 GMT
 * ----------------------------------------------
 */
ob_start();

// First I'm going to delete all existing variables except those we may need
$keep = array(
  'GLOBALS', 
  '_SERVER', 
  '_GET', 
  '_POST', 
  '_COOKIE', 
  'keep', 
  'key', 
  'val'
);

foreach ($GLOBALS as $key => $val) 
{
  if (!in_array($key,$keep))
  {
    unset($$key);
  }
}

// Not necessary just felt like it
unset($keep);

// This function loops through an array to check if that variable has been supplied via GET or POST
// If it has it willl convert it in to a variable for our use
function create_variable($vars, $method = 'post')
{
  foreach($vars as $Var)
  {
    global $$Var;
    if($method == 'post')
    {
      if(isset($_POST[$Var])) 
      {
        $$Var = $_POST[$Var];
      }
    }
    else
    {
      if(isset($_GET[$Var])) 
      {
        $$Var = $_GET[$Var];
      }
    }
  }
}

// This is an array of all variables we can expect in the URL
// These may be required outside of being logged in from email links
$getVars = array(
  'action', 
  'tbl', 
  'gbsession', 
  'uid', 
  'panel', 
  'section', 
  'ip', 
  'rid', 
  'id', 
  'resetBlock', 
  'entry'
);
create_variable($getVars, 'get');
 
if (isset($_GET['include_path']) || isset($_POST['include_path']))
{
  die("Hacking Attempt!");
}

define('LAZ_INCLUDE_PATH', dirname(__FILE__));
global $GB_DB, $GB_PG;
require_once LAZ_INCLUDE_PATH.'/admin/version.php';
require_once LAZ_INCLUDE_PATH.'/admin/config.inc.php';
require_once LAZ_INCLUDE_PATH.'/lib/' . $DB_CLASS;
require_once LAZ_INCLUDE_PATH.'/lib/image.class.php';
require_once LAZ_INCLUDE_PATH.'/lib/template.class.php';
require_once LAZ_INCLUDE_PATH.'/lib/session.class.php';
require_once LAZ_INCLUDE_PATH.'/lib/admin.class.php';

define('LAZ_TABLE_PREFIX', $table_prefix);

// This is an array of all post variables we can expect in the login or session check
$postVars = array(
  'username', 
  'password', 
  'remember', 
  'gbsession', 
  'uid'
);
create_variable($postVars);  

$gb_auth = new gb_session(LAZ_INCLUDE_PATH);
$AUTH = $gb_auth->checkSessionID();
$VARS = $gb_auth->fetch_array($gb_auth->query('SELECT * FROM '.LAZ_TABLE_PREFIX.'_config'));
date_default_timezone_set($VARS['offset']);
$GB_PG['base_url'] = $VARS['base_url'];
$GB_PG['index']    = $VARS['base_url'].'/index.php';
$GB_PG['admin']    = $GB_PG['base_url'].'/admin.php';
$GB_PG['comment']  = $GB_PG['base_url'].'/comment.php';
$GB_PG['addentry'] = $GB_PG['base_url'].'/addentry.php';
$gb_auth->free_result($gb_auth->result);
$template = new gb_template(LAZ_INCLUDE_PATH);

if (isset($_COOKIE['lang']) && !empty($_COOKIE['lang']))
{
  $template->set_lang($_COOKIE['lang']);
}
else
{
  $template->set_lang($VARS['lang']);
}
$LANG = $template->get_content();

if (!$AUTH)
{
  define('IS_INCLUDE', false);
  $message = (isset($username) || isset($password)) ? '<div style="border: 1px solid #D00; width: 300px; background: #FFC0CB; margin: 0 auto 10px auto; padding: 3px 0;" id="admin_error">' . $LANG['PassMess2'] . '</div>': '';
  $adminVariables = '';
  if(!empty($action) && !empty($tbl) && !empty($id))
  {
    $adminVariables = htmlspecialchars('?action='.$action.'&tbl='.$tbl.'&id='.intval($id));
  }
  $EMAILJS = '';
  $enter_html = '';
  eval("\$enter_html  = \"".$template->get_template('header')."\";");
  eval("\$enter_html .= \"".$template->get_template('admin_enter')."\";");
  eval("\$enter_html .= \"".$template->get_template('footer')."\";");
  echo $enter_html;
}
else
{
  // This is an array of some generic variables common to most forms
  $postVars = array(
    'gbsession', 
    'uid', 
    'action', 
    'panel', 
    'section', 
    'tbl'
  );
  create_variable($postVars);
  
  $action = (!isset($action)) ? '' : $action;
  $rid    = (!isset($rid)) ? '' : $rid;
  $admin  = new gb_admin($AUTH['session'],$AUTH['uid']);
  $admin->VARS =& $VARS;
  $admin->db =& $gb_auth;

  switch ($action)
  {

   case 'accept':
     $admin->accept_entry($id,$tbl);
     $admin->show_entry($tbl,$rid,$LANG['AdminTopMessAcc']);
     break;
    
   case 'unaccept':
     $admin->unaccept_entry($id,$tbl);
     $admin->show_entry($tbl,$rid,$LANG['AdminTopMessUnacc']);
     break;    

   case 'show':
     $postVars = array(
       'record', 
       'table'
     );
     create_variable($postVars);   
     $admin->show_entry($tbl,$rid);
     break;

   case 'del':
     $admin->del_entry($id,$tbl);
     $topmessage = ($tbl == 'pubpics') ? $LANG['AdminTopImageDel'] : $LANG['AdminTopMessDel'];
     $admin->show_entry($tbl,$rid,$topmessage);
     break;

   case 'edit':
     $admin->show_form($id,$tbl,$rid);
     break;

   case 'info':
     $admin->show_panel("info");
     break;
    
   case 'multi':
     $postVars = array(
       'tbl', 
       'rid', 
       'multidelete', 
       'multiaccept', 
       'multiunaccept', 
       'entryarray', 
       'comarray'
     );
     create_variable($postVars);
     $multimess = '';
     if (isset($comarray))
     {
       foreach ($comarray as $key => $value) 
       {
         if (isset($multidelete))
         {
           $admin->del_entry($key,'com');
           $multimess = $LANG['AdminTopMesssDel'];
         }
         elseif (isset($multiaccept))
         {
           $admin->accept_entry($key,'com');
           $multimess = $LANG['AdminTopMesssAcc'];
         }
         elseif (isset($multiunaccept))
         {
           $admin->unaccept_entry($key,'com');
           $multimess = $LANG['AdminTopMesssUnacc'];
         }
       }
     }
     if (isset($entryarray))
     {
       foreach ($entryarray as $key => $value) 
       {
         if (isset($multidelete))
         {
           $admin->del_entry($key,$tbl);
           $multimess = $LANG['AdminTopMesssDel'];
         } 
         elseif (isset($multiaccept))
         {
           $admin->accept_entry($key,$tbl);
           $multimess = $LANG['AdminTopMesssAcc'];
         }
         elseif (isset($multiunaccept))
         {
           $admin->unaccept_entry($key,$tbl);
           $multimess = $LANG['AdminTopMesssUnacc'];
         }
       }
     }
     $admin->show_entry($tbl,$rid,$multimess);
     break;

   case 'smilies':
     $getVars = array(
       'del_smilie', 
       'edit_smilie', 
       'scan_dir'
     );
     create_variable($getVars, 'get');
     
     $postVars = array(
       's_code', 
       's_emotion', 
       'edit_smilie', 
       'add_smilies', 
       'new_smilie', 
       'new_emotion'
     );
     create_variable($postVars);
     
     if (isset($scan_dir))
     {
       $smilie_list = $admin->scan_smilie_dir();
     }
     if (isset($del_smilie))
     {
       $gb_auth->query("DELETE FROM ".LAZ_TABLE_PREFIX."_smilies WHERE id='" . intval($del_smilie) . "'");
     }
     if (isset($edit_smilie))
     {
       $edit_smilie = intval($edit_smilie);
       if (isset($s_code) && isset($s_emotion))
       {
         $gb_auth->query("UPDATE ".LAZ_TABLE_PREFIX."_smilies SET s_code='" . $gb_auth->escape_string($s_code) . "', s_emotion='" . $gb_auth->escape_string($s_emotion) . "' WHERE id='$edit_smilie'");
         $admin->show_panel('smilies');
       }
       $gb_auth->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_smilies WHERE id='$edit_smilie'");
       if ($gb_auth->fetch_array($gb_auth->result))
       {
         $smilie_data = $gb_auth->record;
       }
     }
     if (isset($add_smilies))
     {
       if(isset($new_smilie) && isset($new_emotion))
       {
         for(reset($new_smilie); $key=key($new_smilie); next($new_smilie))
         {
           if (!empty($new_emotion[$key]) && !empty($new_smilie[$key]))
           {
             $size = GetImageSize("./img/smilies/$key");
             $gb_auth->query("INSERT INTO ".LAZ_TABLE_PREFIX."_smilies (s_code,s_filename,s_emotion,width,height) VALUES('" . $gb_auth->escape_string($new_smilie[$key]) . "','" . $gb_auth->escape_string($key) . "','" . $gb_auth->escape_string($new_emotion[$key]) . "','".$size[0]."','".$size[1]."')");
           }
         }
       }
     }
     $admin->show_panel('smilies');
     break;

   case 'update': 
     $id = $_POST['id'];
     $admin->update_record($id,$tbl);
     $admin->show_entry($tbl,$rid);
     break;

   case 'template':
     $tpl_name = (isset($_GET['tpl_name'])) ? $_GET['tpl_name'] : '';
     $postVars = array(
       'gb_template', 
       'tpl_name', 
       'save'
     );
     create_variable($postVars);       
     $save = (isset($save)) ? $save : '';
     $template_list = $admin->scan_templates_dir();
     $admin->edit_template($tpl_name,$save);
     break;
    
   case 'bandel':
     $admin->ban_ip($ip);
     $admin->del_entry($id,$tbl);
     $admin->show_entry($tbl,$rid,$LANG['AdminTopIPBanned']);
     break;
    
   case 'banunaccept':
     $admin->ban_ip($ip);
     $admin->unaccept_entry($id,$tbl);
     $admin->show_entry($tbl,$rid,$LANG['AdminTopIPBanned'].' '.$LANG['AdminTopMessUnacc']);
     break;
    
   case 'banip':
     $admin->ban_ip($ip);
     $admin->show_entry($tbl,$rid,$LANG['AdminTopIPBanned']);
    break;    

   case 'save':
    if ($panel == 'general')
    {
      if($section == 'general')
      {
        // This is the variables for this section
        $postVars = array(
          'entries_per_page', 
          'lang', 
          'book_name', 
          'charset', 
          'show_ip', 
          'allow_html', 
          'allowed_tags', 
          'allow_search', 
          'smilies', 
          'always_flash', 
          'agcode', 
          'allow_urlagcode', 
          'allow_emailagcode', 
          'allow_imgagcode', 
          'allow_flashagcode', 
          'agcode_img_width', 
          'agcode_img_height', 
          'encrypt_email', 
          'base_url', 
          'laz_url', 
          'permalinks', 
          'use_gravatar', 
          'included'
        );
        create_variable($postVars);
        $entries_per_page  = (is_numeric(intval($entries_per_page))) ? intval($entries_per_page) : 10;
        $always_flash      = (isset($always_flash)) ? 1 : 0;
        $allow_imgagcode   = (isset($allow_imgagcode)) ? 1 : 0;
        $allow_flashagcode = (isset($allow_flashagcode)) ? 1 : 0;
        $allow_urlagcode   = (isset($allow_urlagcode)) ? 1 : 0;
        $allow_emailagcode = (isset($allow_emailagcode)) ? 1 : 0;
        $permalinks        = (isset($permalinks)) ? 1 : 0;
        $use_gravatar      = (isset($use_gravatar)) ? 1 : 0;
        $laz_url           = htmlspecialchars($laz_url);
        $sqlquery  = "UPDATE ".LAZ_TABLE_PREFIX."_config set entries_per_page='$entries_per_page', lang='" . $gb_auth->escape_string($lang) . "', book_name='" . $gb_auth->escape_string($book_name) . "', charset='" . $gb_auth->escape_string($charset) . "', show_ip='$show_ip', allow_html='$allow_html', allowed_tags='" . $gb_auth->escape_string($allowed_tags) . "', laz_url='" . $gb_auth->escape_string($laz_url) . "', use_gravatar='$use_gravatar', ";
        $sqlquery .= "allow_search='$allow_search', smilies='$smilies', always_flash='$always_flash', agcode='$agcode', allow_urlagcode='$allow_urlagcode', allow_imgagcode='$allow_imgagcode', allow_flashagcode='$allow_flashagcode', allow_emailagcode='$allow_emailagcode', included='$included', ";
        $sqlquery .= "agcode_img_width='$agcode_img_width', agcode_img_height='$agcode_img_height', encrypt_email='$encrypt_email', base_url='" . $gb_auth->escape_string($base_url) . "', permalinks='$permalinks', agcode_img_width='$agcode_img_width', agcode_img_height='$agcode_img_height' WHERE (config_id = '1')";
        $gb_auth->query($sqlquery);
      }
      elseif($section == 'fields')
      {
        // This is the variables for this section
        $postVars = array(
          'require_email', 
          'allow_loc', 
          'allow_url', 
          'allow_gender', 
          'allow_icq', 
          'allow_aim', 
          'allow_msn', 
          'allow_yahoo', 
          'allow_skype', 
          'allow_private', 
          'hide_comments', 
          'allow_img', 
          'img_width', 
          'img_height', 
          'max_img_size', 
          'thumbnail', 
          'thumb_min_fsize', 
          'disablecomments'
        );
        create_variable($postVars);
        if ($allow_img == 1)
        {
          $test = @is_dir(LAZ_INCLUDE_PATH.'/public');
          if (!$test)
          {
            @mkdir(LAZ_INCLUDE_PATH.'/public', 0777);
          }
        }
        $thumbnail = (isset($thumbnail)) ? 1 : 0;
        $sqlquery  = "UPDATE ".LAZ_TABLE_PREFIX."_config set require_email='$require_email', allow_loc='$allow_loc', allow_url='$allow_url', allow_gender='$allow_gender', allow_icq='$allow_icq', allow_aim='$allow_aim', allow_msn='$allow_msn', ";
        $sqlquery .= "allow_yahoo='$allow_yahoo', allow_skype='$allow_skype', allow_private='$allow_private', hide_comments='$hide_comments', allow_img='$allow_img', img_width='$img_width', img_height='$img_height', max_img_size='$max_img_size', ";
        $sqlquery .= "thumbnail='$thumbnail', thumb_min_fsize='$thumb_min_fsize', disablecomments='$disablecomments' WHERE (config_id = '1')";
        $gb_auth->query($sqlquery);
      }
      elseif ($section == 'email')
      {
        // This is the variables for this section
        $postVars = array(
          'admin_mail', 
          'book_mail', 
          'always_bookemail', 
          'notify_private', 
          'notify_admin', 
          'notify_admin_com', 
          'notify_guest', 
          'html_email', 
          'notify_mes', 
          'mail_type', 
          'smtp_server', 
          'smtp_username', 
          'smtp_password', 
          'smtp_port', 
          'mailSSL'
        );
        
        create_variable($postVars);
        $always_bookemail = (isset($always_bookemail)) ? 1 : 0;
        $notify_private   = (isset($notify_private)) ? 1 : 0;
        $notify_admin     = (isset($notify_admin)) ? 1 : 0;
        $notify_admin_com = (isset($notify_admin_com)) ? 1 : 0;
        $notify_guest     = (isset($notify_guest)) ? 1 : 0;
        $html_email       = (isset($html_email)) ? 1 : 0;
        $smtp_port        = (isset($smtp_port) && is_numeric($smtp_port)) ? intval($smtp_port) : 25; 
        $sqlquery         = "UPDATE ".LAZ_TABLE_PREFIX."_config set admin_mail='" . $gb_auth->escape_string($admin_mail) . "', book_mail='" . $gb_auth->escape_string($book_mail) . "', always_bookemail='$always_bookemail', notify_private='$notify_private', notify_admin='$notify_admin', notify_admin_com='$notify_admin_com', notify_guest='$notify_guest', notify_mes='" . $gb_auth->escape_string($notify_mes) . "', html_email='$html_email', smtp_server='" . $gb_auth->escape_string($smtp_server) . "', smtp_username='" . $gb_auth->escape_string($smtp_username) . "', smtp_password='" . $gb_auth->escape_string($smtp_password) . "', smtp_port='$smtp_port', mail_type='$mail_type', mailSSL='$mailSSL' WHERE (config_id = '1')";
        $gb_auth->query($sqlquery);
      }
      elseif ($section == 'security')
      {
        // This is the variables for this section
        $postVars = array(
          'count_blocks', 
          'post_time_min', 
          'post_time_max', 
          'check_headers', 
          'antibottest', 
          'bottestquestion', 
          'bottestanswer', 
          'need_pass', 
          'comment_pass', 
          'com_question', 
          'solve_media', 
          'captcha_noise', 
          'sfs_confidence', 
          'captcha_grid', 
          'captcha_grey', 
          'captcha_trans', 
          'captcha_width', 
          'captcha_height', 
          'max_url', 
          'require_checking', 
          'require_comchecking', 
          'min_text', 
          'max_text', 
          'max_word_len', 
          'use_regex', 
          'badwords1', 
          'badwords2', 
          'badwords3', 
          'flood_check', 
          'flood_timeout', 
          'banned_ip', 
          'banned_ips', 
          'honeypot'
        );
        
        create_variable($postVars);
        $max_url       = (is_numeric(intval($max_url))) ? intval($max_url) : 99;
        $captcha_noise = (isset($captcha_noise)) ? 1 : 0;
        $captcha_grid  = (isset($captcha_grid)) ? 1 : 0;
        $captcha_grey  = (isset($captcha_grey)) ? 1 : 0;
        $captcha_trans = (isset($captcha_trans)) ? 1 : 0;
        $use_regex     = (isset($use_regex)) ? 1 : 0;
        $check_headers = (isset($check_headers)) ? 1 : 0;
        $count_blocks  = (isset($count_blocks)) ? 1 : 0;
        $solve_media   = (isset($solve_media)) ? 1 : 0;

        $sqlquery  = "UPDATE ".LAZ_TABLE_PREFIX."_config set use_regex='$use_regex', post_time_max='$post_time_max', post_time_min='$post_time_min', captcha_trans='$captcha_trans', antibottest='$antibottest', bottestquestion='" . $gb_auth->escape_string($bottestquestion) . "', bottestanswer='" . $gb_auth->escape_string($bottestanswer) . "', count_blocks='$count_blocks', sfs_confidence='$sfs_confidence', ";
        $sqlquery .= "min_text='$min_text', max_text='$max_text', max_word_len='$max_word_len', require_checking='$require_checking', require_comchecking='$require_comchecking', flood_check='$flood_check', banned_ip='$banned_ip', check_headers='$check_headers', solve_media='$solve_media', honeypot='$honeypot', ";
        $sqlquery .= "flood_timeout='$flood_timeout', captcha_noise='$captcha_noise', captcha_grid='$captcha_grid', captcha_grey='$captcha_grey', max_url='$max_url', need_pass='$need_pass', comment_pass='" . $gb_auth->escape_string($comment_pass) . "', com_question='" . $gb_auth->escape_string($com_question) . "', captcha_width='$captcha_width', captcha_height='$captcha_height' WHERE (config_id = '1')";
        $gb_auth->query($sqlquery);
        
        // First we delete everything in the bad words table
        $sqlquery= 'DELETE FROM '.LAZ_TABLE_PREFIX.'_words';
        $gb_auth->query($sqlquery);
        
        // Now we loop through all three lists to check for empties and duplicates before storing them
        // 1 = censor, 2 = block and 3 = moderate
        for ($i=1; $i<=3; $i++)
        {
          $badwords = 'badwords'.$i; // Create a variable containing the name of the textarea
          $$badwords = str_replace("\r", '', trim($$badwords)); // Remove any \r which windows adds and trim any white space from ends
          $word_array = explode("\n", $$badwords);
          $word_array = array_unique($word_array); // Remove any duplicates
          if (sizeof($word_array > 0))
          {
            foreach ($word_array as $value) // Loop through the array and add each entry to the table along with what type it is
            {
              if (trim($value) != '') 
              {
                $sqlquery= "INSERT INTO ".LAZ_TABLE_PREFIX."_words (word, type) VALUES('" . $gb_auth->escape_string($value) . "', $i)";
                $gb_auth->query($sqlquery);
              }
            }
          }
        }
        $banned_ips = str_replace("\r", '', trim($banned_ips));
        $ip_array = explode("\n", $banned_ips);
        $ip_array = array_unique($ip_array);
        if (sizeof($ip_array) > 0)
        {
          $sqlquery = 'DELETE FROM '.LAZ_TABLE_PREFIX.'_ban WHERE timestamp=0';
          $gb_auth->query($sqlquery);
          foreach ($ip_array as $value)
          {
            if ((preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){2}/', $value)) || (preg_match('!^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])/[0-2]?[0-9]|3[0-2]$!', $value)))
            {
              $sqlquery= "INSERT INTO ".LAZ_TABLE_PREFIX."_ban (ban_ip) VALUES('".$value."')";
              $gb_auth->query($sqlquery);
            }
          }
        }
      }
      $admin->get_updated_vars();
      $admin->show_settings('general');
    }
    elseif ($panel == 'style')
    {
      if ($section == 'style')
      {
        // This is the variables for this section
        $postVars = array(
          'pbgcolor', 
          'width', 
          'font_face', 
          'link_color', 
          'text_color', 
          'tb_font_1', 
          'tb_font_2', 
          'laz_top_font_color', 
          'top_link_color', 
          'laz_top_num_color', 
          'tb_hdr_color', 
          'tb_text', 
          'tb_bg_color', 
          'tb_color_1', 
          'tb_color_2', 
          'search_bg_color', 
          'search_font_color', 
          'input_error_color', 
          'errorbox_border_color', 
          'errorbox_border_style', 
          'errorbox_border_width', 
          'errorbox_font_color', 
          'errorbox_back_color', 
          'external_css'
        );
        create_variable($postVars);
        
        $external_css = (isset($external_css)) ? 1 : 0;
        $sqlquery  = "UPDATE ".LAZ_TABLE_PREFIX."_config set pbgcolor='" . $gb_auth->escape_string($pbgcolor) . "', width='$width', font_face='" . $gb_auth->escape_string($font_face) . "', text_color='" . $gb_auth->escape_string($text_color) . "', link_color='" . $gb_auth->escape_string($link_color) . "', tb_font_1='$tb_font_1', tb_font_2='$tb_font_2', input_error_color='" . $gb_auth->escape_string($input_error_color) . "', top_link_color='" . $gb_auth->escape_string($top_link_color) . "', ";
        $sqlquery .= "tb_hdr_color='" . $gb_auth->escape_string($tb_hdr_color) . "', tb_bg_color='" . $gb_auth->escape_string($tb_bg_color) . "', tb_text='" . $gb_auth->escape_string($tb_text) . "', tb_color_1='" . $gb_auth->escape_string($tb_color_1) . "', tb_color_2='" . $gb_auth->escape_string($tb_color_2) . "', search_bg_color='" . $gb_auth->escape_string($search_bg_color) . "', search_font_color='" . $gb_auth->escape_string($search_font_color) . "', laz_top_font_color='" . $gb_auth->escape_string($laz_top_font_color) . "', laz_top_num_color='" . $gb_auth->escape_string($laz_top_num_color) . "', ";
        $sqlquery .= "errorbox_border_color='" . $gb_auth->escape_string($errorbox_border_color) . "', errorbox_border_width='" . $gb_auth->escape_string($errorbox_border_width) . "', errorbox_border_style='" . $gb_auth->escape_string($errorbox_border_style) . "', errorbox_font_color='" . $gb_auth->escape_string($errorbox_font_color) . "', errorbox_back_color='" . $gb_auth->escape_string($errorbox_back_color) . "', external_css='$external_css' WHERE (config_id = '1')";
      }
      elseif ($section == 'date')
      {
        // This is the variables for this section
        $postVars = array(
          'tformat', 
          'offset', 
          'dformat', 
          'smarttime'
        );
        
        create_variable($postVars);
        $smarttime = (isset($smarttime)) ? 1 : 0;
        $sqlquery  = "UPDATE ".LAZ_TABLE_PREFIX."_config set dformat='$dformat', tformat='$tformat', offset='$offset', smarttime='$smarttime' WHERE (config_id = '1')";
      }
      elseif ($section == 'adblock')
      {
        // This is the variables for this section
        $postVars = array(
          'ad_code', 
          'ad_pos'
        );
        
        create_variable($postVars);
        $sqlquery  = "UPDATE ".LAZ_TABLE_PREFIX."_config set ad_pos='$ad_pos', ad_code='" . $gb_auth->escape_string($ad_code) . "' WHERE (config_id = '1')";  
      }
      elseif ($section == 'include')
      {
        $external_css = (isset($external_css)) ? 1 : 0;
        $sqlquery  = "UPDATE ".LAZ_TABLE_PREFIX."_config set external_css='$external_css' WHERE (config_id = '1')";
      }
      
      $gb_auth->query($sqlquery);
      $admin->get_updated_vars();
      $admin->show_settings('style');
    }
    elseif ($panel == 'password')
    {
      // This is the variables for this section
      $postVars = array(
        'NEWadmin_name', 
        'NEWadmin_pass', 
        'comfirm'
      );
      
      create_variable($postVars);
      if (trim($NEWadmin_pass) == '') 
      {
        $sqlquery= "UPDATE ".LAZ_TABLE_PREFIX."_auth set username='$NEWadmin_name' WHERE (ID = '$uid')";
      }
      else
      {       
        $sqlquery= "UPDATE ".LAZ_TABLE_PREFIX."_auth set username='$NEWadmin_name', password=PASSWORD('$NEWadmin_pass') WHERE (ID = '$uid')";
      }
      $gb_auth->query($sqlquery);
      $admin->get_updated_vars();
      $admin->show_settings('pwd');
    }
    else
    {
      $admin->show_panel();
    }
    break;

   case 'settings':
     if ($panel == 'general')
     {
       $admin->show_settings('general');
     }
     elseif ($panel == 'style')
     {
       $admin->show_settings('style');
     }
     elseif ($panel == 'pwd')
     {
       $admin->show_settings('pwd');
     }
     else
     {
       $admin->show_panel();
     }
     break;

   case 'logout':
     $gb_auth->generateNewSessionID($uid);
     $message = $LANG['PassMess1'];
     if (isset($_GET['delcookie']))
     {
       setcookie('lgu', '', time() - 3600);
       setcookie('lgp', '', time() - 3600);
     }
     die ('Successfully logged out. You may now close this window.');
     //header("Location: $GB_PG[index]");
     break;

   default:
     $admin->show_panel("intro");
     break;
  }

}

echo trim(ob_get_clean());
?>