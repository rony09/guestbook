<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 20:00:49 GMT
 * ----------------------------------------------
 */

class addentry 
{
  var $db;
  var $ip;
  var $template;
  var $name = '';
  var $email = '';
  var $url = '';
  var $comment = '';
  var $location = '';
  var $icq = '';
  var $aim = '';
  var $msn = '';
  var $yahoo = '';
  var $skype = '';
  var $gender = '';
  var $bottest = '';
  var $userfile = '';
  var $timehash = '';
  var $user_img = '';
  var $preview = '';
  var $private = '';
  var $keep_pic = '';
  var $image_file = '';
  var $image_tag = '';
  var $honeypot = '';
  var $accepted = 1;
  var $table = array();

  function __CONSTRUCT($path = '')
  {
    global $GB_TBL, $_SERVER, $GB_PG;
    $this->ip = $_SERVER['REMOTE_ADDR'];
    $this->db = new guestbook_vars($path);
    $this->db->getVars();
    $this->template = &$this->db->template;
    $this->include_path = $path;
    //$this->table = &$GB_TBL;
    $GB_PG['base_url'] = $this->db->VARS['base_url'];
  }
  
//
// Lets check the tmp folder for any images older than 30 minutes and delete them
//

  function clear_tmpfiles($cachetime = 1800)
  {
    $delfiles = 0;
    $filelist = '';
    if (is_dir($this->include_path . '/tmp'))
    {
      if (is_writable($this->include_path . '/tmp'))
      {
        $hnd = opendir($this->include_path . '/tmp');
        while (($file = readdir($hnd)))
        {
          if (is_file($this->include_path.'/tmp/'.$file))
          {
            $filelist[] = $file;
          }
        }
        closedir($hnd);
      }
    }
    if (is_array($filelist))
    {
      while (list ($key, $file) = each ($filelist))
      {
        //if (preg_match('/jpg|gif|png|swf|bmp|tmp/i', $tmpfile[1]))
        if (strpos($file, 'img-') === 0)
        {
          $tmpfile = explode('.', $file);
          $tmpfile[0] = preg_replace('/img-/', '', $tmpfile[0]);
          if ($tmpfile[0] < (time()-$cachetime))
          {
            if (unlink($this->include_path . '/tmp/' . $file))
            {
              $delfiles++;
            }
          }
        }
        else
        {
          if (unlink($this->include_path . '/tmp/' . $file))
          {
            $delfiles++;
          }
        }
      }
    }
    return $delfiles;
  }
  
  
//
// Just a function to strip slashes from everything on error
//
  
  function strip_all_slashes()
  {
    $this->name     = stripslashes($this->name);
    $this->email    = stripslashes($this->email);
    $this->url      = stripslashes($this->url);
    $this->comment  = stripslashes($this->comment);
    $this->location = stripslashes($this->location);
    $this->icq      = stripslashes($this->icq);
    $this->aim      = stripslashes($this->aim);
    $this->msn      = stripslashes($this->msn);
    $this->yahoo    = stripslashes($this->yahoo);
    $this->skype    = stripslashes($this->skype);
    $this->gender   = stripslashes($this->gender);
    $this->bottest  = stripslashes($this->bottest);
    $this->timehash = stripslashes($this->timehash);
  }

//
// Check the submitted entry to make sure it's all nice and fits in with our rules
//

  function check_entry($type = '')
  {
    global $GB_PG;
    $the_max_img_size = $this->db->VARS['max_img_size']*1024;
    $the_time = time();
    if (get_magic_quotes_gpc())
    {
      $this->strip_all_slashes();
    }    
    $this->name     = htmlspecialchars($this->db->FormatString($this->name));
    $this->email    = htmlspecialchars($this->db->FormatString($this->email));
    $this->location = htmlspecialchars($this->db->FormatString($this->location));
    $this->comment  = htmlspecialchars($this->db->FormatString($this->comment));
    $this->icq      = intval($this->db->FormatString($this->icq));
    $this->aim      = htmlspecialchars($this->db->FormatString($this->aim));
    $this->msn      = htmlspecialchars($this->db->FormatString($this->msn));
    $this->yahoo    = htmlspecialchars($this->db->FormatString($this->yahoo));
    $this->skype    = htmlspecialchars($this->db->FormatString($this->skype));
    
    // Are we checking for the honeypot?
    if($this->db->VARS['honeypot'] == 1)
    {
      if($this->honeypot == 1)
      {
        //sleep(20);
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost10'], 1),0,1);
      }
    }
    
    // Check if a timehash has been sent otherwise submitted data has been manipulated
    if ($this->timehash == '')
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost4'].' (4)', 5),0,1);
    }
    
