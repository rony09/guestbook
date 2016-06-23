<?php
$section = (!empty($_GET['section'])) ? $_GET['section'] : 'general';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Guestbook - General Settings</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script> 
<style type="text/css">
<!--
th, .optionsTitle { color: #FFF; background-color: navy; font-size: 14px; height: 20px; border: 1px solid #BBD8E7; font-weight: bold; }
.optionsTitle a, .optionsTitle a:hover { text-decoration: none; color: #FFF; font-size: 12px; font-weight: normal; }
table { background: #BBD8E7; }
-->
</style>
<script type="text/javascript">
<!--
function CheckValue() {
  if(!(document.FormMain.entries_per_page.value >= 1)) {
    alert("The maximum records per page must be greater than 0!");
    document.FormMain.entries_per_page.focus();
    return false;
  }
}
//-->
</script>
</head>
<body>
<?php 
$include_path = dirname(__FILE__);
include($include_path.'/menu.php'); 
?>
<div class="contenttable">
<form action="<?php echo $this->SELF.'?section='.$section; ?>" name="FormMain" method="post" onsubmit="return CheckValue()">
<?php
if($section == 'general')
{ 
?>
<table>
  <tr>
    <td colspan="2" height="25" class="section">General Options</td>
  </tr>
  <tr>
    <td colspan="2" class="subsection">Below are numerous configuration options for your guestbook.</td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Base URL</b> <span class="important">[important]</span><br />
      <small>The base url is used to make sure images and links are correct.
      This should be set to the url you see in the address bar of your web browser but without the /admin.php nor anything that comes after the .php</small></td>
    <td valign="top" class="righttd">
        <input type="text" name="base_url" value="<?php echo htmlspecialchars($this->VARS['base_url']); ?>" size="30" class="input">
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Guestbook URL</b> <span class="important">[important]</span><br />
      <small>This is the actual address of your guestbook. If you are using the guestbook as it is then this should match the base url above. If you have integrated Lazarus in to your website using the gbinclude file then this is set to the page you have integrated it in to.</small></td>
    <td valign="top" class="righttd">
        <input type="text" name="laz_url" value="<?php echo htmlspecialchars($this->VARS['laz_url']); ?>" size="30" class="input">
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Using Include File?</b> <span class="important">[important]</span><br />
      <small>Are you using the guestbook as a standalone page or have you included it in to another file using the gbinclude.php file as explained in include code section? 
      You can have anyone going to index.php, addentry.php or comments.php forwarded to the Guestbook URL from above or you can just have them blocked and told the file requested doesn't exist (404 error).</small></td>
    <td valign="top" class="righttd">
      <select name="included" class="input">
      <?php
      $doWhat = array(0 => 'do nothing', 1 => 'forward them', 2 => 'block them');
      for ($i=0;$i<=2;$i++)
      {
        $selected = ($this->VARS['included'] == $i) ? ' selected' : '';
        echo '<option value="' . $i . '"' . $selected . '>' . $doWhat[$i] . "</option>\n";
      }
      ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="lefttd"> <b>Maximum Records Displayed Per Page</b><br />
      <small>20 records per page is recommend.</small></td>
    <td valign="top" class="righttd">
      <input type="text" name="entries_per_page" value="<?php echo $this->VARS['entries_per_page']; ?>" maxlength="5" size="5" class="input"></td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Language</b><br>
      <small>The language file you want to use.</small></td>
    <td valign="top" class="righttd">
     <select name="lang" class="input">
      <option value="<?php echo $this->VARS['lang']; ?>" selected>Language</option>
<?php
chdir('./lang');
$hnd = opendir('.');
while ($file = readdir($hnd)) 
{
  if(is_file($file)) 
  {
    if(!preg_match('/^codes-/',$file))
    {
      $langlist[] = $file;
    }
  }
}

closedir($hnd);
if ($langlist) 
{
  asort($langlist);
  while (list ($key, $file) = each ($langlist)) 
  {
    if(preg_match('/\.php/',$file,$regs)) 
    {
      $language = str_replace($regs[0],'',$file);
      $isSelected = ($this->VARS['lang'] == $language) ? ' selected="selected"' : '';
      echo '<option value="' . $language . '"' . $isSelected . '>' . $language . "</option>\n";
    }
  }
}
chdir('../');
?>
     </select>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Guestbooks Name</b><br />
      <small>Specify a name for your guestbook. What you put here will appear at the top of the guestbook and will also be used as the name in email sending.<br />
      You can use HTML to alter the appearance of the name in the guestbook or even post an image. Any HTML you do use will be removed when using the name for email sending.</small></td>
    <td valign="top" class="righttd">
        <input type="text" name="book_name" value="<?php echo htmlspecialchars($this->VARS['book_name']); ?>" size="30" class="input">
    </td>
  </tr>  
  <tr>
    <td valign="top" class="lefttd"> <b>Character Set</b><br>
      <small>What character set do you want the guestbook to use?<br>
      If you are using the gbinclude.php file to integrate the guestbook into your site then you must
      make sure the character set here matches the one on the page you have included the guestbook in. 
      If these are different you may mess up entries when using the edit function of easy admin.</small></td>
    <td valign="top" class="righttd">
    <?php 
     $charsets = array(
       'utf-8',
       'utf-16',
       'iso-8859-1',
       'iso-8859-2',
       'iso-8859-3',
       'iso-8859-4',
       'iso-8859-5',
       'iso-8859-6-i',
       'iso-8859-7',
       'iso-8859-8-i',
       'iso-8859-9',
       'iso-8859-10',
       'iso-8859-13',
       'iso-8859-14',
       'iso-8859-15',
       'us-ascii',
       'euc-jp',
       'shift_jis',
       'iso-2022-jp',
       'euc-kr',
       'gb2312',
       'gb18030',
       'big5',
       'tis-620',
       'koi8-r',
       'koi8-u',
       'macintosh',
       'windows-1250',
       'windows-1251',
       'windows-1252',
       'windows-1253',
       'windows-1254',
       'windows-1255',
       'windows-1256',
       'windows-1257'
     );
     echo '<select name="charset" class="input">
      <option value="' . $this->VARS['charset'] . '">Charset</option>
     ';
     foreach ($charsets as $value) 
     {
     	 $isSelected = ($this->VARS['charset'] == $value) ? ' selected="selected"' : '';
       echo '<option value="' . $value . '"' . $isSelected . '>' . $value . '</option>
       '; 
     }
     ?>
      </select>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Show Guest's IP or Hostname</b><br />
      <small>For security reasons, you may wish to display the IP or Hostname of
      the person signing your guestbook in their post. The default is ON.</small>
    </td>
    <td valign="top" class="righttd">
      <input type="radio" name="show_ip" id="showip1" value="1" <?php if ($this->VARS['show_ip'] == 1) {echo 'checked';}?>>
      <label for="showip1">Show IP or Hostname</label> <br />
      <input type="radio" name="show_ip" id="showip2" value="0" <?php if ($this->VARS['show_ip'] == 0) {echo 'checked';}?>>
      <label for="showip2">Hide IP or Hostname</label>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>HTML Codes</b><br />
      <small>If HTML Code is enabled, this means the users can use <b>allowed</b> HTML
      tags in the comment field.</small></td>
    <td valign="top" class="righttd">
        <input type="radio" name="allow_html" id="allow_html1" value="1" <?php if ($this->VARS['allow_html'] == 1) {echo 'checked';}?>>
        <label for="allow_html1">allow HTML Codes</label> <br />
        <input type="radio" name="allow_html" id="allow_html2" value="0" <?php if ($this->VARS['allow_html'] == 0) {echo 'checked';}?>>
        <label for="allow_html2">disable HTML Codes</label>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Allowed HTML Tags</b><br />
      <small>Here you can specify which tags you wish to allow. 
      You can only use tags where the closing tag is identical to the opening tag. eg &lt;b&gt;..&lt;/b&gt;, &lt;i&gt;..&lt;/i&gt;</small></td>
    <td valign="top" class="righttd">
        <input type="text" name="allowed_tags" value="<?php echo htmlspecialchars($this->VARS['allowed_tags']); ?>" size="30" maxlength="60" class="input"><br />
        <small>Seperate tags with commas (ie i,u,b).</small>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Enable Search</b><br />
      <small>Do you want to allow people to search your guestbook? It is only a basic search function, nothing special</small></td>
    <td valign="top" class="righttd">
        <input type="radio" name="allow_search" id="allow_search1" value="1" <?php if ($this->VARS['allow_search'] == 1) {echo 'checked';}?>>
        <label for="allow_search1">Enable search</label> <br />
        <input type="radio" name="allow_search" id="allow_search2" value="0" <?php if ($this->VARS['allow_search'] == 0) {echo 'checked';}?>>
        <label for="allow_search2">Disable search</label>
    </td>
  </tr>   
  <tr>
    <td valign="top" class="lefttd"> <b>Smilies</b><br />
      <small>If you have used email or internet chat, you are likely
      familiar with the smilie concept. Certain standard emoticons are automatically
      converted into smilies.</small></td>
    <td valign="top" class="righttd">
        <input type="radio" name="smilies" id="smilies1" value="1" <?php if ($this->VARS['smilies'] == 1) {echo 'checked';}?>>
        <label for="smilies1">activate Smilies</label><br />
        <input type="radio" name="smilies" id="smilies2" value="0" <?php if ($this->VARS['smilies'] == 0) {echo 'checked';}?>>
        <label for="smilies2">disable Smilies</label>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>AGCodes</b><br />
      <small>AGCode is a variation on the HTML tags you may already be familiar with.
       Basically, it allows you to add functionality or style to your message that would normally require HTML.
       You can use AGCode even if HTML is not enabled for the guestbook.<br />
			 The IMG agcode allows the poster to post an image in their post. This image may be posted on a different website and may not be safe.
          You can specify a maximum width and height for items posted using the [img] and the [flash] tags and any items larger than this will be resized using HTML.
          Images posted using the [img] AGcode are clickable and will open the full size image in a new window.<br />
			 The URL agcode allows the poster to post links in their post. If you allow the URL AGcode then ALL urls in the entry will automatically be made into links.<br />
          Flash AGcode is used to post Flash objects such as YouTube videos.</small>
          <!--<small><strong>If Lazarus keeps displaying YouTube urls posted using Flash AGcode as a link then select this option to have Lazarus use the full YouTube url when getting the data for the Flash file and not the short version.</strong><br />
          <input type="checkbox" name="always_flash" id="always_flash1" value="1" <?php if ($this->VARS['always_flash'] == 1) {echo 'checked';}?>><label for="always_flash1">Use full url for YouTube Flash AGcode</label></small>--></td>
    <td valign="top" class="righttd">
        <input type="radio" name="agcode" id="agcode1" value="1" <?php if ($this->VARS['agcode'] == 1) {echo 'checked';}?>>
        <label for="agcode1">allow AGCodes</label><br />
        <input type="radio" name="agcode" id="agcode2" value="0" <?php if ($this->VARS['agcode'] == 0) {echo 'checked';}?>>
        <label for="agcode2">disable AGCodes</label><br />
        <input type="checkbox" name="allow_urlagcode" id="allow_urlagcode" value="1" <?php if ($this->VARS['allow_urlagcode'] == 1) {echo 'checked';}?>>
        <label for="allow_urlagcode">Use the URL agcode.</label><br />        
        <input type="checkbox" name="allow_emailagcode" id="allow_emailagcode" value="1" <?php if ($this->VARS['allow_emailagcode'] == 1) {echo 'checked';}?>>
        <label for="allow_emailagcode">Use the EMAIL agcode.</label><br />
        <input type="checkbox" name="allow_imgagcode" id="allow_imgagcode" value="1" <?php if ($this->VARS['allow_imgagcode'] == 1) {echo 'checked';}?>>
        <label for="allow_imgagcode">Use the IMG agcode.</label><br />
        <input type="checkbox" name="allow_flashagcode" id="allow_flashagcode" value="1" <?php if ($this->VARS['allow_flashagcode'] == 1) {echo 'checked';}?>>
        <label for="allow_flashagcode">Use the FLASH agcode.</label><br />        
        Maximum Image/Flash Width = <input type="text" name="agcode_img_width" size="3" maxlength="5" value="<?php echo $this->VARS['agcode_img_width']; ?>" class="input"><br />
        Maximum Image/Flash Height = <input type="text" name="agcode_img_height" size="3" maxlength="5" value="<?php echo $this->VARS['agcode_img_height']; ?>" class="input"><br />
        <small>You can disable this feature by setting both to 0.</small>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Email Encryption</b><br />
      <small>You can have the email addresses shown in the guestbook encrypted to prevent them being harvested by spammers.</small></td>
    <td valign="top" class="righttd">
        <input type="radio" name="encrypt_email" id="encrypt_email1" value="1" <?php if ($this->VARS['encrypt_email'] == 1) {echo 'checked';}?>>
        <label for="encrypt_email1">Encrypt email addresses</label><br />
        <input type="radio" name="encrypt_email" id="encrypt_email2" value="0" <?php if ($this->VARS['encrypt_email'] == 0) {echo 'checked';}?>>
        <label for="encrypt_email2">Do not encrypt email addresses</label>
    </td>
  </tr>  
  <tr>
    <td valign="top" class="lefttd"> <b>Permalinks</b><br />
      <small>A permalink is a permanent link to a single entry. The link will show only the one entry.</small></td>
    <td valign="top" class="righttd">
        <input type="checkbox" name="permalinks" id="permalinks" value="1" <?php if ($this->VARS['permalinks'] == 1) {echo 'checked';}?>> <label for="permalinks">Use permalinks.</label><br />
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Gravatars</b><br />
      <small>A Gravatar (<strong>g</strong>lobally <strong>r</strong>ecognised <strong>avatar</strong>) is an image based upon a persons email address. You can upload your own gravatar to use at <a href="http://gravatar.com" target="_blank">Gravatar.com</a> where you can also find out more about gravatars. If no image has been upoaded a random Wavatar is assigned to the email address.<br />
      Please note that the gravatar will show even if email addresses are hidden since the email address used for the gravatar is hashed so cannot be read.</small></td>
    <td valign="top" class="righttd">
        <input type="checkbox" name="use_gravatar" id="use_gravatar" value="1" <?php if ($this->VARS['use_gravatar'] == 1) {echo 'checked';}?>> <label for="use_gravatar">Use Gravatars.</label><br />
    </td>
  </tr>     
</table>
<?php
}
if($section == 'fields')
{ 
?>
  <table>
    <tr> 
      <td colspan="2" height="25" class="section">Field Definitions</td>
    </tr> 
    <tr> 
      <td colspan="2" class="subsection">Below are numerous configuration options for your guestbook fields.</td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>Require Email Address</b><br />
        <small>These options are to do with the email address field. You can make the email address a required field or
        leave it as optional. You can also have the email address shown in the entry (Display) or not (Hide).<br />
        You also have the option of removing the email field all together.</small></td>
      <td valign="top" class="righttd">
        <b>Require an email and</b><br />
        <input type="radio" name="require_email" value="1" <?php if ($this->VARS['require_email'] == 1) {echo 'checked';}?>>
        Display 
        <input type="radio" name="require_email" value="4" <?php if ($this->VARS['require_email'] == 4) {echo 'checked';}?>>
        Hide<br /> 
        <b>Do not require an email and</b><br />       
        <input type="radio" name="require_email" value="0" <?php if ($this->VARS['require_email'] == 0) {echo 'checked';}?>>
        Display 
        <input type="radio" name="require_email" value="3" <?php if ($this->VARS['require_email'] == 3) {echo 'checked';}?>>
        Hide<br />
        <br />
        <input type="radio" name="require_email" value="2" <?php if ($this->VARS['require_email'] == 2) {echo 'checked';}?>>
        Do not use email field</td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>Location Field</b></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_loc" value="1" <?php if ($this->VARS['allow_loc'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_loc" value="0" <?php if ($this->VARS['allow_loc'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>    
    <tr>
      <td valign="top" class="lefttd"> <b>Homepage Field</b></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_url" value="1" <?php if ($this->VARS['allow_url'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_url" value="0" <?php if ($this->VARS['allow_url'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>Gender Field</b></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_gender" value="1" <?php if ($this->VARS['allow_gender'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_gender" value="0" <?php if ($this->VARS['allow_gender'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>ICQ Field</b></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_icq" value="1" <?php if ($this->VARS['allow_icq'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_icq" value="0" <?php if ($this->VARS['allow_icq'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>AIM Field</b></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_aim" value="1" <?php if ($this->VARS['allow_aim'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_aim" value="0" <?php if ($this->VARS['allow_aim'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>MSN Field</b></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_msn" value="1" <?php if ($this->VARS['allow_msn'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_msn" value="0" <?php if ($this->VARS['allow_msn'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>Yahoo Field</b></td>
      <td valign="top" class="righttd">
		  <input type="radio" name="allow_yahoo" value="1" <?php if ($this->VARS['allow_yahoo'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_yahoo" value="0" <?php if ($this->VARS['allow_yahoo'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>Skype Field</b></td>
      <td valign="top" class="righttd">
		  <input type="radio" name="allow_skype" value="1" <?php if ($this->VARS['allow_skype'] == 1) {echo 'checked';}?>>On 
        <input type="radio" name="allow_skype" value="0" <?php if ($this->VARS['allow_skype'] == 0) {echo 'checked';}?>>Off
      </td>
    </tr>    
    <tr>
      <td valign="top" class="lefttd"> <b>Private Messages</b><br />
        <small>Do you want to show the private message option on the add entry form?</small></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_private" value="1" <?php if ($this->VARS['allow_private'] == 1) {echo 'checked';}?>>
        Allow private messages<br />
        <input type="radio" name="allow_private" value="0" <?php if ($this->VARS['allow_private'] == 0) {echo 'checked';}?>>
        No private messages</td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"> <b>Comments</b><br />
        <small>You can allow people to comment on entries or even restrict comments to admin only.</small></td>
      <td valign="top" class="righttd">
        <input type="radio" value="0" name="disablecomments"<?php if ($this->VARS['disablecomments'] == 0) {echo ' checked';}?> id="disablecomments0"> <label for="disablecomments0">Enabled</label><br />
        <input type="radio" value="1" name="disablecomments"<?php if ($this->VARS['disablecomments'] == 1) {echo ' checked';}?> id="disablecomments1"> <label for="disablecomments1">Disabled</label><br />
        <input type="radio" value="2" name="disablecomments"<?php if ($this->VARS['disablecomments'] == 2) {echo ' checked';}?> id="disablecomments2"> <label for="disablecomments2">Admin Only</label>
    </tr>   
    <tr>
      <td valign="top" class="lefttd"> <b>Hide Comments</b><br />
        <small>If this option is selected then any comments are initially hidden and become visible when a javascript link is clicked.</small></td>
      <td valign="top" class="righttd">
        <input type="radio" name="hide_comments" value="1" <?php if ($this->VARS['hide_comments'] == 1) {echo 'checked';}?>>
        Hide Comments<br />
        <input type="radio" name="hide_comments" value="0" <?php if ($this->VARS['hide_comments'] == 0) {echo 'checked';}?>>
        Show Comments</td>
    </tr>   
    <tr>
      <td valign="top" class="lefttd"> <b>Picture Upload</b><br />
      
      <?php if ((is_writable($include_path . '/../public')) && (is_writable($include_path . '/../tmp'))) 
      {  ?>
        <small>You can allow guests to upload an image with their post. You can specify the height and width of the image that is displayed in the entry here. All
        images which are bigger will automatically be resized when displayed in the entry but the full size image can still be seen by clicking on the smaller image.
        The height and width you specify here are also used to specify the size of any thumbnail created.</small></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_img" value="1" <?php if ($this->VARS['allow_img'] == 1) {echo 'checked';}?>>
        allow Picture Upload <br />
        <input type="radio" name="allow_img" value="0" <?php if ($this->VARS['allow_img'] == 0) {echo 'checked';}?>>
        disable Picture Upload <br />
        <font size="1">Size image will appear in entry:<br />
        <input type="text" name="img_width" size="3" value="<?php echo $this->VARS['img_width']; ?>" class="input">
        X 
        <input type="text" name="img_height" size="3" value="<?php echo $this->VARS['img_height']; ?>" class="input">
        <small>width x height</small><br />
				<small>Maximum filesize: </small><input type="text" name="max_img_size" size="4" value="<?php echo round($this->VARS['max_img_size']); ?>" class="input"><small>kb</small>
				<?php }
				else
				{  ?>
			<small>You can allow guests to upload an image with their post. You can specify the height and width of the image that is displayed in the entry here. All
        images which are bigger will automatically be resized when displayed in the entry but the full size image can still be seen by clicking on the smaller image.
        The height and width you specify here is also used to specify the size of any thumbnail created.<br />
        <font color="red"><b>THESE OPTIONS ARE DISABLED AS YOU NEED TO CHANGE THE PERMISSIONS OF THE PUBLIC AND TMP FOLDERS TO 777 SO THAT THE GUESTBOOK CAN MOVE THE IMAGES THERE.<br /></small></td>
      <td valign="top" class="righttd">
        <input type="radio" name="allow_img" value="1" disabled>
        allow Picture Upload <br />
        <input type="radio" name="allow_img" value="0" checked disabled>
        disable Picture Upload <br />
        <font size="1">Size in entry:<br />
        <input type="text" name="img_width" size="5" value="<?php echo $this->VARS['img_width']; ?>" class="input" disabled>
        X 
        <input type="text" name="img_height" size="5" value="<?php echo $this->VARS['img_height']; ?>" class="input" disabled>
        <small>width x height</small><br />
				<small>Maximum filesize: </small><input type="text" name="max_img_size" size="4" value="<?php echo round($this->VARS['max_img_size']); ?>" class="input" disabled><small>kb</small>
				<?php } ?>
        </td>
    </tr>
    <tr>
      <td valign="top" class="lefttd"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Thumbnails</b><br />
        <small>If you have Image Magick or PHP's GD extension on your server you can make smaller versions of the uploaded
        pictures to appear in the guestbook. This will save your bandwidth.</small></td>
      <td valign="top" class="righttd">
        <input type="checkbox" name="thumbnail" value="1" <?php if ($this->VARS['thumbnail'] == 1) {echo 'checked';}?>>
        create thumbnails<br />
        <small>Create thumbnail if filesize is over<br /></small>
        <input type="text" name="thumb_min_fsize" size="5" value="<?php echo $this->VARS['thumb_min_fsize']; ?>" class="input">
        <small> kb</small></td>
    </tr>
  </table>
<?php
}
if($section == 'email')
{ 
$hasSSL = (!function_exists('extension_loaded')	|| !extension_loaded('openssl')) ? false : true;
$noSSL = (!$hasSSL) ? ' disabled="disabled"' : '';
//echo("establishing SSL connections requires the OpenSSL extension enabled");
?>
<script type="text/javascript">
// Function taken from DD-WRT
function setMask() 
{
  var OldInput = document.getElementById('smtp_password');
  if(!OldInput) return;
  
  var val = OldInput.value;
  var val_maxlength = OldInput.maxlength;
  var val_size = OldInput.size;
  var parent = OldInput.parentNode;
  var sibling = OldInput.nextSibling;
  var newInput = document.createElement('input');
  newInput.setAttribute('value', val);
  newInput.setAttribute('name', 'smtp_password');
  newInput.setAttribute('id', 'smtp_password');
  newInput.setAttribute('size', val_size);
  newInput.setAttribute('class', 'input');
  
  if (OldInput.type == 'password')
  	newInput.setAttribute('type', 'text');
  else
  	newInput.setAttribute('type', 'password');
  
  parent.removeChild(OldInput);
  parent.insertBefore(newInput, sibling);
  newInput.focus();
}
</script>
<table>
  <tr>
    <td colspan="2" height="25" class="section">Email Options</td>
  </tr>
  <tr>
    <td colspan="2" class="subsection">Change various email setting such as who to notify, what to include and how to send them.</td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Webmaster E-mail</b><br />
       <small>Your e-mail address. This is the address notifications are sent to if you have requested them. You can have the notifications sent to more than one address. 
       Simply seperate the email addresses using a comma ( , ). If no address is set for Guestbooks Email then this is the address thank you emails will be sent from. 
       If you have multiple addresses specified then the emails will be sent from the first one.</small></td>
    <td valign="top" class="righttd"><input type="text" name="admin_mail" value="<?php echo htmlspecialchars($this->VARS['admin_mail']); ?>" size="30" maxlength="200" class="input"></td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Guestbooks E-mail</b><br />
       <small>This email address will be used when sending thank you emails to guests. 
       It will also be used when sending notifications to you about new entries when no email address has been supplied in the entry. 
       If this is left blank then the webmasters email address from above will be used.<br />
       You can choose to have all notifications that get sent to the webmaster to appear from this address. 
       Again if no email has been supplied here then the webmaster address from above will be used.</small></td>
    <td valign="top" class="righttd"><input type="text" name="book_mail" value="<?php echo htmlspecialchars($this->VARS['book_mail']); ?>" size="30" maxlength="60" class="input"><br />
    <input type="checkbox" name="always_bookemail" value="1" <?php if ($this->VARS['always_bookemail'] == 1) {echo 'checked';}?>> Always send notifications from this</td>
  </tr>
  <tr>
    <td valign="top" class="lefttd">
      <b>E-mail notification</b><br />
        <small>Select whether you want to send emails to yourself when someone has signed your guestbook.
        Note: your email address above must be valid and an email server must be properly configured.<br />
        If you select <b>Attach uploaded images to emails</b> then if an image has been included in the post a copy of it will be attached 
        to the notification email sent to the webmaster if you have requested to receive notification emails.</small>
    </td>
    <td valign="top" class="righttd">
      <input type="checkbox" name="notify_private" value="1" <?php if ($this->VARS['notify_private'] == 1) {echo 'checked';}?>>
      Notify webmaster of private messages<br />
      <input type="checkbox" name="notify_admin" value="1" <?php if ($this->VARS['notify_admin'] == 1) {echo 'checked';}?>>
      Notify webmaster of new public messages<br />
      <input type="checkbox" name="notify_admin_com" value="1" <?php if ($this->VARS['notify_admin_com'] == 1) {echo 'checked';}?>>
      Notify webmaster of new comments<br />      
      <input type="checkbox" name="notify_guest" value="1" <?php if ($this->VARS['notify_guest'] == 1) {echo 'checked';}?>>
      Send thank you email to guests<br />
      <input type="checkbox" name="html_email" value="1" <?php if ($this->VARS['html_email'] == 1) {echo 'checked';}?>> 
      Attach uploaded images to emails.</td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"><b>Thank You Email</b><br />
       <small>You can customise the thank you email that guests recieve when they submit a post in the guestbook.
        Simply edit the wording in the box to the right. A short thank you is best.<br />
        <br />
        You can have the guests name put into the email for you by simply putting <b>[NAME]</b> where you want
        it to be. The guestbook will replace the <b>[NAME]</b> with the name supplied in the entry. You can also
        use AGCode to style the message but if you want to use a URL it is recommended you just put the URL in the 
        message and let the AGCode function turn it in to a link.</small>
    </td>
    <td valign="top" class="righttd">
      <textarea rows="5" cols="30" wrap="virtual" name="notify_mes" id="notify_mes" class="input"><?php echo htmlspecialchars($this->VARS['notify_mes']); ?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS["base_url"]; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('notify_mes','up');" /><br /><img src="<?php echo $this->VARS["base_url"]; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('notify_mes','down');" /></div>
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Email Sending</b><br />
    <small>Choose to send emails using either sendmail or SMTP. Sendmail is installed on most servers by default and we recommend using it if it is available.</small></td>
    <td valign="top" class="righttd">
	    <input type="radio" name="mail_type" value="1" <?php if ($this->VARS['mail_type'] == 1) {echo 'checked';}?>>Sendmail <small>(recommended)</small><br />
      <input type="radio" name="mail_type" value="2" <?php if ($this->VARS['mail_type'] == 2) {echo 'checked';}?>>SMTP
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"><b>SMTP Settings</b><br />
      <small>Your SMTP details should be available from your web host or your email provider. 
      If your SMTP server's address is something like <em>mail.yourdomain.com</em> you could probably just use localhost for the server.<br />
      Please note that the SMTP function of Lazarus Guestbook may not work with all SMTP servers.<br />
      If you are using SMTP please create a file in the guestbook folder called smtplog.txt and make it writable for logging error messages.
      <?php if(!$hasSSL) { echo '<div class="error">Your server does not have the OpenSSL extension enabled and so cannot make SSL nor TLS connections.</div>'; } ?></small>
    <td valign="top" class="righttd">
      <small>Server:</small><input type="text" name="smtp_server" value="<?php echo htmlspecialchars($this->VARS['smtp_server']); ?>" size="30" maxlength="100" class="input"><br />
      <small>Username:</small><input type="text" name="smtp_username" value="<?php echo htmlspecialchars($this->VARS['smtp_username']); ?>" size="30" maxlength="100" class="input"><br />
      <small ondblclick="setMask();">Password:</small><input type="password" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars($this->VARS['smtp_password']); ?>" size="30" maxlength="100" class="input"><br />
      <small>Port:</small><input type="number" min="0" max="65500" name="smtp_port" value="<?php echo $this->VARS['smtp_port']; ?>" size="10" maxlength="5" class="input"><br />
      <small>Connection Security:</small><select name="mailSSL" class="input"<?php echo $noSSL; ?>>
      <option value="0"<?php echo ($this->VARS['mailSSL'] == 0) ? ' selected="selected"': ''; ?>>None</option>
      <option value="1"<?php echo ($this->VARS['mailSSL'] == 1) ? ' selected="selected"': ''; ?>>SSL</option>
      <option value="2"<?php echo ($this->VARS['mailSSL'] == 2) ? ' selected="selected"': ''; ?>>TLS</option>
      </select>
      <?php if(!$hasSSL) { echo '<small style="color:#D00;">(disabled)</small>'; } ?> 
      </td>
  </tr>
</table>
<?php
}
if($section == 'security')
{ 
?>
<table>
  <tr>
    <td colspan="2" height="25" class="section">Security Options</td>
  </tr> 
  <tr>
    <td colspan="2" class="subsection">Below are numerous configuration options for your Guestbook.</td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Count Blocked Posts</b><br />
      <small>You can have Lazarus keep track of how many attempted entries and comments have been blocked by the anti spam functions or other settings.</small>
      </td>
    <td valign="top" class="righttd">
         <input type="checkbox" id="count_blocks" name="count_blocks"<?php if ($this->VARS['count_blocks'] == 1) {echo ' checked';} ?>> <label for="count_blocks">Count blocked posts</label><br />
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Posting Times</b><br />
      <small>To help fight spam the guestbook makes a note of what time the guest opened the addentry and comments page. 
      When the guest posts their entry the guestbook then compares this time with the values on the right and rejects it if 
      they posted it to fast or took to long. If you do not wish to use either of these timings simply set it to 0.<br />
      This check is for entries and comments so don't use to long a waiting time.</small>
      </td>
    <td valign="top" class="righttd">
         How long until they can post their entry:<br />
        <input type="text" size="2" maxlength="2" name="post_time_min" value="<?php echo($this->VARS['post_time_min']); ?>" class="input"> seconds<br />
        Amount of time they have to post their entry:<br />
        <input type="text" size="4" maxlength="5" name="post_time_max" value="<?php echo($this->VARS['post_time_max']); ?>" class="input"> seconds<br />
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Header Checks</b><br />
      <small>To further help fight spam you can have Lazarus check the http headers sent by the guests browser to see if it matches known spam behavior.</small>
      </td>
    <td valign="top" class="righttd">
         <input type="checkbox" id="check_headers" name="check_headers"<?php if ($this->VARS['check_headers'] == 1) {echo ' checked';} ?>> <label for="check_headers">Check headers</label><br />
    </td>
  </tr>    
  <tr>
    <td valign="top" class="lefttd"> <b>Anti Bot Test For Entries</b><br />
      <small>You can require that users have to enter the randomly generated characters from an image or answer a question you have set before their post gets added.<br />
			If you select the question method then simply specify <b>BOTH</b> the question and the answer in their respective boxes on the right although you do not have to supply a question if you just want to password protect your guestbook. 
			Try to keep the question short, you are limited to 50 characters. The answer is limited to 20 characters.<br />
			Some examples are:<br />
			Question: What colour is the sky?<br />
			Answer: blue<br />
			Answers are not case sensitive so they could answer the above as blue or Blue or even BLUE and it will pass.</small>
         <?php if (!extension_loaded('gd')) { echo '<p style="color#C00;">Sorry but you do not have GD installed and so cannot use image verification</p>'; } ?>
      </td>
    <td valign="top" class="righttd">
        <input type="radio" name="antibottest" value="2" <?php if ($this->VARS['antibottest'] == 2) {echo 'checked';} 
        if (!extension_loaded('gd')) { echo ' disabled'; } ?>> 
        Use image verification (<acronym title="Completely Automated Public Turing Test">CAPTCHA</acronym>).<br />
        <input type="radio" name="antibottest" value="1" <?php if ($this->VARS['antibottest'] == 1) {echo 'checked';}?>> 
        Use question &amp; answer (<acronym title="Semi Automatic Public Turing Test">SAPTCHA</acronym>).<br />
        <input type="radio" name="antibottest" value="0" <?php if ($this->VARS['antibottest'] == 0) {echo 'checked';}?>> 
        No bot test.<br />
        <fieldset><legend>Anti Bot Question &amp; Answer</legend>
        Question: 
        <input type="text" name="bottestquestion" size="30" maxlength="50" value="<?php echo htmlspecialchars($this->VARS['bottestquestion']); ?>" class="input"><br />
        Answer: &nbsp;&nbsp;
        <input type="text" name="bottestanswer" size="29" maxlength="20" value="<?php echo htmlspecialchars($this->VARS['bottestanswer']); ?>" class="input"></fieldset>
    </td>
  </tr>  
  <tr>
    <td valign="top" class="lefttd"> <b>Anti Bot Test For Comments</b><br />
      <small>Exactly the same as above except this is for the comments
      </td>
    <td valign="top" class="righttd">
        <input type="radio" name="need_pass" value="2" <?php if ($this->VARS['need_pass'] == 2) {echo 'checked';}
        if (!extension_loaded('gd')) { echo ' disabled'; }?>>
        Use image verification (<acronym title="Completely Automated Public Turing Test">CAPTCHA</acronym>)<br />
        <input type="radio" name="need_pass" value="1" <?php if ($this->VARS['need_pass'] == 1) {echo 'checked';}?>>
        Use question &amp; answer (<acronym title="Semi Automatic Public Turing Test">SAPTCHA</acronym>). <br />
        <input type="radio" name="need_pass" value="0" <?php if ($this->VARS['need_pass'] == 0) {echo 'checked';}?>>
        No bot test<br />
        <fieldset><legend>Anti Bot Question &amp; Answer</legend>
        Question: 
        <input type="text" name="com_question" size="30" maxlength="50" value="<?php echo htmlspecialchars($this->VARS['com_question']); ?>" class="input"><br />
        Answer: &nbsp;&nbsp;
        <input type="text" name="comment_pass" size="29" value="<?php echo htmlspecialchars($this->VARS['comment_pass']); ?>" class="input"></fieldset> 
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>CAPTCHA Options</b><br />
    <?php if (!extension_loaded('gd')) { echo '<p style="color#C00;">Sorry but you do not have GD installed and so cannot use image verification</p>'; } ?>
      <small>Here you can set the options for how your CAPTCHA (image verification) image should appear. You can turn off the foreground lines. 
      Disable the foreground distortion (all the dots that make the picture look grainy) and even turn off the colour so the image uses grey.<br />
      You can also specify the dimensions of the image to make it better fit your site and even make the background solid or transparent.
      <p>Recommended setting is to have everything selected</p></small>
      </td>
    <td valign="top" class="righttd">
        <input type="checkbox" name="captcha_noise"<?php if ($this->VARS['captcha_noise'] == 1) {echo ' checked';}
        if (!extension_loaded('gd')) { echo ' disabled'; }?>> Use foreground noise<br />
        <input type="checkbox" name="captcha_grid"<?php if ($this->VARS['captcha_grid'] == 1) {echo ' checked';}
        if (!extension_loaded('gd')) { echo ' disabled'; }?>> Use random lines<br />
        <input type="checkbox" name="captcha_grey"<?php if ($this->VARS['captcha_grey'] == 1) {echo ' checked';}
        if (!extension_loaded('gd')) { echo ' disabled'; }?>> Use Greyscale<br />
        <input type="checkbox" name="captcha_trans"<?php if ($this->VARS['captcha_trans'] == 1) {echo ' checked';}
        if (!extension_loaded('gd')) { echo ' disabled'; }?>> Use Transparency<br />
        Image dimension:<br />
        Width: <input type="text" name="captcha_width" value="<?php echo $this->VARS['captcha_width']; ?>" size="3" maxsize="3" class="input"> &nbsp; Height: <input type="text" name="captcha_height" value="<?php echo $this->VARS['captcha_height']; ?>" size="3" maxsize="3" class="input">
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Solve Media</b><br />
      <small><a href="http://solvemedia.com" target="_blank">Solve Media</a> is a remotely hosted CAPTCHA alternative much like reCaptcha. 
      The main difference is Solve Media always uses real words where as reCaptcha can sometimes display mathematical formulas or musical notation.<br />
      The real difference between them is that Solve Media occasionally display adverts or ad related captcha for which I make a small amount of revenue.</small>
      </td>
    <td valign="top" class="righttd">
        <input type="checkbox" name="solve_media"<?php if($this->VARS['solve_media'] == 1) {echo ' checked';}?>> Use Solve Media
    </td>
  </tr>
  <tr>
    <td class="lefttd"> <b>Maximum urls in a post</b><br />
      <small>You can limit how many urls (web links) can be included in a post. This is useful because a lot of spammers just post a long list of links.<br />
      If a post contains more than the allowed number of urls it will be blocked.</small></td>
    <td valign="top" class="righttd">
      <input type="text" name="max_url" value="<?php echo $this->VARS['max_url']; ?>" maxlength="2" size="5" class="input"><br />
      <small>0 = no links, 99 = disable check</small></td>
  </tr>   
  <tr>
    <td valign="top" class="lefttd"> <b>Moderate Posts</b><br />
      <small>Activate this option if you wish to review all entries before they appear in the guestbook. This does not apply to private messages.
			You must supply an webmaster email address in the email section so you can be notified of new entries.</small>
      </td>
    <td valign="top" class="righttd">
        <input type="radio" name="require_checking" value="1" <?php if ($this->VARS['require_checking'] == 1) {echo 'checked';}?>>
        Moderate posts<br />
        <input type="radio" name="require_checking" value="0" <?php if ($this->VARS['require_checking'] == 0) {echo 'checked';}?>>
        Do not moderate posts<br />
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd"> <b>Moderate Comments</b><br />
      <small>Activate this option if you wish to review all comments before they appear in the guestbook.
			You must supply an webmaster email address in the email section so you can be notified of new entries.</small>
      </td>
    <td valign="top" class="righttd">
        <input type="radio" name="require_comchecking" value="1" <?php if ($this->VARS['require_comchecking'] == 1) {echo 'checked';}?>>
        Moderate comments<br />
        <input type="radio" name="require_comchecking" value="0" <?php if ($this->VARS['require_comchecking'] == 0) {echo 'checked';}?>>
        Do not moderate comments<br />
    </td>
  </tr>  	  
  <tr>
      <td valign="top" class="lefttd"> <b>Message Length</b><br />
        <small>You can set the minimum and maximum
        message length here. The max. word length is an option to avoid messages 
        from nice people entering a bunch of characters without spaces. :)</small>
      </td>
    <td valign="top" class="righttd">
      <input type="text" name="min_text" size="5" value="<?php echo $this->VARS['min_text']; ?>" class="input">
      Min. message length<br />
      <input type="text" name="max_text" size="5" value="<?php echo $this->VARS['max_text']; ?>" class="input">
      Max. message length<br />
      <input type="text" name="max_word_len" size="5" value="<?php echo $this->VARS['max_word_len']; ?>" class="input">
      Max. Word length</td>
  </tr>
  <tr>
    <td valign="top" class="lefttd">
      <b>Censor Option</b><br />
			<small>You may have certain words censored on your Guestbook. Words you choose to censor will be replaced by #@*%!. You can also have any post containing bad words marked for review before being published or have the post blocked. 
        The censoring is applied to name, email address, location, homepage url and their message.<br /><br />
        Type all words you want censored in the relevant box on the right putting each word on its own line. If you type "dog", all messages containing the string "dog"
        would be blocked, marked for moderation or the word dog replaced (dog, for instance, would appear as "#@*%!") depending on which box you put the word dog in.<br /><br />
		   You can use <a href="http://regular-expressions.info" target="_blank">regular expressions</a> to make your censoring more powerful. Only enable this option if you understand PHP's regular expressions.</small><br />
			<input type="checkbox" name="use_regex" value="1" <?php if ($this->VARS['use_regex'] == 1) {echo 'checked';}?>> Use Regex.</td>
    <td valign="top" class="righttd">
<!--    <input type="radio" name="censor" value="0"<?php if ($this->VARS['censor'] == 0) {echo ' checked';}?>> Do not censor posts<br />
    <br />
    Censor posts and...<br />
    <input type="radio" name="censor" value="1"<?php if ($this->VARS['censor'] == 1) {echo ' checked';}?>> Replace words on save<br />
    <input type="radio" name="censor" value="3"<?php if ($this->VARS['censor'] == 3) {echo ' checked';}?>> Replace words on display<br />
    <input type="radio" name="censor" value="4"<?php if ($this->VARS['censor'] == 4) {echo ' checked';}?>> Moderate post<br />
    <input type="radio" name="censor" value="2"<?php if ($this->VARS['censor'] == 2) {echo ' checked';}?>> Block post<br /> -->
     <b>Words to Censor</b><br />
     <textarea name="badwords1" id="badwords1" rows="3" cols="30" wrap="VIRTUAL" class="input">
<?php
if (isset($badwords1) && sizeof($badwords1)>0) {
  for ($i=0; $i<sizeof($badwords1); $i++) {
    echo htmlspecialchars($badwords1[$i]) . "\n";
  }
}
?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS["base_url"]; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('badwords1','up');" /><br /><img src="<?php echo $this->VARS["base_url"]; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('badwords1','down');" /></div><br /><br />
     <b>Words to Moderate</b><br />
     <textarea name="badwords3" id="badwords3" rows="3" cols="30" wrap="VIRTUAL" class="input">
<?php
if (isset($badwords3) && sizeof($badwords3)>0) {
  for ($i=0; $i<sizeof($badwords3); $i++) {
    echo htmlspecialchars($badwords3[$i]) . "\n";
  }
}
?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS["base_url"]; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('badwords3','up');" /><br /><img src="<?php echo $this->VARS["base_url"]; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('badwords3','down');" /></div><br /><br />
     <b>Words to Block</b><br />
     <textarea name="badwords2" id="badwords2" rows="3" cols="30" wrap="VIRTUAL" class="input">
<?php
if (isset($badwords2) && sizeof($badwords2)>0) {
  for ($i=0; $i<sizeof($badwords2); $i++) {
    echo htmlspecialchars($badwords2[$i]) . "\n";
  }
}
?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS["base_url"]; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('badwords2','up');" /><br /><img src="<?php echo $this->VARS["base_url"]; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('badwords2','down');" /></div><br />
    </td>
  </tr>
  <tr valign="top">
    <td class="lefttd"> <b>Flood Check?</b><br />
      <small>You may prevent your users from flooding your Guestbook with posts by activating this feature.
      By enabling floodcheck, you disallow users from posting within a given time span of their last post.
      In other words, if you set a floodcheck time span of 60 seconds, a user may not post again within 60 seconds of his last post.</small>
      <br /><br /><center>
      <input type="radio" name="flood_check" value="1" <?php if ($this->VARS['flood_check'] == 1) {echo 'checked';}?>> FloodCheck On
      <input type="radio" name="flood_check" value="0" <?php if ($this->VARS['flood_check'] == 0) {echo 'checked';}?>> FloodCheck Off
      </center>
      </td>
    <td valign="top" class="righttd"> <b>FloodCheck Time Span</b><br />
      <small>Set the amount of time in seconds used by FloodCheck to prevent post flooding.
      Recommended: 300. Type the number of seconds only.</small><br />
      <input type="text" name="flood_timeout" size="5" value="<?php echo $this->VARS['flood_timeout']; ?>" class="input">
    </td>
  </tr>
  <tr>
    <td valign="top" class="lefttd">
      <b>Banned IP?</b><br />
        <small>You may ban any IP addresses from signing your Guestbook. Type in the complete IP number (eg. 243.21.31.7),
        or use a partial IP number (eg. 243.21.31.). The Guestbook will do matches from the beginning of each IP number that you enter.
        Thus, if you enter a partial IP of 243.21.31., someone attempting to sign who has an IP number of 243.21.31.5
        will not be able to sign. Similarly, if you have an IP ban on 243.21., someone signing who has an IP of 243.21.3.44
        will not be able to sign. So be careful when you add IPs to your ban list and be as specific as possible. You must have specify atleast two octets (ie 255.255.).<br />
        You can also specify a range of IP addresses to block using the x.x.x.x-y.y.y.y format such as 243.21.12.0-243.21.15.255.<br />
        You can also specify blocks of IP addresses using the <a href="http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing" target="_blank">CIDR format</a>  
        (eg. 127.0.0.1/24) which makes it easier to block whole ranges of IP addresses.</small>
        <center>Block listed IP addresses? 
        <input type="radio" name="banned_ip" value="1" <?php if ($this->VARS['banned_ip'] == 1) {echo 'checked';}?>> yes
        <input type="radio" name="banned_ip" value="0" <?php if ($this->VARS['banned_ip'] == 0) {echo 'checked';}?>> no
        </center>
    </td>
    <td valign="top" class="righttd">
     <b>IP Number Ban List:</b><br />
      <small>Put each IP number on its own line.<small><br />
     <textarea name="banned_ips" id="banned_ips" rows=5 cols=30 wrap="VIRTUAL" class="input">
<?php
if (isset($banned_ips) && sizeof($banned_ips)>0) {
  for ($i=0; $i<sizeof($banned_ips); $i++) {
    echo "$banned_ips[$i]\n";
  }
}
?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS['base_url']; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('banned_ips','up');" /><br /><img src="<?php echo $this->VARS['base_url']; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('banned_ips','down');" /></div>
    </td>
  </tr>
  <tr valign="top">
    <td class="lefttd"> <b>Stop Forum Spam Check</b><br />
      <small>You can check the IP address and email address, if supplied, of people making posts/comments against the <a href="http://www.stopforumspam.com/" target="_blank">Stop Forum Spam</a> database. The higher the setting the more aggressive the check will be.</small>
    </td>
    <td valign="top" class="righttd"> <b><abbr title="Stop Forum Spam">SFS</abbr> Confidence Level</b><br />
      <select name="sfs_confidence" class="input">
      <?php
      $sfsLevels = array(0 => 'off', 90 => 'low', 50 => 'med', 25 => 'high');
      foreach($sfsLevels as $percent => $level)
      {
        $isSelected = ($percent == $this->VARS['sfs_confidence']) ? ' selected="selected"' : '';
        echo '<option value="' . $percent . '"' . $isSelected . '>' . $level . "</option>\n";
      }
      ?>
    </td>
  </tr>
  <tr valign="top">
    <td class="lefttd"> <b>Honeypot</b><br />
      <small>A honeypot is a checkbox that gets hidden by JavaScript so hiding it from most browsers. Since most bots don't support JavaScript and just fill in every input they will fill this in resulting in their post being stopped.</small>
    </td>
    <td valign="top" class="righttd"> <b>Honeypot</b><br />
        <input type="radio" name="honeypot" value="1" <?php if ($this->VARS['honeypot'] == 1) {echo 'checked';}?> id="honeypot1"> <label for="honeypot1">Enable</label>
        <input type="radio" name="honeypot" value="0" <?php if ($this->VARS['honeypot'] == 0) {echo 'checked';}?> id="honeypot2"> <label for="honeypot2">Disable</label>
    </td>
  </tr>
</table>
<?php
}
?>
 <br />
  <center>
    <input type="submit" class="submit" value="Submit Settings">
    <input type="reset" class="reset" value="Reset">
    <input type="hidden" value="<?php echo $this->uid; ?>" name="uid">
    <input type="hidden" value="<?php echo $this->gbsession; ?>" name="gbsession">
    <input type="hidden" value="save" name="action">
    <input type="hidden" value="general" name="panel">
    <input type="hidden" value="<?php echo $section; ?>" name="section">
  </center>
</form>
</div>