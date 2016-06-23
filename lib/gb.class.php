<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 20:03:55 GMT
 * ----------------------------------------------
 */

class guestbook
{

  var $total;
  var $db;
  var $template;
  var $path;
  var $searchtext = '';
  var $searchfield = '';
  var $searchquery = '';
  var $searchquery2 = '';
  var $postsearch = '';
  var $getsearch = '';

  function __CONSTRUCT($path = '')
  {
	  global $GB_PG;
    $this->db = new guestbook_vars($path);
    $this->db->getVars();
    $this->total = 0;
    $this->path = $path;
    $this->template = &$this->db->template;
    $GB_PG['base_url'] = $this->db->VARS['base_url'];
  }
  
//
// Generate our next page/last page links
//  

  function get_nav($totalentries,$entry)
  {
    global $_SERVER, $GB_PG;
    $VARS =& $this->VARS;
    $self = (preg_match('/\?/',$GB_PG['index'])) ? $GB_PG['index'].'&amp;entry=' : $this->db->VARS['laz_url'].'?entry=';
    $entriesperpage = $this->db->VARS['entries_per_page']; // How many entries to be displayed on a page
    $totalpages = ceil($totalentries/$entriesperpage); // How high do the links go?
    if ($totalentries <= $entriesperpage) // Check we have enough entries to make more than one page
    {
     return '[1]';
    }
    $currentpage = (!empty($entry)) ? ceil(($entry + $entriesperpage) / $entriesperpage) : 1; // What page we on?
    $loopstart = ($currentpage > 3) ? $currentpage - 2 : 1;
    $loops = ($totalpages > 4) ? 5 : $totalpages;
    if ($loopstart == 2)
    {
      $pagination = '<a href="'.$self.'0'.$this->getsearch.'">1</a>';
    }
    elseif ($loopstart > 2)
    {
      $pagination = '<a href="'.$self.'0'.$this->getsearch.'">1</a> ...';
    }
    else
    {
      $pagination = '';
    }
    for ($i = $loopstart; $i < ($loopstart + $loops); $i++)
    {
      if ($i < 1)
      {
        continue;
      }
      if (($i > $totalpages) || ($i > $currentpage + 2))
      {
        break;
      }
      if ($i == $currentpage)
      {
        $pagination .= ' ['.$i.']';
      }
      else
      { 
        $pagination .= ' <a href="'.$self.(($i - 1) * $entriesperpage).$this->getsearch.'">'.$i.'</a>';
      }
    }
    if ($loopstart < ($totalpages - $loops))
    {
      $pagination .= ' ... <a href="'.$self.(($totalpages * $entriesperpage) - $entriesperpage).$this->getsearch.'">'.$totalpages.'</a>';
    }
    elseif ($loopstart == ($totalpages - $loops))
    {
      $pagination .= ' <a href="'.$self.(($totalpages * $entriesperpage) - $entriesperpage).$this->getsearch.'">'.$totalpages.'</a>';
    }
    return $pagination;
  }  