    // Are we running a bot test and if so is the answer empty?
    if ((($this->db->VARS['antibottest'] == 1) || ($this->db->VARS['antibottest'] == 2)) && (empty($this->bottest)))
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost13'], 3),0,1);
    }
    
    // Just usual adding of slashes for protection
    if (!get_magic_quotes_gpc())
    {
      $this->bottest = addslashes($this->bottest);
      $this->db->VARS['bottestanswer'] = addslashes($this->db->VARS['bottestanswer']);
      $this->timehash = addslashes($this->timehash);
    }
    
    /* 
     * We are using a bot test so lets check their answer
     * 1 - built in captcha or question and answer
     * 2 - Using third party captcha of Solve Media     
     */
    if (($this->db->VARS['antibottest'] == 1) && (strtolower($this->bottest) != strtolower($this->db->VARS['bottestanswer'])))
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost14'], 4),0,1);
    }
    elseif (($this->db->VARS['antibottest'] == 2))
    {
      if (($this->db->VARS['solve_media'] == 0) && (!$this->db->captcha_test($this->bottest, $this->timehash)))
      {
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost14'], 4),0,1);
      }
      elseif ($this->db->VARS['solve_media'] == 1) 
      {
      	require_once(LAZ_INCLUDE_PATH . '/solvemedialib.php');
        $privkey='IgTCJw84R-0k.RO.6NVgCAiaaDp5qzNW';
        $hashkey='YBb1ktjDEKcxkdb6fmgnjo.ODYvf0nbj';
        $adcopy_challenge =(!get_magic_quotes_gpc()) ? addslashes($_POST['adcopy_challenge']) : $_POST['adcopy_challenge'];
        $solvemedia_response = solvemedia_check_answer($privkey, $this->ip, $adcopy_challenge, $this->bottest, $hashkey);
        if (!$solvemedia_response->is_valid)
        {
        	return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost14'], 4),0,1);
        }
      }
    }
    
    // Make the timehash in to something we can use
    $decodedhash = $this->db->generate_timehash($this->timehash);
    
    // Now check if they have posted to fast
    if (($the_time < ($decodedhash + $this->db->VARS['post_time_min'])) && ($this->db->VARS['post_time_min'] != 0))
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost15']),0,1);
    }
    
    // Or possibly they took to long to post
    if (($the_time > ($decodedhash + $this->db->VARS['post_time_max'])) && ($this->db->VARS['post_time_max'] != 0))
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost16']),1, 1);
    }
    
    /*
     * Do we want to check the headers? If so then do it
     * If they fail the check the error message will end with the number 5 followed by a .
     * with the next number indicating which header they failed on
     */     
    if ($this->db->VARS['check_headers'] == 1)
    {
      if (($failedHeader = $this->db->check_headers(1, $this->ip)) != 0)
      {
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost4'].' (5.'.$failedHeader.')', 6),0,1);
      }
    }
    
    // I have set a limit of 50 characters for the email address. Probably should increase that
    if (strlen($this->email) > 50)
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost4']));
    }
    
    // Lets check that the email is valid by RFC specs
    if (!$this->db->check_emailaddress($this->email))
    {
      $this->email = '';
    }
    
    // if their ICQ number is to low or to high then make it 0
    if (($this->icq < 1000) || ($this->icq >999999999))
    {
      $this->icq=0;
    }
    
    /*
     * This section is just some bog standard checks such as
     * Check they gave us their name,
     * that the message isn't to short or to long.
     * do we require an email address and if so have they provided one,
     * any submitted URL is valid otherwise remove it.
     * For example (5.6) means that their hostname indicates they are on a banned host
     */
    if ($this->name == '')
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost1']));
    }
    elseif (strlen($this->comment)<$this->db->VARS['min_text'])
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost3']));
    }
    elseif (strlen($this->comment)>$this->db->VARS['max_text'])
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost17']));
    }
    elseif ((($this->db->VARS['require_email'] == 1) || ($this->db->VARS['require_email'] == 4)) && $this->email == '')
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost12']));
    }
    else
    {
      $this->url = trim($this->url);
      //if (($this->url, 0, 7) !== 'http://')
      if (!preg_match('/^http(s)?\:\/\//i', $this->url))
      {
        $this->url = 'http://'.$this->url;
      }
      //if (!preg_match('/^http(s)?\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_=]*)?$/i', $this->url))
      if (!preg_match('/^https?:\/\/[\w\#$%&~\/.\-;:=,?@\[\]+]+$/uis', $this->url))
      {
        $this->url = '';
      }
      if (htmlspecialchars($this->url) != $this->url)
      {
        $this->url = '';
      }
    }
    
    // Check if their IP is banned
    if (($this->db->VARS['banned_ip'] == 1) || ($this->db->VARS['sfs_confidence'] > 0))
    {
      $banned = $this->db->isBannedIp($this->ip, $this->db->VARS['banned_ip'], $this->db->VARS['sfs_confidence']);
      if ($banned == 1) // Admin has banned their IP
      {
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost9'], 2),0,1);
      }
      elseif ($banned == 2)  // Their IP was added to block list by SFS
      {
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost9'], 9),0,1);
      }
    }
    
    // Check if enough time has passed since their last post
    if ($this->db->VARS['flood_check'] == 1)
    {
      if ($this->db->FloodCheck($this->ip))
      {
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost8']),0,1);
      }
    }
    
    // Check no part of the post contains any banned words
    if ($this->db->BlockBadWords($this->name) || $this->db->BlockBadWords($this->email) || $this->db->BlockBadWords($this->location) || $this->db->BlockBadWords($this->comment) || $this->db->BlockBadWords($this->url))
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost10'], 7));
    }
    
    // Check that neither name nor location are to long
    if (!$this->db->CheckWordLength($this->name) || !$this->db->CheckWordLength($this->location))
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost4'].' (3)'));
    }
    
    // Check their entry is not longer than allowed
    if (!$this->db->CheckWordLength($this->comment))
    {
      return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost10']));
    }
    
    // Have they posted to many URLs?
    if ($this->db->VARS['max_url'] < 99)
    {
      if ($this->db->urlCounter($this->comment) > $this->db->VARS['max_url'])
      {
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost10'], 8));
      }
    }
    
    // If we want to check them againt the SFS database then lets do so
    if ($this->db->VARS['sfs_confidence'] > 0)
    {
      $sfsCheck = $this->db->SFSCheck($this->ip, $this->email);
      if($sfsCheck != 0)
      {
        if($sfsCheck == 3)
        {
          $this->accepted = 0;
        }
        else
        {
          $errorMessage = ($sfsCheck == 1) ? $this->db->LANG['ErrorPost9'] : $this->db->LANG['ErrorPost4'];
          return $this->form_addguest($this->db->gb_error($errorMessage . ' (sfs)', 9),0,1);
        }
      }
    }
    
    // Have they submitted an image, if so deal with it
    if (is_array($this->userfile) && ($this->db->VARS['allow_img'] == 1) && $this->userfile['userfile']['tmp_name'] != 'none' && (strpos($this->userfile['userfile']['type'], 'image') === 0))
    {
      $extension = array('1' => 'gif','2' => 'jpg','3' => 'png', '6' => 'bmp');
      if ($this->userfile['userfile']['size'] > $the_max_img_size)
      {
        return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost6']));
      }
      else
      {
        move_uploaded_file($this->userfile['userfile']['tmp_name'], $this->include_path . '/tmp/img-' . $the_time . '.tmp');
        $size = GetImageSize($this->include_path . '/tmp/img-' . $the_time . '.tmp');
        if (($size !== false) && ((($size[2] > 0) && ($size[2] < 4)) || ($size[2] == 6)))
        {
          $this->image_file = 'img-' . $the_time . '.' . $extension[$size[2]];
          $img = new gb_image();
          $img->set_destdir($this->include_path . '/public');
          $img->set_border_size($this->db->VARS['img_width'], $this->db->VARS['img_height']);
          if ($type == 'preview')
          {
            rename($this->include_path . '/tmp/img-' . $the_time . '.tmp', $this->include_path . '/tmp/' . $this->image_file);
            chmod($this->include_path . '/tmp/' . $this->image_file, 0755);
            $new_img_size = $img->get_img_size_format($size[0], $size[1]);
            $GB_UPLOAD = 'tmp';
            $row['p_filename'] = $this->image_file;
            $row['p_filename2'] = $this->image_file;
            $row['width'] = $size[0];
            $row['height'] = $size[1];
            $id = '1';
            eval("\$this->tmp_image = \"".$this->template->get_template('user_pic')."\";");
          }
          else
          {
            rename($this->include_path . '/tmp/img-' . $the_time . '.tmp', $this->include_path . '/public/' . $this->image_file);
            chmod($this->include_path . '/public/' . $this->image_file, 0755);
            if ($this->db->VARS['thumbnail'] == 1)
            {
              $min_size = 1024*$this->db->VARS['thumb_min_fsize'];
              $img->set_min_filesize($min_size);
              $img->set_prefix('t_');
              $img->create_thumbnail($this->include_path . '/public/' . $this->image_file, "$this->image_file");
            }
          }
        }
        else
        {
          @unlink($this->include_path . '/tmp/img-' . $the_time . '.tmp');
          return $this->form_addguest($this->db->gb_error($this->db->LANG['ErrorPost7']));
        }
      }
    }
    if (!empty($this->user_img))
    {
      $illegalChars = array('?' => '',"\\" => '',':'  => '','*' => '','"' => '','<' => '','>' => '','|' => '','../' => '','./' => '',"\n" => '',"\r" => '',"\t" => '');
      $this->image_file = trim(strtr($this->user_img, $illegalChars));
      $this->image_file = (file_exists($this->include_path . '/tmp/' . $this->image_file)) ? $this->image_file : '';
      if(!empty($this->image_file))
      {
        $img = new gb_image();
        $img->set_destdir($this->include_path . '/public');
        $img->set_border_size($this->db->VARS['img_width'], $this->db->VARS['img_height']);      
        if ($type == 'preview')
        {
          $size = getimagesize($this->include_path . '/tmp/' . $this->image_file);
          $new_img_size = $img->get_img_size_format($size[0], $size[1]);
          $GB_UPLOAD = 'tmp';
          $row['p_filename'] = $this->image_file;
          $row['p_filename2'] = $this->image_file;
          $row['width'] = $size[0];
          $row['height'] = $size[1];
          $id = '1';
          eval("\$this->tmp_image = \"".$this->template->get_template('user_pic')."\";");  
        }
        else
        {
          rename($this->include_path . '/tmp/' . $this->image_file, $this->include_path . '/public/' . $this->image_file);
          chmod($this->include_path . '/public/' . $this->image_file, 0755);
          if ($this->db->VARS['thumbnail'] == 1)
          {
            $min_size = 1024*$this->db->VARS['thumb_min_fsize'];
            $img->set_min_filesize($min_size);
            $img->set_prefix('t_');
            $img->create_thumbnail($this->include_path . '/public/' . $this->image_file, "$this->image_file");
          }
        }
      }   
    }
    return 1;
  }
   
