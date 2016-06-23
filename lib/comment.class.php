<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook 
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 20:04:06 GMT
 * ----------------------------------------------
 */

class gb_comment 
{
  var $comment;
  var $ip;
  var $id;
  var $db;
  var $user;
  var $email;
  var $pass_comment;
  var $template;
  var $path;
  var $bottest;
  var $timehash;
  var $accepted = 1;
  var $honeypot;

  function __CONSTRUCT($path = '')
  {
    global $_SERVER, $GB_PG;
    $this->ip = $_SERVER['REMOTE_ADDR'];
    $this->db = new guestbook_vars($path);
    $this->db->getVars();
    $this->path = $path;
    $this->template =& $this->db->template;
    $GB_PG['base_url'] = $this->db->VARS['base_url'];
  }

//
// Are they trying to make a comment on a post that actualy exists?
//

  function is_valid_id()
  {
    $this->db->query("SELECT id FROM ".LAZ_TABLE_PREFIX."_data WHERE (id = '".$this->id."') AND (accepted = 1)");
    $this->db->fetch_array($this->db->result);
    return ($this->db->record) ? true : false;
  }

//
// Generate the comments form if thats what we want to do
//

  function comment_form($extra_html = '')
  {
    global $GB_PG, $gbsession, $rid, $uid, $included; //, $include_path;
    if (($this->db->VARS['disablecomments'] != 1) && ((!empty($_GET['gbsession'])) && (!empty($_GET['uid']))))
    {
      $GB_PG['comment'] = $GB_PG['comment'].'?gbsession='.$gbsession.'&amp;rid='.$rid.'&amp;uid='.$uid.'&amp;included='.$included;
    }
    $this->db->query("SELECT x.*, y.p_filename, y.width, y.height, z.comments from ".LAZ_TABLE_PREFIX."_data x left join ".LAZ_TABLE_PREFIX."_pics y on (x.id=y.msg_id and y.book_id=2) left join ".LAZ_TABLE_PREFIX."_com z on (x.id=z.id) WHERE (x.accepted='1' AND x.id=".$this->id.") LIMIT 1");
    $row = $this->db->fetch_array($this->db->result);
    $LANG =& $this->db->LANG;
    $VARS =& $this->db->VARS;
    if (isset($_COOKIE['lang']) && !empty($_COOKIE['lang']) && file_exists(LAZ_INCLUDE_PATH.'/lang/codes-'.$_COOKIE['lang'].'.php'))
    {
      $LANG_CODES = $GB_PG['base_url'].'/lang/codes-'.$_COOKIE['lang'].'.php';
    }
    elseif (file_exists(LAZ_INCLUDE_PATH.'/lang/codes-'.$VARS['lang'].'.php'))
    {
      $LANG_CODES = $GB_PG['base_url'].'/lang/codes-'.$VARS['lang'].'.php';
    }
    else
    {
      $LANG_CODES = $GB_PG['base_url'].'/lang/codes-english.php';
    }
    $antispam = $this->db->VARS['antispam_word'];
    $HTML_CODE = ($this->db->VARS['allow_html'] == 1) ? $this->db->LANG['BookMess2'] : $this->db->LANG['BookMess1'];
    $AG_CODE = ($this->db->VARS['agcode'] == 1) ? '<a href="'.$LANG_CODES.'?show=agcode" onclick="openCentered(\''.$LANG_CODES.'?show=agcode\',\'_codes\',640,450,\'scrollbars=yes\')" target="_codes">'.$this->db->LANG['FormMess3'].'</a>' : $this->db->LANG['FormMess6'];
    $SMILE_CODE = ($this->db->VARS['smilies'] == 1) ? $this->db->LANG['FormMess2'] : $this->db->LANG['FormMess7'];
    $DATE = $this->db->DateFormat($row['date']);
    $MESSAGE = nl2br($row['comment']);
    $id = $this->id;
    $bgcolor = $this->db->VARS['tb_color_1'];
    $COMMENT ='';
    if ($row['p_filename'] && preg_match('/^img-/',$row['p_filename']))
    {
      $img = new gb_image();
      $img->set_border_size($this->db->VARS['img_width'], $this->db->VARS['img_height']);
      $new_img_size = $img->get_img_size_format($row['width'], $row['height']);
      $row['p_filename2'] = $row['p_filename'];
      $GB_UPLOAD = 'public';
      if (file_exists($this->path . '/public/t_' . $row['p_filename']))
      {
        $row['p_filename2'] = 't_'.$row['p_filename2'];
      }      
      eval("\$USER_PIC = \"".$this->template->get_template('user_pic')."\";");
    }
    else
    {
      $USER_PIC = '';
    }
    $row['name'] = $this->db->CensorBadWords($row['name']);            
    $MESSAGE = $this->db->CensorBadWords($MESSAGE);            
    if ($this->db->VARS['smilies'] == 1)
    {
      $MESSAGE = $this->db->emotion($MESSAGE);
      $LAZSMILEYS = $this->db->generate_smilies();
    }
    else
    {
      $LAZSMILEYS = '';
    }
    if ($row['url'] && ($this->db->VARS['allow_url'] == 1))
    {
      $row['url'] = $this->db->CensorBadWords($row['url']); 
      eval("\$URL = \"".$this->template->get_template('url')."\";");
    }
    else
    {
      $URL = '';
    }
    if ($row['location'] && ($this->db->VARS['allow_loc'] == 1))
    {
      $row['location'] = $this->db->CensorBadWords($row['location']);
      $THEIRLOC = urlencode($row['location']);
      eval("\$LOCATION = \"".$this->template->get_template('location')."\";");
    }
    else
    {
      $LOCATION = '';
    }    
    if ($row['icq'] && ($this->db->VARS['allow_icq'] == 1) && ($row['icq'] != 0))
    {
      eval("\$ICQ = \"".$this->template->get_template('icq')."\";");
    }
    else
    {
      $ICQ = '';
    }
    if ($row['aim'] && ($this->db->VARS['allow_aim'] == 1))
    {
      eval("\$AIM = \"".$this->template->get_template('aim')."\";");
    }
    else
    {
      $AIM = '';
    }
    if ($row['msn'] && ($this->db->VARS['allow_msn'] == 1))
    {
      eval("\$MSN = \"".$this->template->get_template('msn')."\";");
    }
    else
    {
      $MSN = '';
    }
    if ($row['yahoo'] && ($this->db->VARS['allow_yahoo'] == 1))
    {
      eval("\$YAHOO = \"".$this->template->get_template('yahoo')."\";");
    }
    else
    {
      $YAHOO = '';
    }
    if ($row['skype'] && ($this->db->VARS['allow_skype'] == 1))
    {
      eval("\$SKYPE = \"".$this->template->get_template('skype')."\";");
    }
    else
    {
      $SKYPE = '';
    }    
    if ($row['email'])
    {
      $GRAVATAR = ($this->db->VARS['use_gravatar'] == 1) ? ' background: transparent url(http://www.gravatar.com/avatar/' . md5($row['email']) . '?s=24&amp;d=wavatar&amp;r=G) no-repeat right;' : '';
      if($this->db->VARS['require_email'] < 2)
      {
        $row['email'] = $this->db->CensorBadWords($row['email']); 
        if ($this->db->VARS['encrypt_email'] == 1)
        {
          $MAILTO = $this->db->html_encode('mailto:'.$row['email']);
        }
        else
        {
          $MAILTO = 'mailto:' . $row['email'];
        }
        eval("\$EMAIL = \"".$this->template->get_template('email')."\";");
      }
      else
      {
        $EMAIL = '';
      }
    }
    else
    {
      $GRAVATAR = '';
      $EMAIL = '';
    }
    if ($this->db->VARS['allow_gender'] == 1)
    {
      if ($row['gender'] == 'f')
      {
        eval("\$GENDER = \"".$this->template->get_template('img_female')."\";");
      }
      elseif ($row['gender'] == 'm')
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
      $hostname = (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $row['host']) ) ? 'IP' : 'Host';
      $HOST = '<em style="font-weight: normal;">'.$hostname.': '.$row['host']."</em>\n";
    }
    else
    {
      $HOST='';
    }
    $TIMEHASH = $this->db->generate_timehash();
    $TimehashTag = '<input type="hidden" name="gb_timehash" value="'.$TIMEHASH.'" />';
    $EXTRAJS = '';
    $OPTIONS[] ='';
    $EMAILREQ = '';
    $EMAILDISPLAYED = ($this->db->VARS['require_email'] > 2) ? $LANG['FormEmailDisplay'] : '';
    if ((($this->db->VARS['require_email'] == 1) || ($this->db->VARS['require_email'] == 4)))
    {
      $EXTRAJS .= 'document.getElementById(\'gb_email\').value = trim(document.getElementById(\'gb_email\').value);
        if(document.getElementById(\'gb_email\').value == "") {
          errorStyling(\'gb_email\');
          errorMessages[errorNum++] = "'.$LANG['ErrorPost12'].'";
        }';
      $EMAILREQ = '*';
    }
    $footerJS = '';
    if ($this->db->VARS['require_email'] != 2)
    {
      eval("\$OPTIONS['email'] = \"".$this->template->get_template('form_email')."\";");
    }
    if($this->db->VARS['honeypot'] == 1)
    {
      $honeypot = '<br /><span id="gb_username"><input type="checkbox" name="gb_username" value="1" /> Spammer?</span><br />';
      $footerJS .= "document.getElementById('gb_username').style.display = 'none';\n";
    }
    else
    {
      $honeypot = '';
    }
    if ($this->db->VARS['need_pass'] == 1)
    {
      $com_question = '';
      if ($this->db->VARS['com_question'] != '') 
      {
        $com_question =  $this->db->VARS['com_question']."<br />\n";
        // This is a bit of a hack but means only editing this file and ot the template as well
        $LANG['FormPass'] = $LANG['FormBot'];
      }
      $EXTRAJS .= 'document.getElementById(\'gb_bottest\').value = trim(document.getElementById(\'gb_bottest\').value);
        if(document.getElementById(\'gb_bottest\').value == "") {
          errorStyling(\'gb_bottest\');
          errorMessages[errorNum++] = "'.$LANG['ErrorPost13'].'";
        }';      
      eval("\$OPTIONS['antibot'] = \"".$this->template->get_template('com_pass')."\";");
    }
    elseif ($this->db->VARS['need_pass'] == 2)
    {
      if($this->db->VARS['solve_media'] == 1)
      {
        $EXTRAJS .= 'document.getElementById(\'adcopy_response\').value = trim(document.getElementById(\'adcopy_response\').value);
        if(document.getElementById(\'adcopy_response\').value == "") {
          errorStyling(\'adcopy_response\');
          errorMessages[errorNum++] = "'.$LANG['ErrorPost13'].'";
        }';
        require_once(LAZ_INCLUDE_PATH . '/solvemedialib.php'); //include the Solve Media library 
        $SolveMedia = solvemedia_get_html('G8vem0b2VDBXju20c9OwHO7makkjC9-o');	//outputs the widget
        eval("\$OPTIONS['antibot'] = \"".$this->template->get_template('form_captcha2')."\";");
      }
      else
      {
        $EXTRAJS .= 'document.getElementById(\'gb_bottest\').value = trim(document.getElementById(\'gb_bottest\').value);
        if(document.getElementById(\'gb_bottest\').value == "") {
          errorStyling(\'gb_bottest\');
          errorMessages[errorNum++] = "'.$LANG['ErrorPost13'].'";
        }';
        $footerJS .= "document.getElementById('captchaReload').style.display = 'block';\nreloadCaptcha();";
        eval("\$OPTIONS['antibot'] = \"".$this->template->get_template('form_captcha')."\";");
      }
    }
    $OPTIONAL = implode("\n",$OPTIONS);  
    $GB_COMMENT = '#';
    $GB_ENTRY = '';
    $display_tags = $this->db->create_buttons($LANG_CODES);
    if ($row['comments'])
    {
      $coms = $this->db->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_com WHERE id='".$this->id."' AND comaccepted='1' order by com_id asc");
      while ($com = $this->db->fetch_array($coms))
      {
        $COMDATE = $this->db->DateFormat($com['timestamp']);
        $comhostname = (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $com['host'])) ? 'IP' : 'Host';
        $comhost = ($this->db->VARS['show_ip'] == 1) ? '<i>'.$comhostname.': '.$com['host']."</i><br />\n" : '';
        $com['comments'] = ($this->db->VARS['smilies'] == 1) ? nl2br($this->db->emotion($com['comments'])) : nl2br($com['comments']);
        $com['name'] = $this->db->CensorBadWords($com['name']);
        $com['comments'] = $this->db->CensorBadWords($com['comments']);
        $COMEMAIL = '';
        $COMGRAVATAR = '';
        $template['com_email'] = $this->template->get_template('com_email');
        if (!empty($com['email']))
        {
           $COMGRAVATAR = ($this->db->VARS['use_gravatar'] == 1) ? 'padding-left:26px;background: url(http://www.gravatar.com/avatar/' . md5($com['email']) . '?s=24&amp;d=wavatar&amp;r=G) no-repeat;' : '';
           if($this->db->VARS['require_email'] < 2)
           {
             $template['com_email'] = $this->template->get_template('com_email');
             $com['email'] = $this->db->CensorBadWords($com['email']); 
             if ($this->db->VARS['encrypt_email'] == 1)
             {
               $COMMAILTO = $this->db->html_encode('mailto:' . $com['email']);
             }
             else
             {
               $COMMAILTO = 'mailto:' . $com['email'];
             }
             eval("\$COMEMAIL = \"" . $template['com_email']."\";");
          }
          else
          {
            $COMEMAIL = '';
          }
        }        
        eval("\$COMMENT .= \"".$this->template->get_template('com')."\";");
      }
    }     
    $theirbrowser = $this->db->browser_detect($row['browser']);
    $comment_html = '';
    $COMMENTLINK = '';
    $PERMALINK = '';
    eval("\$GB_ENTRY = \"".$this->template->get_template('entry')."\";");
    $GB_ENTRY .= $TimehashTag;
    eval("\$comment_html = \"".$this->template->get_template('header')."\";");
    eval("\$comment_html .= \"".$this->template->get_template('comment')."\";");
    eval("\$comment_html .= \"".$this->template->get_template('footer')."\";");
    return $comment_html;
  }


//
// Check the submitted comment to make sure it's nice an clean and do some formatting
//

  function check_comment()
  {
    $the_time = time();
    $this->comment = htmlspecialchars($this->db->FormatString($this->comment));
    $this->user = htmlspecialchars($this->db->FormatString($this->user));
    $this->email = htmlspecialchars($this->db->FormatString($this->email));
    
    if (empty($this->timehash))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost4'].' (4)', 5));
    }
    
    if($this->db->VARS['honeypot'] == 1)
    {
      if($this->honeypot == 1)
      {
        sleep(20);
        return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost10'], 1));
      }
    }
    
    if (($this->db->VARS['need_pass'] == 1) && empty($this->pass_comment)) 
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost13'], 3));
    }
    elseif (($this->db->VARS['need_pass'] == 2) && empty($this->bottest)) 
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost13'], 3));
    }
    
    if($this->db->VARS['check_headers'] == 1)
    {
      if(($failedHeader = $this->db->check_headers(2, $this->ip)) != 0)
      {
        return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost4'].' (5.'.$failedHeader.')', 6));
      }
    }
    
    if(get_magic_quotes_gpc())
    {
      $this->user = stripslashes($this->user);
      $this->email = stripslashes($this->email);
      $this->comment = stripslashes($this->comment);
    }
    
    if (!$this->db->check_emailaddress($this->email))
    {
      $this->email = '';
    }
    
    if (empty($this->comment))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost11']));
    }
    
    if (empty($this->user))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost1']));
    }
    
    if ((($this->db->VARS['require_email'] == 1) || ($this->db->VARS['require_email'] == 4)) && $this->email == '')
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost12']));
    }
    
    if ($this->db->VARS['need_pass'] == 1)
    {
      if (strtolower($this->db->VARS['comment_pass']) != strtolower($this->pass_comment))
      {
        return $this->comment_form($this->db->gb_error($this->db->LANG['PassMess3'], 4));
      }
    }
    elseif ($this->db->VARS['need_pass'] == 2)
    {
      if (($this->db->VARS['solve_media'] == 0) && (!$this->db->captcha_test($this->bottest, $this->timehash)))
      {
        return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost14'], 4));
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
        	return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost14'], 4),0,1);
        }
      }      
    }
    