  function show_entries($entry = 0)
  {
   global $GB_PG;
   $LANG =& $this->db->LANG;
   $VARS =& $this->db->VARS;
   $BACK2BOOK = '';
   $searching = 0;
   if (!empty($this->searchtext) && !empty($this->searchfield) && $this->db->VARS['allow_search'])
   {
    $this->searchfield = htmlspecialchars($this->searchfield);
    $validfield = ($this->searchfield == 'comment') ? 1 : 0;
    $validfield = ($this->searchfield == 'name') ? 1 : $validfield;
    $validfield = (($this->searchfield == 'location') && ($this->db->VARS['allow_loc'] == 1)) ? 1 : $validfield;
    if ($validfield == 1)
    {
      $entry = (isset($_POST['search'])) ? 0 : $entry;
      $searching = 1;
      if (get_magic_quotes_gpc())
      {
       $this->searchtext = stripslashes($this->searchtext);
      }
      $this->searchqueries = $this->create_searcharray($this->searchtext);
      $theQuery = $this->create_searchquery($this->searchqueries, $this->searchfield);
      if (!empty($theQuery))
      {
       $this->searchquery = ' AND '.str_replace('x.', '', $theQuery );
       $this->searchquery2 = ' AND '.$theQuery;
       $this->searchtext = urlencode($this->db->undo_htmlspecialchars(stripslashes($this->searchtext)));
       $this->postsearch = '<input type="hidden" name="searchfield" value="'.$this->searchfield.'" />
       <input type="hidden" name="searchtext" value="'.$this->searchtext.'" />';
       $this->getsearch = '&amp;searchfield='.$this->searchfield.'&amp;searchtext='.$this->searchtext;
       $BACK2BOOK = '<strong><a href="'.$GB_PG['index'].'">'.$LANG['BookMess4'].'</a></strong>';
      }
    }
   }   
   $this->db->fetch_array($this->db->query("select count(*) as total from ".LAZ_TABLE_PREFIX."_data WHERE accepted='1'".$this->searchquery));
   $this->total = $this->db->record['total'];
   $TPL = $this->get_entries($entry,$this->db->VARS['entries_per_page']);
   $TPL['GB_TOTAL'] = $this->total;
   $searchmessage = (!$this->total && $searching) ? '<div style="font-family:'.$VARS['font_face'].';text-align: center;font-weight:bold;">'.$LANG['NoResults'].'</div>' : '';  
   // $TPL['GB_JUMPMENU'] = implode("\n",$this->generate_JumpMenu());
   $TPL['GB_TIME'] = $this->db->DateFormat(time(), 1);
   $TPL['GB_NAVIGATION'] = $this->get_nav($this->total,$entry);
   $TPL['GB_HTML_CODE'] = ($this->db->VARS['allow_html'] == 1) ? $this->db->LANG['BookMess2'] : $this->db->LANG['BookMess1'];
   $SEARCH = ($this->db->VARS['allow_search']) ? $this->generate_search($BACK2BOOK,$searchmessage) : '';
   if(isset($_GET['permalink']))
   {
    $TOPLINK = '<a href="'.$this->db->VARS['laz_url'].'"><strong>'.$LANG['BookMess4'].'</strong></a>';
    $TPL['GB_NAVIGATION'] = '';
    $SEARCH = '';
   }
   else
   {
    $TOPLINK = '<a href="'.$GB_PG['addentry'].'" rel="nofollow"><strong>'.$LANG['BookMess3'].'</strong></a>';
   }
   $guestbook_html = '';
   eval("\$guestbook_html = \"".$this->template->get_template('header')."\";");
   eval("\$guestbook_html .= \"".$this->template->get_template('body')."\";");
   eval("\$guestbook_html .= \"".$this->template->get_template('footer')."\";");
   return $guestbook_html;
  }
  
//
// Generate the drop down jump menu
//  

  function generate_JumpMenu()
  {
   $menu_array[] = '<select name="entry" class="select">';
   $menu_array[] = '<option value="0" selected="selected">'.$this->db->LANG['FormSelect'].'</option>';
   if ($this->db->VARS['entries_per_page'] < $this->total)
   {
    $remain = $this->total % $this->db->VARS['entries_per_page'];
    $i = $this->total-$remain;
    if ($remain > 0)
    {
      $menu_array[] = '<option value="0">'.$i.'-'.$this->total.'</option>';
    }    
    while ($i > 0)
    {
      $num_max = $i;
      $num_min = $num_max-$this->db->VARS['entries_per_page'];
      $num_min++;
      $menu_array[] = '<option value="'.$remain.'">'.$num_min.'-'.$num_max.'</option>';
      $i = $num_min-1;
      $remain += $this->db->VARS['entries_per_page'];
    }
   }
   $menu_array[] = '</select>';
   $menu_array[] = $this->postsearch;
   $menu_array[] = '<input type="submit" value="'.$this->db->LANG['FormButton'].'" class="input" />';
   return $menu_array;
  }
  
//
// Generate our search list
//

  function generate_search($BACK2BOOK ='', $SEARCHMESSAGE = '')
  {
   $VARS =& $this->db->VARS;
   $LANG =& $this->db->LANG;
   $LOCATIONFIELD = ($this->db->VARS['allow_loc'] == 1) ? '<option value="location">'.$LANG['FormLoc'].'</option>' : '';
   $SEARCH = '';
   $SEARCHED = '';
   $RESULTMESSAGE = '';
   $template['search'] = $this->template->get_template('search');
   eval("\$SEARCH = \"".$template['search']."\";");
   return $SEARCH; 
  }
  
//
// Parses the search query and turns it ito something useful
//  
  