//
// Format our entry for MySQL insertion then insert it
//   

  function add_guest()
  {
    global $GB_PG;
    if (($this->preview == 1) && ($this->user_img))
    {
      $img = new gb_image();
      $img->set_destdir($this->include_path . '/public');
      $img->set_border_size($this->db->VARS['img_width'], $this->db->VARS['img_height']);
      if ($this->db->VARS['thumbnail'] == 1)
      {
        $min_size = 1024*$this->db->VARS['thumb_min_fsize'];
        $img->set_min_filesize($min_size);
        $img->set_prefix('t_');
        $img->create_thumbnail($this->include_path . '/tmp/' . $this->user_img, $this->user_img);
      }
      copy($this->include_path . '/tmp/' . $this->user_img, $this->include_path . '/public/' . $this->user_img);
      unlink($this->include_path . '/tmp/' . $this->user_img);
      $this->image_file = $this->user_img;
    }
    if ($this->db->VARS['allow_html'] == 1)
    {
      $this->comment = $this->db->allowed_html($this->comment);
    }
    if ($this->db->VARS['agcode'] == 1)
    {
      $this->comment = $this->db->AGCode($this->comment);
    }
    if (get_magic_quotes_gpc())
    {
      $this->strip_all_slashes();
    }
    
    $this->name     = $this->db->escape_string($this->name);
    $this->location = $this->db->escape_string($this->location);
    $this->aim      = $this->db->escape_string($this->aim);
    $this->msn      = $this->db->escape_string($this->msn);
    $this->yahoo    = $this->db->escape_string($this->yahoo);
    $this->skype    = $this->db->escape_string($this->skype);
    $this->email    = $this->db->escape_string($this->email);
    $this->url      = $this->db->escape_string($this->url);
    $this->ip       = $this->db->escape_string($this->ip);
    $this->gender   = $this->db->escape_string($this->gender);    
    $host           = $this->db->escape_string(htmlspecialchars(gethostbyaddr($this->ip)));
    $agent          = $this->db->escape_string(htmlspecialchars($_SERVER['HTTP_USER_AGENT']));
    $the_time = time();
    if ($this->db->VARS['require_checking'] == 1)
    {
      $this->accepted = ($this->private == 1) ? '1' : '0';
    }
    // Check for moderation words
    if ($this->db->BlockBadWords($this->name,3) || $this->db->BlockBadWords($this->email,3) || $this->db->BlockBadWords($this->location,3) || $this->db->BlockBadWords($this->comment,3) || $this->db->BlockBadWords($this->url,3))
    {
      $this->accepted = ($this->private == 1) ? $this->accepted : '0';
    } 
    $sql_usertable = (($this->private == 1) && ($this->db->VARS['allow_private'] == 1)) ? LAZ_TABLE_PREFIX.'_private' : LAZ_TABLE_PREFIX.'_data';
    $this->db->query("INSERT INTO $sql_usertable (name,gender,email,url,date,location,host,browser,comment,icq,aim,msn,yahoo,skype,accepted,ip) VALUES ('$this->name','$this->gender','$this->email','$this->url',$the_time,'$this->location','$host','$agent','".$this->db->escape_string($this->comment)."','$this->icq','$this->aim','$this->msn','$this->yahoo','$this->skype',$this->accepted,'$this->ip')");
    $entry_id = $this->db->insert_id();
    $this->db->query("INSERT INTO ".LAZ_TABLE_PREFIX.'_ip'." (guest_ip,timestamp) VALUES ('$this->ip','$the_time')");
    $imagedata = array('mime' => '', 'name' => '', 'data' => '');
    if (!empty($this->image_file) || !empty($this->user_img))
    {
      $size = getimagesize("$this->include_path/public/$this->image_file");
      if ((is_array($size)) && ((($size[2] > 0) && ($size[2] < 4)) || $size[2] == 6))
      {
        $book_id = ($this->private==1) ? 1 : 2;
        $p_filesize = filesize("$this->include_path/public/$this->image_file");
        //$this->db->fetch_array($this->db->query("SELECT MAX(id) AS msg_id FROM $sql_usertable"));
        $this->db->query("INSERT INTO ".LAZ_TABLE_PREFIX."_pics (msg_id,book_id,p_filename,p_size,width,height) VALUES ('".$entry_id."',$book_id,'$this->image_file','$p_filesize','$size[0]','$size[1]')");
        if ($this->db->VARS['html_email'] == 1) 
        {
          if (!empty($size['mime']))
          {
            $imagedata['mime'] = $size['mime'];
          }
          else
          {
            $mimetype = array(1 => 'image/gif', 2 => 'image/jpeg', 3 => 'image/png', 6 => 'image/png');
            $imagedata['mime'] = $mimetype[$size[2]];
          }
          $imagedata['name'] = $this->image_file;
          $imgdata = file_get_contents($this->include_path . '/public/' . $this->image_file);
          $imagedata['data'] = chunk_split(base64_encode($imgdata));
        }
      }
    }
    $LANG =& $this->db->LANG;
    if ($this->db->check_emailaddress($this->db->VARS['book_mail']) && ($this->db->VARS['always_bookemail'] == 1))
    {
      $admin_email = $this->db->VARS['book_mail'];
    }
    else
    {
      $admin_emails = explode(',', $this->db->VARS['admin_mail']);
      if ($this->db->check_emailaddress($admin_emails[0]))
      {
        $admin_email = $admin_emails[0];
      }
      else 
      {
        $admin_email = 'guestbookentry@'.$host;
      }
    }
    if (($this->email == '') || ($this->db->VARS['always_bookemail'] == 1))
    {
      $from_email = $admin_email;
    }
    else
    {
      $from_email = $this->email;
    }
    //$messagebody = stripslashes($messagebody);
    $fromname = $this->db->undo_htmlspecialchars(stripslashes($this->name));
    if (($this->db->VARS['notify_guest'] == 1) && ($this->email != '') && ($admin_email != ''))
    {
      $email_message = nl2br($this->db->AGCode($this->db->VARS['notify_mes']));
      $email_message = str_replace('[NAME]', stripslashes($this->name), $email_message);
      $this->db->send_email($this->email,$this->db->LANG['EmailGuestSubject'],$email_message, 'From: "'.strip_tags($this->db->VARS['book_name']).'" <'.$admin_email.'>', $admin_email);
    }
    $hostname = (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $host) ) ? 'IP' : 'Host';
    $messagebody  = '<div style="background-color:#fff;border:1px solid #a5cae4;border-radius:5px;padding:5px;">';
    $messagebody .= $LANG['FormName'].': '.$this->name."<br />\n";
    $messagebody .= $hostname.': '.$host.' ('.$this->ip.")<br />\n";
    $messagebody .= ($this->location != '') ? $LANG['FormLoc'].': '.$this->location."<br />\n" : '';
    $messagebody .= ($this->email != '') ? $LANG['FormEmail'].': <a href="mailto:'.$this->email.'">'.$this->email."</a><br />\n" : '';
    $messagebody .= ($this->url != '') ? $LANG['FormUrl'].': <a href="'.$this->url.'" target="_blank">'.$this->url."</a><br />\n" : ''; 
    $messagebody .= ($this->aim != '') ? 'AIM: '.$this->aim."<br .>\n" : '';
    $messagebody .= ($this->icq != '') ? 'ICQ: '.$this->icq."<br />\n" : ''; 
    $messagebody .= ($this->msn != '') ? 'MSN: '.$this->msn."<br />\n" : ''; 
    $messagebody .= ($this->yahoo != '') ? 'Yahoo: '.$this->yahoo."<br />\n" : '';
    $messagebody .= ($this->skype != '') ? 'Skype: '.$this->skype."<br />\n" : '';
    $messagebody .= '<hr style="min-height:1px;margin:20px 0 10px;border:0;color:#d7edfc;background-color:#d7edfc" />';
    $messagebody .= "<br />\n<br />\n".nl2br($this->comment);
    if ($GB_PG['base_url'] != '')
    {
      $messagebody .= "<br />\n<br />\n<div style=\"background-color:#f0f7fc;border-top:1px solid #d7edfc;padding:2px;\">";
      if ($sql_usertable == LAZ_TABLE_PREFIX.'_data')
      {      
        $urlDivider = (strpos($this->db->VARS['laz_url'], '?') > 0) ? '&' : '?';
        $messagebody .= $LANG['EmailAdminSubject'].': <a href="'.$this->db->VARS['laz_url']. $urlDivider .'permalink=true&entry='.$entry_id.'">'.$this->db->VARS['laz_url']. $urlDivider .'permalink=true&entry='.$entry_id."</a><br>\n";
        $messagebody .= ($this->accepted == 0) ? $LANG['AdminAccept'].': <a href="'.$GB_PG['admin'].'?action=accept&amp;tbl=gb&amp;id='.$entry_id.'">'.$GB_PG['admin'].'?action=accept&amp;tbl=gb&amp;id='.$entry_id."</a><br>\n" : $LANG['AdminUnaccept'].': <a href="'.$GB_PG['admin'].'?action=unaccept&amp;tbl=gb&amp;id='.$entry_id.'">'.$GB_PG['admin'].'?action=unaccept&amp;tbl=gb&amp;id='.$entry_id."</a><br>\n";
      }
      $messagebody .= $LANG['AdminEdit'].': <a href="'.$GB_PG['admin'].'?action=edit&amp;tbl=gb&amp;id='.$entry_id.'">'.$GB_PG['admin'].'?action=edit&amp;tbl=gb&amp;id='.$entry_id."</a><br />\n";
      $messagebody .= $LANG['AdminDelete'].': <a href="'.$GB_PG['admin'].'?action=del&amp;tbl=gb&amp;id='.$entry_id.'">'.$GB_PG['admin'].'?action=del&amp;tbl=gb&amp;id='.$entry_id."</a><br />\n";
      $messagebody .= $LANG['FormSelect'].': <a href="'.$this->db->VARS['laz_url'].'">'.$this->db->VARS['laz_url']."</a><br />\n";
      $messagebody .= '</div>';
    }
    $messagebody .= '</div>';
    $admin_emails = explode(',', $this->db->VARS['admin_mail']);
    foreach ($admin_emails as $adminsaddy)
    {
      $adminsaddy = trim($adminsaddy);
      if ($this->db->check_emailaddress($adminsaddy))
      {
        if (($this->db->VARS['notify_private'] == 1) && ($this->private == 1))
        {
          $this->db->send_email($adminsaddy,$this->db->LANG['EmailAdminSubject'].' - '.$this->db->LANG['FormPriv'],$this->db->LANG['FormPriv']."<br>\n<br>\n".$messagebody, 'From: "'.$fromname.'" <'.$from_email.'>', $from_email, $imagedata);
        }
        if ((($this->db->VARS['notify_admin'] == 1) || ($this->db->VARS['require_checking'] == 1)) && ($this->private == 0))
        {
          $this->db->send_email($adminsaddy,$this->db->LANG['EmailAdminSubject'],$messagebody, 'From: "'.$fromname.'" <'.$from_email.'>', $from_email, $imagedata);
        }
      }
    }
    $LANG =& $this->db->LANG;
    $VARS =& $this->db->VARS;
    $success_message = $LANG['BookMess10'];
    if ($this->accepted == 0)
    {
      $success_message = $LANG['BookMess11'];
    }
    $success_html = '';
    eval("\$success_html .= \"".$this->template->get_template('success_header')."\";");
    eval("\$success_html .= \"".$this->template->get_template('success')."\";");
    eval("\$success_html .= \"".$this->template->get_template('footer', false)."\";");
    return $success_html;
  }
		