    $decodedhash = $this->db->generate_timehash($this->timehash);
    
    if (($the_time < ($decodedhash + $this->db->VARS['post_time_min'])) && ($this->db->VARS['post_time_min'] != 0))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost15']));
    }
    
    if (($the_time > ($decodedhash + $this->db->VARS['post_time_max'])) && ($this->db->VARS['post_time_max'] != 0))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost16']));
    }
    
    if (!$this->db->CheckWordLength($this->user))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost4'].' (3)'));
    }
    
    if (strlen($this->comment)<$this->db->VARS['min_text'])
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost3']));
    }
    
    if (strlen($this->comment)>$this->db->VARS['max_text'])
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost17']));
    }
    
    if (!$this->db->CheckWordLength($this->comment))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost10']));
    }
    
    if ($this->db->BlockBadWords($this->user) || $this->db->BlockBadWords($this->email) || $this->db->BlockBadWords($this->comment))
    {
      return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost10'], 7));
    }
    
    if ($this->db->VARS['max_url'] < 99)
    {
      if ($this->db->urlCounter($this->comment) > $this->db->VARS['max_url'])
      {
        return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost10'], 8));
      }
    }
    
    if ($this->db->VARS['flood_check'] == 1)
    {
      if ($this->db->FloodCheck($this->ip))
      {
        return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost8']));
      }
    }
    
    if (($this->db->VARS['banned_ip'] == 1) || ($this->db->VARS['sfs_confidence'] > 0))
    {
      $banned = $this->db->isBannedIp($this->ip, $this->db->VARS['banned_ip'], $this->db->VARS['sfs_confidence']);
      if ($banned == 1)
      {
        return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost9'], 2));
      }
      elseif ($banned == 2)
      {
        return $this->comment_form($this->db->gb_error($this->db->LANG['ErrorPost9'], 9));
      }
    }
    
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
          return $this->comment_form($this->db->gb_error($errorMessage . ' (sfs)', 9));
        }
      }
    }
    if ($this->db->VARS['allow_html'] == 1)
    {
      $this->comment = $this->db->allowed_html($this->comment);
    }
    if ($this->db->VARS['agcode'] == 1)
    {
      $this->comment = $this->db->AGCode($this->comment);
    }
    return 1;
  }