  function create_searcharray($searchstring)
  {
   $tmpstr = ''; // Just a buffer
   if ($searchstring == '') // If no data don't waste our time
   {
    return '';
   }
   $searchstring = str_replace('%', '\%', $searchstring); // Replace special LIKE characters
   $searchstring = str_replace('_', '\_', $searchstring); // Replace special LIKE characters
   if (strpos($searchstring, '"') === false) // If there are no " we can keep it simple
   {
    $searcharray = explode(' ', $searchstring);
   }
   else  // If " are present though
   {
    $searcharray = array();
    $quotecount = 0; // Indicates if we found a quote or not
    for ($i=0;$i<strlen($searchstring);$i++)
    {
      if ($searchstring[$i] == ' ' && ($quotecount == 0)) // Found a space not inside a quote
      {
       if ($tmpstr != '')  // If buffers not empty
       {
        $searcharray[] = $tmpstr;  // Add it to the search array
        $tmpstr = '';
       }
      }
      elseif ($searchstring[$i] == '"') // If we find a quote
      {
       if ($tmpstr != '')
       {
        $searcharray[] = $tmpstr; // If buffer is not empty add it to the array
        $tmpstr = '';
       }
       $quotecount = ($quotecount == 0) ? 1 : 0; // Change our marker
      }
      else
      {
       $tmpstr .= $searchstring[$i]; // Anything left we dump in the array.
      }
    }
   }
   if ($tmpstr != '')
   {
    $searcharray2 = explode(' ', $tmpstr); // Dump remainder into a new array splitting at spaces
    foreach ($searcharray2 as $key=>$value) 
    {
      $searcharray[] = $value;	
    }
   }
   return $searcharray;
  }

//
// Creates the extra part of the query if doing a search
//

  function create_searchquery($searcharray,$searchfield)
  {
   for ($i=0;$i<count($searcharray);$i++)
   {
   	if (trim($searcharray[$i]) != '') // Check the item contains data
   	{
   	  $searcharray2[] = 'x.'.$searchfield.' LIKE "%'.addslashes($searcharray[$i]).'%"'; // Make it into a LIKE and add to second array
   	}
   }
   if (is_array($searcharray2))
   {
    return implode(' OR ', $searcharray2); // Turn second array into a string
   }
   else
   {
    return false;
   }
  }  
  
//
// Retrieve and format our entries for dislaying
//