//
// Generate the form for them to sign
//

  function form_addguest($extra_html = '', $new_hash = 0, $an_error = 0)
  {
    global $GB_PG, $_COOKIE;
    $LANG =& $this->db->LANG;
    $VARS =& $this->db->VARS;
    if (($an_error === 1) && get_magic_quotes_gpc())
    {
      $this->strip_all_slashes();
    }
    $this->icq = (intval($this->icq) === 0) ? '' : $this->icq;
    $antispam = $this->db->VARS['antispam_word'];
    $HTML_CODE = ($this->db->VARS['allow_html'] == 1) ? $this->db->LANG['BookMess2'] : $this->db->LANG['BookMess1'];
    if (isset($_COOKIE['lang']) && !empty($_COOKIE['lang']) && file_exists($this->include_path.'/lang/codes-'.$_COOKIE['lang'].'.php'))
    {
      $LANG_CODES = $GB_PG[base_url].'/lang/codes-'.$_COOKIE['lang'].'.php';
    }
    elseif (file_exists($this->include_path.'/lang/codes-'.$VARS['lang'].'.php'))
    {
      $LANG_CODES = $GB_PG['base_url'].'/lang/codes-'.$VARS['lang'].'.php';
    }
    else
    {
      $LANG_CODES = $GB_PG['base_url'].'/lang/codes-english.php';
    }
    $AG_CODE = ($this->db->VARS['agcode'] == 1) ? '<a href="'.$LANG_CODES.'?show=agcode" onclick="openCentered(\''.$LANG_CODES.'?show=agcode\',\'_codes\',640,450,\'scrollbars=yes\'); return false;" target="_codes">'.$this->db->LANG['FormMess3'].'</a>' : $this->db->LANG['FormMess6'];
    if ($this->db->VARS['smilies'] == 1)
    {
      $SMILE_CODE = $this->db->LANG['FormMess2'];
      $SMILEYS = '';
      $LAZSMILEYS = $this->db->generate_smilies();
    }
    else
    {
      $SMILE_CODE = $this->db->LANG['FormMess7'];
      $LAZSMILEYS = '';
    }
    $EXTRAJS = '';
    $EMAILJS = '';
    $BOTTEST = '';
    $EMAILREQ = '';
    $EMAILDISPLAYED = ($this->db->VARS['require_email'] > 2) ? $LANG['FormEmailDisplay'] : '';
    if ((($this->db->VARS['require_email'] == 1) || ($this->db->VARS['require_email'] == 4)))
    {
      $EXTRAJS .= " document.book.gb_email.value=trim(document.book.gb_email.value);\nif(document.book.gb_email.value == \"\") {\n    errorStyling('gb_email');\n    errorMessages[errorNum++] = \"".$LANG['ErrorPost12']."\";\n }";
      $EMAILREQ = '*';
    }
    //$this->strip_all_slashes();
    $OPTIONS[] ='';
    if ($this->db->VARS['require_email'] != 2)
    {
      eval("\$OPTIONS['email'] = \"".$this->template->get_template('form_email')."\";");
    }
    if ($this->db->VARS['allow_loc'] == 1)
    {
      eval("\$OPTIONS['location'] = \"".$this->template->get_template('form_loc')."\";");
    }
    if ($this->db->VARS['allow_url'] == 1)
    {
      eval("\$OPTIONS['url'] = \"".$this->template->get_template('form_url')."\";");
    }
    if ($this->db->VARS['allow_icq'] == 1)
    {
      $this->icq = ($this->icq === 0) ? '' : $this->icq;
      eval("\$OPTIONS['icq'] = \"".$this->template->get_template('form_icq')."\";");
    }
    if ($this->db->VARS['allow_aim'] == 1)
    {
      eval("\$OPTIONS['aim'] = \"".$this->template->get_template('form_aim')."\";");
    }
    if ($this->db->VARS['allow_yahoo'] == 1)
    {
      eval("\$OPTIONS['yahoo'] = \"".$this->template->get_template('form_yahoo')."\";");
    }
    
    if ($this->db->VARS['allow_skype'] == 1)
    {
      eval("\$OPTIONS['skype'] = \"".$this->template->get_template('form_skype')."\";");
    }
    if ($this->db->VARS['allow_msn'] == 1)
    {
      eval("\$OPTIONS['msn'] = \"".$this->template->get_template('form_msn')."\";");
    }
    if ($this->db->VARS['allow_gender'] == 1)
    {
      $ourgender = array('f' => '', 'm' => '', 'x' => '');
      if(!empty($extra_html) && !empty($this->gender))
      {
        $ourgender[$this->gender] = ' checked';
      }
      else
      {
        $ourgender['x'] = ' checked';
      }
      eval("\$OPTIONS['gender'] = \"".$this->template->get_template('form_gender')."\";");
    }
    if ($this->db->VARS['allow_img'] == 1)
    {
      if((!empty($extra_html)) && (!empty($this->image_file)))
      {
        eval("\$OPTIONS['img'] = \"".$this->template->get_template('form_image_prev')."\";");
      }
      else
      {
        eval("\$OPTIONS['img'] = \"".$this->template->get_template('form_image')."\";");
      }
    }
    $TIMEHASH = (!empty($extra_html) && ($new_hash == 0)) ? $this->timehash : $this->db->generate_timehash();
    $footerJS = '';
    $PRIVATE = '';
    $PRIVATE .= ($this->db->VARS['allow_private'] == 1) ? '<input type="checkbox" name="gb_private" value="1" /> <font size="1" face="' . $VARS['font_face'] . '">' . $LANG['FormPriv'] . '</font>' : '';
    if($this->db->VARS['honeypot'] == 1)
    {
      $PRIVATE .= '<span id="gb_username"><input type="checkbox" name="gb_username" value="1" /> Spammer?<br /></span>';
      $footerJS .= "document.getElementById('gb_username').style.display = 'none';\n";
    }
    $PRIVATE .= '<input type="hidden" name="gb_timehash" value="' . $TIMEHASH . '" />';
    $OPTIONAL = implode("\n",$OPTIONS);
    if ($this->db->VARS['antibottest'] == 1)
    {
      $EXTRAJS .= " document.book.gb_bottest.value=trim(document.book.gb_bottest.value);\n if(document.book.gb_bottest.value == \"\") {\n    errorStyling('gb_bottest');\n    errorMessages[errorNum++] = \"".$LANG['ErrorPost13']."\";\n }";
      $bot_question = (get_magic_quotes_gpc()) ? stripslashes($this->db->VARS['bottestquestion']) : $this->db->VARS['bottestquestion'];
      eval("\$BOTTEST .= \"".$this->template->get_template('form_bots')."\";");
    }
    elseif ($this->db->VARS['antibottest'] == 2)
    {
      if ($this->db->VARS['solve_media'] == 1)
      {
        $EXTRAJS .= " document.book.adcopy_response.value=trim(document.book.adcopy_response.value);\n if(document.book.adcopy_response.value == \"\") {\n    errorStyling('adcopy_response');\n    errorMessages[errorNum++] = \"".$LANG['ErrorPost13']."\";\n }";
        require_once(LAZ_INCLUDE_PATH . '/solvemedialib.php'); //include the Solve Media library 
        $SolveMedia = solvemedia_get_html('G8vem0b2VDBXju20c9OwHO7makkjC9-o');	//outputs the widget
        eval("\$BOTTEST .= \"".$this->template->get_template('form_captcha2')."\";");
      }
      else
      {
        $EXTRAJS .= " document.book.gb_bottest.value=trim(document.book.gb_bottest.value);\n if(document.book.gb_bottest.value == \"\") {\n    errorStyling('gb_bottest');\n    errorMessages[errorNum++] = \"".$LANG['ErrorPost13']."\";\n }";
        eval("\$BOTTEST .= \"".$this->template->get_template('form_captcha')."\";");
        $footerJS .= "document.getElementById('captchaReload').style.display = 'block';\nreloadCaptcha();";
      }
    }
    $display_tags = $this->db->create_buttons($LANG_CODES);
    $addform_html = '';
    eval("\$addform_html = \"".$this->template->get_template('header')."\";");
    eval("\$addform_html .= \"".$this->template->get_template('form')."\";");
    eval("\$addform_html .= \"".$this->template->get_template('footer')."\";");
    return $addform_html;
  }
   