//
// Insert the formatted comment into our database
//

  function insert_comment()
  {
    global $GB_PG;
    $the_time = time();
    $LANG =& $this->db->LANG;
    $this->user = $this->db->escape_string($this->user);
    //$this->comment = $this->db->escape_string($this->comment);
    $this->email = $this->db->escape_string($this->email);
    $host = $this->db->escape_string(htmlspecialchars(gethostbyaddr($this->ip)));
    if ($this->db->VARS['require_comchecking'] == 1)
    {
      $this->accepted = 0;
    }
    if ($this->db->BlockBadWords($this->user,3) || $this->db->BlockBadWords($this->email,3) || $this->db->BlockBadWords($this->comment,3))
    {
      $this->accepted = 0;
    }
    $this->db->query("INSERT INTO ".LAZ_TABLE_PREFIX."_com (id,name,email,comments,host,timestamp,comaccepted,ip) VALUES ('$this->id','$this->user','$this->email','" . $this->db->escape_string($this->comment) . "','$host','$the_time','$this->accepted','$this->ip')");
    $entry_id = $this->db->insert_id();
    $this->db->query("SELECT x.*, z.comments from ".LAZ_TABLE_PREFIX."_data x left join ".LAZ_TABLE_PREFIX."_com z on (x.id=z.id) WHERE (x.id=".$this->id.") LIMIT 1");
    $original = $this->db->fetch_array($this->db->result);
    $prev_comments = '';
    $prev_coms = '';
    if ($original['comments'])
    {
      $this->db->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_com WHERE id='".$this->id."' AND comaccepted='1' order by com_id asc");
      while ($com = $this->db->fetch_array($this->db->result))
      {
        $prev_coms[] = "<br>\n<br>\n<b>".$com['name'].":</b><br>\n".$com['comments'];;
      }
      if(is_array($prev_coms) && (count($prev_coms) > 1))
      {
        array_pop($prev_coms);
        $prev_comments = implode('', $prev_coms);
      }
      else
      {
        $prev_comments = '';
      }
    }
    $hostname = ( preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $host) ) ? 'IP' : 'Host';
    $messagebody  = '<div style="background-color:#fff;border:1px solid #a5cae4;border-radius:5px;padding:5px;">';
    $messagebody .= $LANG['FormName'].': ' . $this->user . "<br />\n";
    $messagebody .= ($this->email != '') ? $LANG['FormEmail'].': <a href="mailto:'.$this->email.'">'.$this->email."</a><br />\n" : '';
    $messagebody .= $hostname.': ' . $host .' ('.$this->ip.")<br />\n<br />\n";
    $messagebody .= '<hr style="min-height:1px;margin:20px 0 10px;border:0;color:#d7edfc;background-color:#d7edfc" />';
    $messagebody .= $LANG['EmailMess2'] . ":<br />\n".nl2br($this->comment)."<br>\n<br>\n";
    $messagebody .= $LANG['EmailMess1'] . ":<br />\n<br />\n<b>" . $original['name'] . ":</b><br>\n" . $original['comment'] . "<br />\n<br />\n";
    if (!empty($prev_comments))
    {
      $messagebody .= $LANG['BookMess7'] . $prev_comments . "<br>\n<br>\n";
    }
    if ($GB_PG['base_url'] != '')
    {
      $messagebody .= "<br />\n<br />\n<div style=\"background-color:#f0f7fc;border-top:1px solid #d7edfc;padding:2px;\">";
      $messagebody .= "<br />\n<br />\n";
      $urlDivider = (strpos($this->db->VARS['laz_url'], '?') > 0) ? '&' : '?';
      $messagebody .= $LANG['EmailAdminComSubject'].': <a href="' . $this->db->VARS['laz_url'] .  $urlDivider . 'permalink=true&entry=' . $this->id . '">'.$this->db->VARS['laz_url']. $urlDivider .'permalink=true&entry=' . $this->id . "</a><br>\n";      
      $messagebody .= ($this->accepted == 0) ? $LANG['AdminAccept'] . ': <a href="'.$GB_PG['admin'] . '?action=accept&tbl=com&id=' . $entry_id . '">'.$GB_PG['admin'].'?action=accept&tbl=com&id=' . $entry_id . "</a><br />\n" : $LANG['AdminUnaccept'] . ': <a href="' . $GB_PG['admin'] . '?action=unaccept&tbl=com&id=' . $entry_id . '">' . $GB_PG['admin'] . '?action=unaccept&tbl=com&id=' . $entry_id . "</a><br />\n"; 
      $messagebody .= $LANG['AdminEdit'].': <a href="'.$GB_PG['admin'].'?action=edit&tbl=com&id='.$entry_id.'">'.$GB_PG['admin'].'?action=edit&tbl=com&id='.$entry_id."</a><br />\n";
      $messagebody .= $LANG['AdminDelete'].': <a href="'.$GB_PG['admin'].'?action=del&tbl=com&id='.$entry_id.'">'.$GB_PG['admin'].'?action=del&tbl=com&id='.$entry_id."</a><br />\n";
      $messagebody .= $LANG['FormSelect'].': <a href="'.$this->db->VARS['laz_url'].'">'.$this->db->VARS['laz_url']."</a><br>\n";
      $messagebody .= '</div>';
    }
    $messagebody .= '</div>';
    $messagebody = stripslashes($messagebody);
    $admin_emails = explode(',', $this->db->VARS['admin_mail']);
    if ($this->db->check_emailaddress($this->db->VARS['book_mail']) && ($this->db->VARS['always_bookemail'] == 1))
    {
      $admin_email = $this->db->VARS['book_mail'];
    }
    else
    {
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
    if (($this->db->VARS['notify_admin_com'] == 1) || ($this->db->VARS['require_comchecking'] == 1))
    {
      $this->user = $this->db->undo_htmlspecialchars($this->user);
      $this->user = str_replace('"', '&quot;', $this->user);
      foreach ($admin_emails as $adminsaddy)
      {
        $adminsaddy = trim($adminsaddy);
        if ($this->db->check_emailaddress($adminsaddy))
        {
          if ($this->db->VARS['always_bookemail'] && !empty($this->db->VARS['book_mail']))
          {
            $this->db->send_email($adminsaddy,$this->db->LANG['EmailAdminComSubject'],$messagebody, 'From: "'.$this->user.'" <'.$this->db->VARS['book_mail'].'>', $this->db->VARS['book_mail']);
          }
          else
          {
            $this->db->send_email($adminsaddy,$this->db->LANG['EmailAdminComSubject'],$messagebody, 'From: "'.$this->user.'" <' . $from_email . '>', $from_email);
          }
        }
      }
    }
    if (($this->db->VARS['notify_guest'] == 1) && ($this->email != '') && ($admin_email != ''))
    {
      $email_message = nl2br($this->db->VARS['notify_mes']);
      $email_message = str_replace('[NAME]', stripslashes($this->user), $email_message);
      $this->db->send_email($this->email,$this->db->LANG['EmailGuestSubject'], $email_message, 'From: "' . strip_tags($this->db->VARS['book_name']) . '" <' . $admin_email . '>', $admin_email);
    }    
    $this->db->query("INSERT INTO ".LAZ_TABLE_PREFIX.'_ip'." (guest_ip,timestamp) VALUES ('$this->ip','$the_time')");
  }

  function comment_action($action = '')
  {
    global $GB_PG, $IS_INCLUDE, $gbsession, $uid, $rid, $included;
    $this->id = intval($this->id);
    if ($this->id && $this->is_valid_id() && $action == 1)
    {
      $status = $this->check_comment();
      if ($status == 1)
      {
        $this->insert_comment();
        $LANG =& $this->db->LANG;
        $VARS =& $this->db->VARS;
        $success_message = $LANG['BookMess10'];
        if ($this->accepted == 0)
        {
          $success_message = $LANG['BookMess11'];
        }
        if(($this->db->VARS['disablecomments'] != 1) && ((!empty($_GET['gbsession'])) && (!empty($_GET['uid']))))
        {
          $GB_PG['index'] = $GB_PG['admin'].'?action=show&amp;tbl=gb&amp;gbsession='.$gbsession.'&amp;rid='.$rid.'&amp;uid='.$uid.'&amp;included='.$included;
        }
        define('IS_SUCCESS', true);
        $success_html = '';
        eval("\$success_html .= \"".$this->template->get_template('success_header')."\";");
        eval("\$success_html .= \"".$this->template->get_template('success')."\";");
        eval("\$success_html .= \"".$this->template->get_template('footer', false)."\";");
        echo $success_html;
      }
      else
      {
        echo $status;
      }
    }
    elseif ($this->id && $this->is_valid_id())
    {
      echo $this->comment_form();
    }
    else
    {
      if (IS_INCLUDE)
      {
        echo ("<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=".$GB_PG['index']."\">");
      }
      else
      {
        header("Location: $GB_PG[index]");
      }
    }
  }

}

?>