  function get_entries($entry,$last_entry)
  {
   global $GB_UPLOAD, $GB_PG;
   $VARS =& $this->db->VARS;
   $last_entry = intval($last_entry);
   $img = new gb_image();
   $img->set_border_size($this->db->VARS['img_width'], $this->db->VARS['img_height']);
   $LANG =& $this->db->LANG;
   $id = (isset($_GET['permalink'])) ? 1 : $this->total-$entry;
   $HOST = '';
   $COMMENT = '';
   $GB_ENTRIES = '';
   $i = 0;
   $template['entry'] = $this->template->get_template('entry');
   $template['location'] = $this->template->get_template('location');
   $template['com'] = $this->template->get_template('com');
   $template['url'] = $this->template->get_template('url');
   $template['icq'] = $this->template->get_template('icq');
   $template['aim'] = $this->template->get_template('aim');
   $template['msn'] = $this->template->get_template('msn');
   $template['yahoo'] = $this->template->get_template('yahoo');
   $template['skype'] = $this->template->get_template('skype');
   $template['email'] = $this->template->get_template('email');
   $template['image'] = $this->template->get_template('user_pic');
   $template['male'] = $this->template->get_template('img_male');
   $template['female'] = $this->template->get_template('img_female');
   $template['com_link'] = $this->template->get_template('com_link');
   $template['com_email'] = $this->template->get_template('com_email');
   // Here we create our query dependant upon the admins options.
   if(isset($_GET['permalink'])) // && ($this->db->VARS['permalinks']))
   {
    $this->searchquery2 = ' AND x.id = '.$entry;
    $entry = 0;
    $last_entry = 1;
   }
   if($this->db->VARS['allow_img'] && ($this->db->VARS['disablecomments'] != 1)) // Images and comments allowed
   {
    $result = $this->db->query("SELECT x.*, y.p_filename, y.width, y.height, COUNT(z.com_id) as comments FROM ".LAZ_TABLE_PREFIX."_data x LEFT JOIN ".LAZ_TABLE_PREFIX."_pics y ON (x.id=y.msg_id and y.book_id=2) LEFT JOIN ".LAZ_TABLE_PREFIX."_com z ON (x.id=z.id) WHERE x.accepted='1'".$this->searchquery2." GROUP BY x.id ORDER BY x.id DESC LIMIT $entry, $last_entry");
   }
   elseif($this->db->VARS['allow_img'] && ($this->db->VARS['disablecomments'] == 1)) // Images allowed but not comments
   {
    $result = $this->db->query("SELECT x.*, y.p_filename, y.width, y.height FROM ".LAZ_TABLE_PREFIX."_data x LEFT JOIN ".LAZ_TABLE_PREFIX."_pics y ON (x.id=y.msg_id and y.book_id=2) WHERE x.accepted='1'".$this->searchquery2." GROUP BY x.id ORDER BY x.id DESC LIMIT $entry, $last_entry");
   }
   elseif(!$this->db->VARS['allow_img'] && ($this->db->VARS['disablecomments'] != 1)) // Comments allowed but not images
   {
    $result = $this->db->query("SELECT x.*, COUNT(z.com_id) as comments FROM ".LAZ_TABLE_PREFIX."_data x LEFT JOIN ".LAZ_TABLE_PREFIX."_com z ON (x.id=z.id) WHERE x.accepted='1'".$this->searchquery2." GROUP BY x.id ORDER BY x.id DESC LIMIT $entry, $last_entry");
   }
   else // No images and no comments
   {
    $result = $this->db->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_data WHERE accepted='1'".str_replace('x.', '', $this->searchquery2)." ORDER BY id DESC LIMIT $entry, $last_entry");
   }
   while ($row = $this->db->fetch_array($result)) // Loop through the results
   {
    // Check if they want to show their ad code or not.
    if (($this->db->VARS['ad_pos'] > 0) && ($this->db->VARS['ad_code'] != '') && ($this->db->VARS['ad_pos'] == ($i + 1)))
    {
      $GB_ENTRIES .= '<tr bgcolor="';
      $GB_ENTRIES .= ($i % 2) ? $this->db->VARS['tb_color_2'] : $this->db->VARS['tb_color_1'];
      $GB_ENTRIES .= '"><td colspan="2" align="center" class="font1">'.$this->db->VARS['ad_code'].'</td></tr>';
      $i++; 
    }
    $DATE = $this->db->DateFormat($row['date']); // Format the date according to their language
    $MESSAGE = nl2br($row['comment']); // Add <br /> where needed
    if (!empty($row['p_filename']) && preg_match('/^img-/',$row['p_filename'])) // If there is an image show it
    {
      $row['p_filename2'] = $row['p_filename'];
      if (file_exists($this->path.'/public/t_'.$row['p_filename']))
      {
       $row['p_filename2'] = 't_'.$row['p_filename2'];
      }
      $new_img_size = $img->get_img_size_format($row['width'], $row['height']);
      $GB_UPLOAD = 'public';
      eval("\$USER_PIC = \"".$template['image']."\";");
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
    }
/*    if (!$row['location'])
    {
      $row['location'] = '-';
    }*/
    $bgcolor = ($i % 2) ? $this->db->VARS['tb_color_2'] : $this->db->VARS['tb_color_1'];
    $i++;
    if ($row['url'] && ($this->db->VARS['allow_url'] == 1))
    {
      $row['url'] = $this->db->CensorBadWords($row['url']); 
      eval("\$URL = \"".$template['url']."\";");
    }
    else
    {
      $URL = '';
    }
    if ($row['location'] && ($this->db->VARS['allow_loc'] == 1))
    {
      $row['location'] = $this->db->CensorBadWords($row['location']);
      $THEIRLOC = urlencode($row['location']);
      eval("\$LOCATION = \"".$template['location']."\";");
    }
    else
    {
      $LOCATION = '';
    }
    if (($row['icq']) && ($this->db->VARS['allow_icq'] == 1) && ($row['icq'] != 0))
    {
      eval("\$ICQ = \"".$template['icq']."\";");
    }
    else
    {
      $ICQ = '';
    }
    if (($row['aim']) && ($this->db->VARS['allow_aim'] == 1))
    {
      eval("\$AIM = \"".$template['aim']."\";");
    }
    else
    {
      $AIM = '';
    }
    if (($row['msn']) && ($this->db->VARS['allow_msn'] == 1))
    {
      eval("\$MSN = \"".$template['msn']."\";");
    }
    else
    {
      $MSN = '';
    }
    if (($row['yahoo']) && ($this->db->VARS['allow_yahoo'] == 1))
    {
      eval("\$YAHOO = \"".$template['yahoo']."\";");
    }
    else
    {
      $YAHOO = '';
    }
    if (($row['skype']) && ($this->db->VARS['allow_skype'] == 1))
    {
      eval("\$SKYPE = \"".$template['skype']."\";");
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
        $MAILTO = 'mailto:'.$row['email'];
       }
       eval("\$EMAIL = \"".$template['email']."\";");
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
       eval("\$GENDER = \"".$template['female']."\";");
      }
      elseif ($row['gender'] == 'm')
      {
       eval("\$GENDER = \"".$template['male']."\";");
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
    $PERMALINK = '';
    if ($this->db->VARS['permalinks'] == 1)
    {
      $PERMALINK = (preg_match('/\?/',$GB_PG['index'])) ? $GB_PG['index'].'&amp;permalink=true&amp;entry='.$row['id'] : $GB_PG['index'].'?permalink=true&amp;entry='.$row['id'];
      $PERMALINK = '<a href="'.$PERMALINK.'"><img src="'.$GB_PG['base_url'].'/img/permalink.gif" width="14" height="14" alt="Permalink" title="Permalink" style="border:0;" /></a>';
    }
    $GB_COMMENT = (((IS_MODULE) || (IS_INCLUDE)) && preg_match('/\?/',$GB_PG['comment'])) ? $GB_PG['comment'].'&amp;gb_id='.$row['id'] : $GB_PG['comment'].'?gb_id='.$row['id'];
    if ($this->db->VARS['disablecomments'] == 0)
    {
      eval("\$COMMENTLINK = \"".$template['com_link']."\";");
    }
    else
    {
      $COMMENTLINK = '';
    }
    if ($this->db->VARS['show_ip'] == 1)
    {
      $hostname = (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $row['host'])) ? 'IP' : 'Host';
      $HOST = '<em style="font-weight: normal;">'.$hostname.': '.$row['host']."</em>\n";
    }
    if (!empty($row['comments']))
    {
      $foo = $this->db->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_com WHERE id='$row[id]' AND comaccepted='1' order by com_id asc");
      $comment_count = 0;
      while ($com = $this->db->fetch_array($foo))
      {
        $comment_count++;
        $COMDATE = $this->db->DateFormat($com['timestamp']);
        $comhostname = (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $com['host'])) ? 'IP' : 'Host';
        $comhost = ($this->db->VARS['show_ip'] == 1) ? '<em>'.$comhostname.': '.$com['host']."</em><br />\n" : '';
        $com['comments'] = ($this->db->VARS['smilies'] == 1) ? nl2br($this->db->emotion($com['comments'])) : nl2br($com['comments']);
        $com['name'] = $this->db->CensorBadWords($com['name']);
        $com['comments'] = $this->db->CensorBadWords($com['comments']);
        $COMGRAVATAR = '';
        $COMEMAIL = '';
        if (!empty($com['email']))
        {
           $COMGRAVATAR = ($this->db->VARS['use_gravatar'] == 1) ? 'padding-left:26px;background: url(http://www.gravatar.com/avatar/' . md5($com['email']) . '?s=24&amp;d=wavatar&amp;r=G) no-repeat;' : '';
           if($this->db->VARS['require_email'] < 2)
           {
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
        eval("\$COMMENT .= \"".$template['com']."\";");
      }
      unset($com);
      if (($this->db->VARS['hide_comments'] == 1) && ($comment_count > 0))
      {
        $COMMENTDIV = "<br style=\"clear:both;\" /><a href=\"javascript: ;\" onclick=\"toggleSlide('".$row['id']."', ".$comment_count.");\" style=\"clear:both;display:none;\" id=\"comtext_".$row['id']."\" class=\"comHide\">".$LANG['BookMess12']." (".$comment_count.")</a>";
        $COMMENTDIV .= "<div id=\"com".$row['id']."_open\" style=\"position:relative;\" class=\"comDiv\">";
        $COMMENTDIV .= $COMMENT;
        $COMMENTDIV .= '</div>';
        $COMMENT = $COMMENTDIV;
        unset($COMMENTDIV);
      }
    }     
    $theirbrowser = $this->db->browser_detect($row['browser']);
    eval("\$GB_ENTRIES .= \"".$template['entry']."\";");
    $COMMENT = '';
    $id--;
    if (($this->db->VARS['ad_pos'] > $last_entry) && ($this->db->VARS['ad_code'] != '') && ($i == $last_entry))
    {
      $GB_ENTRIES .= '<tr bgcolor="';
      $GB_ENTRIES .= ($i % 2) ? $this->db->VARS['tb_color_2'] : $this->db->VARS['tb_color_1'];
      $GB_ENTRIES .= '"><td colspan="2" align="center" class="font1">'.$this->db->VARS['ad_code'].'</td></tr>';
      $i++; 
    }       
   }
   $TPL['GB_ENTRIES'] = $GB_ENTRIES;
   return $TPL;
  }
  
}
?>