//
// If they want to preview their entry then we need to format the data
//   

  function preview_entry()
  {
    global $GB_PG;
    if (get_magic_quotes_gpc())
    {
      $this->strip_all_slashes();
    }
    //$this->name = htmlspecialchars($this->name);
    //$message = htmlspecialchars($this->comment);
    $message = nl2br($this->db->CensorBadWords($this->comment));
    $this->url = trim($this->url);
    $this->email = trim($this->email);
    //$TEXTEMAIL = '';
    $COMMENTLINK = '';
    if (!$this->db->check_emailaddress($this->email))
    {
      $this->email = '';
    }
    if (substr($this->url, 0, 4) == 'www.')
    {
      $this->url = 'http://'.$this->url;
    }
    if (!preg_match('/^https?:\/\/[\w\#$%&~\/.\-;:=,?@\[\]+]+$/uis', $this->url))
    {
      $this->url = '';
    }
    if (htmlspecialchars($this->url) != $this->url)
    {
      $this->url = '';
    }
    if ($this->db->VARS['allow_html'] == 1)
    {
      $message = $this->db->allowed_html($message);
    }
    if ($this->db->VARS['smilies'] == 1)
    {
      $message = $this->db->emotion($message);
    }
    if ($this->db->VARS['agcode'] == 1)
    {
      $message = $this->db->AGCode($message);
    }
    $antispam = $this->db->VARS['antispam_word'];
    //$this->location = htmlspecialchars($this->location);
    //$this->comment = htmlspecialchars($this->comment);
    $USER_PIC =(isset($this->tmp_image)) ? $this->tmp_image : '';
    $DATE = $this->db->DateFormat(time());
    $host = htmlspecialchars(gethostbyaddr($this->ip));
    $agent = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
    $LANG =& $this->db->LANG;
    $VARS =& $this->db->VARS;
    $this->name = $this->db->CensorBadWords($this->name);
    //$this->comment = $this->db->CensorBadWords($this->comment);   
    if ($this->url && $this->db->VARS['allow_url'] == 1)
    {
      $this->url = $this->db->CensorBadWords($this->url);
      $row['url'] = $this->url;
      eval("\$URL = \"".$this->template->get_template('url')."\";");
    }
    else
    {
      $URL = '';
    }
    if ($this->location && ($this->db->VARS['allow_loc'] == 1))
    {
      $this->location = $this->db->CensorBadWords($this->location);
      $row['location'] = $this->location;
      $THEIRLOC = urlencode($row['location']);
      eval("\$LOCATION = \"".$this->template->get_template('location')."\";");
    }
    else
    {
      $LOCATION = '';
    }    
    if ($this->icq && $this->db->VARS['allow_icq'] == 1)
    {
      $row['icq'] = $this->icq;
      eval("\$ICQ = \"".$this->template->get_template('icq')."\";");
    }
    else
    {
      $ICQ = '';
    }
    if ($this->aim && $this->db->VARS['allow_aim'] == 1)
    {
      $row['aim'] = $this->aim;
      eval("\$AIM = \"".$this->template->get_template('aim')."\";");
    }
    else
    {
      $AIM = '';
    }
    if ($this->msn && $this->db->VARS['allow_msn'] == 1)
    {
      $row['msn'] = $this->msn;
      eval("\$MSN = \"".$this->template->get_template('msn')."\";");
    }
    else
    {
      $MSN = '';
    }
    if ($this->yahoo && $this->db->VARS['allow_yahoo'] == 1)
    {
      $row['yahoo'] = $this->yahoo;
      eval("\$YAHOO = \"".$this->template->get_template('yahoo')."\";");
    }
    else
    {
      $YAHOO = '';
    }    
    if ($this->skype && $this->db->VARS['allow_skype'] == 1)
    {
      $row['skype'] = $this->skype;
      eval("\$SKYPE = \"".$this->template->get_template('skype')."\";");
    }
    else
    {
      $SKYPE = '';
    }
    if ($this->email)
    {
      if ($this->db->VARS['require_email'] < 2)
      {
        $this->email = $this->db->CensorBadWords($this->email);
        $row['email'] = $this->email;
        $GRAVATAR = ($this->db->VARS['use_gravatar'] == 1) ? ' background: transparent url(http://www.gravatar.com/avatar/' . md5($row['email']) . '?s=24&amp;d=wavatar&amp;r=G) no-repeat right;' : '';
        if ($this->db->VARS['encrypt_email'] == 1)
        {
          $MAILTO = $this->db->html_encode('mailto:'.$row['email']);
        }
        else
        {
          $MAILTO = 'mailto:'.$row['email'];
        }
        eval("\$EMAIL = \"".$this->template->get_template('email')."\";");
      }
      else
      {
        $EMAIL = '';
        $GRAVATAR = '';
      }
    }
    else
    {
      $GRAVATAR = '';
      $EMAIL = '';    
    }
    if ($this->db->VARS['allow_gender'] == 1)
    {
      if ($this->gender == 'f')
      {
        eval("\$GENDER = \"".$this->template->get_template('img_female')."\";");
      }
      elseif ($this->gender == 'm')
      {
        eval("\$GENDER = \"".$this->template->get_template('img_male')."\";");
      }
      else
      {
        $GENDER = '';
      }
    }
    else
    {
      $GENDER = '';
    }
    if ($this->db->VARS['show_ip'] == 1)
    {
      $hostname = (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $host) ) ? 'IP' : 'Host';
      $HOST = '<em style="font-weight: normal;">'.$hostname.': '.$host."</em>\n";
    }
    else
    {
      $HOST = '';
    }
    $theirbrowser = $this->db->browser_detect($agent); 
    $AGENT = '';   
    $row['name'] = $this->name;
    $row['location'] = $this->location;
    $GB_PREVIEW = '';
    $preview_html = '';
    eval("\$GB_PREVIEW = \"".$this->template->get_template('preview_entry')."\";");
    $preview_html = $this->form_addguest($GB_PREVIEW);
    return $preview_html;
  }
   
//
// Do whatever was requested from the addentry page. ie Display form or process entry
//   

  function process($action = '')
  {
    switch ($action)
    {
      case 'submit':
        /*if ($this->preview == 1)
        {
          $this->comment = $this->db->undo_htmlspecialchars($this->comment);
          $this->name = $this->db->undo_htmlspecialchars($this->name);
          $this->location = $this->db->undo_htmlspecialchars($this->location);
        }*/
        $this->clear_tmpfiles();
        $status = $this->check_entry();
        return ($status == 1) ? $this->add_guest() : $status;
        break;

      case 'preview':
        $status = $this->check_entry('preview');
        return ($status == 1) ? $this->preview_entry() : $status;
        break;

      default:
        return $this->form_addguest();
    }
  }
}
?>