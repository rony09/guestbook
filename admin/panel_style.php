<?php
$section = (!empty($_GET['section'])) ? $_GET['section'] : 'style';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Guestbook - Style</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->VARS['charset']; ?>">
<link rel="stylesheet" type="text/css" href="admin.css">
<script type="text/javascript" src="lazjs.php?jspage=admin"></script>
<style type="text/css">
<!--
.text_size1 {  font-family: <?php echo $this->VARS['font_face']; ?>; font-size: <?php echo $this->VARS['tb_font_1']; ?>}
.text_size2 {  font-family: <?php echo $this->VARS['font_face']; ?>; font-size: <?php echo $this->VARS['tb_font_2']; ?>}
.font {  font-family: <?php echo $this->VARS['font_face']; ?>; }
table { background: #BBD8E7; }
pre { background: #FFF; }
-->
</style>
<script type="text/javascript">
<!--
var timenow = new Date();
var day = timenow.getDay();
var hour = timenow.getHours();
var minute = timenow.getMinutes();
var serverTime = new Date(<?php echo(date("Y, n-1, j, G, i, s")); ?>)
var serverSecs = serverTime.getTime();
var pcSecs = timenow.getTime();
var theDiff = ((pcSecs - serverSecs)/3600000);
var theDiff = theDiff.toFixed(1);

switch (day)
{
 case 0 :
      textday = "Sunday";
      break;
 case 1 :
      textday = "Monday";
      break;  
 case 2 :
      textday = "Tuesday";
      break;  
 case 3 :
      textday = "Wednesday";
      break;  
 case 4 :
      textday = "Thursday";
      break;  
 case 5 :
      textday = "Friday";
      break;  
 case 6 :
      textday = "Saturday";
      break;      
}

if (hour < 10)
{
   hour = '0'+hour;
}
if (minute < 10)
{
   minute = '0'+minute;
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
<form action="<?php echo $this->SELF.'?section='.$section; ?>" name="FormMain" method="post">
<?php
if($section == 'include')
{ 
?>
  <table>
    <tr> 
      <td height="25" class="section">The Include Code</td>
    </tr>
    <tr> 
      <td class="subsection">Integrating the guestbook into your website is now easier than ever.</td>
    </tr>
    <tr> 
      <td class="lefttd"><p><b>Your include code:</b> <input type="text" value="<?php echo '&lt;?php include(\''.preg_replace("/admin$/", '', dirname(__FILE__)).'gbinclude.php\'); ?&gt;'; ?>" size="96" class="input"></p>
      <p>Simply make a web as normal then put the code on the right EXACTLY where you want the guestbook to appear in the page.<br>
      Please remember that the page you are putting the code into must use the .php extension and not .htm nor .html</p>
      <p><b>example:</b>
      <div style="overflow: auto; border: 1px solid #000; padding: 5px; margin: 2px; background-color: #FFF;">
      <pre>
      &lt;!DOCTYPE HTML PUBLIC &quot;-//W3C//DTD HTML 4.01 Transitional//EN&quot;&gt;
      &lt;html&gt;
        &lt;head&gt;
        &lt;meta http-equiv=&quot;content-type&quot; content=&quot;text/html; charset=utf-8&quot;&gt;
        &lt;title&gt;My Guestbook&lt;/title&gt;
        &lt;/head&gt;
        &lt;body&gt;
        &lt;h1&gt;Welcome To My Site&lt;/h1&gt;
        <?php echo '&lt;?php include(\''.preg_replace("/admin$/", '', dirname(__FILE__)).'gbinclude.php\'); ?&gt;'; ?>
        
        &lt;/body&gt;
      &lt;/html&gt;</pre>
      </div></p>
      <p>The guestbook will appear EXACTLY where you place the include code in your web page.</p>
      </div>
      </td>
    </tr>
  </table>
<?php
}
if($section == 'style')
{ 
?>
  <table>
    <tr> 
      <td colspan="3" height="25" class="section">Style Settings</td>
    </tr>
    <tr> 
      <td colspan="3" class="subsection">Please complete the 
        following fields, which provide information such as your guestbook's table 
        width, the color of the table and the font face and font size.</td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Page Background Color</b><br>
        <small>Default - #FFFFFF</small></td>
      <td valign="top" class="righttd" style="width: 280px;"> 
        <input type="text" name="pbgcolor" value="<?php echo htmlspecialchars($this->VARS['pbgcolor']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd">
        <table border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['pbgcolor']; ?>" bordercolor="#000000">
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Table Width</font></b><br>
        <small>You may use either exact pixels (recommended: 600) or a percentage (recommended: 95%)</small> </td>
      <td class="righttd" valign="top"> 
        <input type="text" name="width" value="<?php echo $this->VARS['width']; ?>" size="10" maxlength="6" class="input">
      </td>
      <td class="displaytd" valign="top">&nbsp;</td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Font Face (e.g., Verdana)</b><br>
        <small>You may use a backup font as well. For example: to use Verdana as your first choice, with Arial as 
        a conditional font for those users that don't have Verdana as a font on their system, you would type "Verdana, Arial") </small> </td>
      <td class="righttd" valign="top"> 
        <input type="text" name="font_face" value="<?php echo htmlspecialchars($this->VARS['font_face']); ?>" size="38" maxlength="70" class="input">
      </td>
      <td style="width:50px;background-color:#FFF;" class="font">Font</td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Link Color</b><br>
        <small>Color of links inside the guestbook. Default - #006699</small> 
      </td>
      <td class="righttd" valign="top"> 
        <input type="text" name="link_color" value="<?php echo htmlspecialchars($this->VARS['link_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['link_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Text Color</b><br>
        <small>Guestbook text color. Default - #000000</small> 
      </td>
      <td class="righttd" valign="top"> 
        <input type="text" name="text_color" value="<?php echo htmlspecialchars($this->VARS['text_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['text_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Text Size 1</b><br>
        <small>The text font size.</small> </td>
      <td class="righttd" valign="top"> 
        <input type="text" name="tb_font_1" value="<?php echo $this->VARS['tb_font_1']; ?>" size="6" maxlength="6" class="input">
      </td>
      <td style="width:50px;background-color:#FFF;" class="text_size1">Text Size 1</td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Text Size 2</b><br>
        <small>A smaller value is recommend here... but depending on your font face, you may want to alter this.</small> 
      </td>
      <td class="righttd" valign="top"> 
        <input type="text" name="tb_font_2" value="<?php echo $this->VARS['tb_font_2']; ?>" size="6" maxlength="6" class="input">
      </td>
      <td style="width:50px;background-color:#FFF;" class="text_size2">Text Size 2</td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Top Section Font Color</b><br>
        <small>This is the color for the text that appears above and below the guestbook. Default - #000000</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="laz_top_font_color" value="<?php echo htmlspecialchars($this->VARS['laz_top_font_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['laz_top_font_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Top Section Links Color</b><br>
        <small>This is the color for the links that appears above and below the guestbook. Default - #006699</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="top_link_color" value="<?php echo htmlspecialchars($this->VARS['top_link_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['top_link_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>    
    <tr> 
      <td class="lefttd"> <b>Top Section Number Color</b><br>
        <small>This is the color for the numbers at the top that say how many entries. Default - #DD0000</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="laz_top_num_color" value="<?php echo htmlspecialchars($this->VARS['laz_top_num_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['laz_top_num_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Table Header Background Color</b><br>
        <small>This is the background color of the section titles. Default - #EAF3FA</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="tb_hdr_color" value="<?php echo htmlspecialchars($this->VARS['tb_hdr_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['tb_hdr_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Table Header Strip Text Color</b><br>
        <small>This is the color of the text at the top of the sections. Default - #0A3E75</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="tb_text" value="<?php echo htmlspecialchars($this->VARS['tb_text']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['tb_text']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>    
      <td class="lefttd"> <b>Table Background Color</b><br>
        <small>This is the color of the borders. Default - #BBD8E7</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="tb_bg_color" value="<?php echo htmlspecialchars($this->VARS['tb_bg_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['tb_bg_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>First Alternating Table Column Color</b><br>
        <small>Default - #FFFFFF</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="tb_color_1" value="<?php echo htmlspecialchars($this->VARS['tb_color_1']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['tb_color_1']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Second Alternating Table Column Color</b><br>
        <small>Default - #EDFBFC</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="tb_color_2" value="<?php echo htmlspecialchars($this->VARS['tb_color_2']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['tb_color_2']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Search Box Background Color</b><br>
        <small>Default - #FFFFFF</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="search_bg_color" value="<?php echo htmlspecialchars($this->VARS['search_bg_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['search_bg_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Search Box Font Color</b><br>
        <small>Default - #000000</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="search_font_color" value="<?php echo htmlspecialchars($this->VARS['search_font_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['search_font_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td class="lefttd"> <b>Input Error Color</b><br>
        <small>Change the colour an input is changed to if it needs attention due to an error.<br />
        Default - #FFC0CB</small></td>
      <td class="righttd" valign="top"> 
        <input type="text" name="input_error_color" value="<?php echo htmlspecialchars($this->VARS['input_error_color']); ?>" size="10" maxlength="20" class="input">
      </td>
      <td class="displaytd"> 
        <table width="70" border="1" cellspacing="0" cellpadding="1" style="background-color:<?php echo $this->VARS['input_error_color']; ?>" bordercolor="#000000">
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>    
    <tr> 
      <td class="lefttd" valign="top"> <b>Error Box</b><br>
        <div style="<?php echo 'font-family:'.$this->VARS['font_face'].';background-color:'.$this->VARS['errorbox_back_color'].';color:'.$this->VARS['errorbox_font_color'].';border: '.$this->VARS['errorbox_border_width'].'px '.$this->VARS['errorbox_border_style'].' '.$this->VARS['errorbox_border_color'].';width:'.$this->VARS['width'].';margin: 10px 3px auto;padding: 3px;height:50px;"'; ?> id="errorBox">ERROR BOX STYLING</div>
      </td>
      <td class="righttd" valign="top" colspan="2"> 
        Border Color: <input type="text" name="errorbox_border_color" id="errorbox_border_color" value="<?php echo $this->VARS['errorbox_border_color']; ?>" size="10" maxlength="20" class="input"><br />
        Border Style: <select name="errorbox_border_style" id="errorbox_border_style" class="input">
          <option value="solid"<?php if($this->VARS['errorbox_border_style'] == 'solid') echo ' selected="selected"'; ?>>solid</option>
          <option value="dotted"<?php if($this->VARS['errorbox_border_style'] == 'dotted') echo ' selected="selected"'; ?>>dotted</option>
          <option value="dashed"<?php if($this->VARS['errorbox_border_style'] == 'dashed') echo ' selected="selected"'; ?>>dashed</option>
          <option value="double"<?php if($this->VARS['errorbox_border_style'] == 'double') echo ' selected="selected"'; ?>>double</option>
          <option value="groove"<?php if($this->VARS['errorbox_border_style'] == 'groove') echo ' selected="selected"'; ?>>groove</option>
          <option value="ridge"<?php if($this->VARS['errorbox_border_style'] == 'ridge') echo ' selected="selected"'; ?>>ridge</option>
          <option value="inset"<?php if($this->VARS['errorbox_border_style'] == 'inset') echo ' selected="selected"'; ?>>inset</option>
          <option value="outset"<?php if($this->VARS['errorbox_border_style'] == 'outset') echo ' selected="selected"'; ?>>outset</option>
          <option value="none"<?php if($this->VARS['errorbox_border_style'] == 'none') echo ' selected="selected"'; ?>>none</option>
        </select><br />
        Border Width: <input type="text" name="errorbox_border_width" id="errorbox_border_width" value="<?php echo $this->VARS['errorbox_border_width']; ?>" size="1" maxlength="1" class="input">px<br />
        Font Color: <input type="text" name="errorbox_font_color" id="errorbox_font_color" value="<?php echo $this->VARS['errorbox_font_color']; ?>" size="10" maxlength="20" class="input"><br />
        Background Color: <input type="text" name="errorbox_back_color" id="errorbox_back_color" value="<?php echo $this->VARS['errorbox_back_color']; ?>" size="10" maxlength="20" class="input"><br />
        <a href="javascript:errorPreview();" style="display:none;" id="previewLink">preview error box changes</a>
      </td>
    </tr>
    <tr>
      <td class="lefttd" colspan="3">
        <p><input type="checkbox" name="external_css" id="external_css" <?php if ($this->VARS['external_css']) {echo "checked";}?>> <label for="external_css">Use external style sheet.</label></p>
        <p>If you prefer to use your own external style sheet to style the guestbook then select the above option. By default Lazarus will replace the class attributes in the templates with the relevant inline CSS.</p>
        <p>Here is the CSS that the guestbook outputs when used as a stand alone guestbook. Simply copy the CSS to get the class names to put into your own CSS file.</p>
        <div style="overflow: auto; border: 1px solid #000; padding: 5px; margin: 2px; background-color: #FFF;">
        <pre>
.lazTop a, .lazTop a:visited, .lazTop a:active { color: <?php echo htmlspecialchars($this->VARS['top_link_color']); ?>; }
.lazTop table a, .lazTop table a:visited, .lazTop table a:active { color: <?php echo htmlspecialchars($this->VARS['link_color']); ?>; }        
.font1 { font-family: <?php echo htmlspecialchars($this->VARS['font_face']); ?>; font-size: <?php echo htmlspecialchars($this->VARS['tb_font_1']); ?>; color: <?php echo htmlspecialchars($this->VARS['text_color']); ?>; }
.font2 { font-family: <?php echo htmlspecialchars($this->VARS['font_face']); ?>; font-size: <?php echo htmlspecialchars($this->VARS['tb_font_2']); ?>; color: <?php echo htmlspecialchars($this->VARS['text_color']); ?>; }
.font3 { font-family: Arial, Helvetica, sans-serif; font-size: 7.5pt; color: <?php echo htmlspecialchars($this->VARS['text_color']); ?>; font-weight: bold; }
.lazTop { background: <?php echo htmlspecialchars($this->VARS['pbgcolor']); ?>; font-family: <?php echo htmlspecialchars($this->VARS['font_face']); ?>; font-size: <?php echo htmlspecialchars($this->VARS['tb_font_2']); ?>; color: <?php echo htmlspecialchars($this->VARS['laz_top_font_color']); ?>; }
.lazTopNum { color: <?php echo htmlspecialchars($this->VARS['laz_top_num_color']); ?>; font-weight:bold; }
.select { font-family: <?php echo htmlspecialchars($this->VARS['font_face']); ?>; font-size: 9pt; }
.input { font-family: <?php echo htmlspecialchars($this->VARS['font_face']); ?>; font-size: 9pt; }
.gbsearch { font-family: <?php echo htmlspecialchars($this->VARS['font_face']); ?>; font-size: <?php echo htmlspecialchars($this->VARS['tb_font_1']); ?>; color: <?php echo htmlspecialchars($this->VARS['search_font_color']); ?>; background: <?php echo htmlspecialchars($this->VARS['search_bg_color']); ?>; }
        </pre>
        </div>
      </td>
    </tr>
  </table>
  <script type="text/javascript">
  document.getElementById('previewLink').style.display = '';
  function errorPreview() {
    errorBox = document.getElementById('errorBox');
    errorBox.style.borderColor = document.getElementById('errorbox_border_color').value;
    errorBox.style.borderStyle = document.getElementById('errorbox_border_style').value;
    errorBox.style.borderWidth = document.getElementById('errorbox_border_width').value + 'px';
    errorBox.style.color = document.getElementById('errorbox_font_color').value;
    errorBox.style.background = document.getElementById('errorbox_back_color').value;
  }
  </script>  
<?php
}
if($section == 'date')
{ 
?>
<table>
  <tr>
    <td colspan="2" height="25" class="section">Date/Time Display Options</td>
  </tr>
  <tr>
    <td colspan="2" class="subsection">
      <b>This Guestbook can display dates and times in a number of different formats.</b>
      </td>
  </tr>
<!--  <tr class="lefttd">
    <td width="55%">
      <b><font size="2" face="Verdana, Arial">Server Time Zone Offset</font></b><font size="1" face="Verdana, Arial"><br>
        You can offset the time drawn from your web server. For instance,
        if your server time is EST (US), but you want all time to reflect Pacific
        Time (US), you would have to offset your server time by placing the
        time zone difference in this field (for this example, that would be
        -3. You would place -3 in this field). The default is for there to be
        no server time zone offset (0).</font>

    </td>
    <td width="45%" valign="top"><input type="text" name="offset" value="<?php echo $this->VARS['offset']; ?>" size="3" maxlength="4" class="input"><br>
          <font size="1">The offset stated below is only offered as a guide</font><br>
          <table border="0" bgcolor="#000000" cellspacing="1" cellpadding="2">
         <tr>
            <td bgcolor="#f7f7f7" align="center"><font size="1">The time on the server is:</font></td>
            <td bgcolor="#f7f7f7" align="center"><font size="1">The time on your computer is:</font></td>
            <td bgcolor="#f7f7f7" align="center"><font size="1">The offset is:</font></td>
         </tr>
         <tr>
            <td bgcolor="#f7f7f7" align="center"><font size="1"><?php echo(date("l H:i")); ?></font></td>
            <td bgcolor="#f7f7f7" align="center"><font size="1"><script type="text/javascript">

</script>
         </font></td>
         </tr>
      </table>
      </td>
  </tr> -->
  <tr class="lefttd">
    <td width="55%">
      <b><font size="2" face="Verdana, Arial">Time Zone</font></b><font size="1" face="Verdana, Arial"><br>
        Please specify what timezone you would like your site to use. This will alter the times displayed on posts to match that of where you are.</font>
    </td>
    <td width="45%" valign="top">
<?php
$utc = new DateTimeZone('UTC');
$dt = new DateTime('now', $utc);

echo '<select name="offset">';
foreach(DateTimeZone::listIdentifiers() as $tz) {
    $current_tz = new DateTimeZone($tz);
    $offset =  $current_tz->getOffset($dt);
    //$transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
    //$abbr = $transition[0]['abbr'];
    $isSelected = ($tz == $this->VARS['offset']) ? ' selected="selected"' : '';
    echo '<option value="' . $tz . '"' . $isSelected . '>' . $tz . ' (UTC'. sprintf('%+03d:%02u', floor($offset / 3600), floor(abs($offset) % 3600 / 60)) . ')</option>';
    //echo '<option value="' . $tz . '"' . $isSelected . '>' . $tz . ' (' . $abbr . ') (UTC'. sprintf('%+03d:%02u', floor($offset / 3600), floor(abs($offset) % 3600 / 60)) . ')</option>';
}
echo '</select>';
?></td>
  </tr>
  <tr class="righttd">
    <td width="55%" valign="top"> <b><font size="2" face="Verdana, Arial">Date Format</font></b> <font size="1" face="Verdana, Arial"><br>
      European Format is DD-MM-YR, while US format is MM-DD-YR. Expanded formats
      include full month name.</font>
    </td>
    <td width="45%" valign="top"> <font size="2" face="Verdana, Arial">
      <input type="radio" name="dformat" id="ISO" value="ISO" <?php if ($this->VARS['dformat'] == "ISO") {echo "checked";}?>>
      <label for="ISO">International Standard Format (2000-04-17)</label><br> 
      <input type="radio" name="dformat" id="USx" value="USx" <?php if ($this->VARS['dformat'] == "USx") {echo "checked";}?>>
      <label for="USx">US Format (04-17-2000)</label><br>
      <input type="radio" name="dformat" id="US" value="US" <?php if ($this->VARS['dformat'] == "US") {echo "checked";}?>>
      <label for="US">Exp. US Format (Monday, April 25, 2000)</label><br>
      <input type="radio" name="dformat" id="Eurox" value="Eurox" <?php if ($this->VARS['dformat'] == "Eurox") {echo "checked";}?>>
      <label for="Eurox">European Format (17.04.2000)</label><br>
      <input type="radio" name="dformat" id="Euro" value="Euro" <?php if ($this->VARS['dformat'] == "Euro") {echo "checked";}?>>
      <label for="Euro">Exp. European Format (Monday, 25 April 2000)</label></font></td>
  </tr>
  <tr class="lefttd">
    <td width="55%"> <b><font size="2" face="Verdana, Arial">Smart Time</font></b> <font size="1" face="Verdana, Arial"><br>
      Display date as <strong>seconds ago</strong>, <strong>minutes ago</strong>, <strong>Today at</strong> and <strong>Yesterday at</strong> where appropriate.</font></td>
    <td width="45%" valign="top"> <font size="2" face="Verdana, Arial">
      <input type="checkbox" name="smarttime" id="smarttime" <?php if ($this->VARS['smarttime']) {echo "checked";}?>> <label for="smarttime"><small>Use Smart time</small></label></font></td>
  </tr>  
  <tr class="righttd">
    <td width="55%"> <b><font size="2" face="Verdana, Arial">Time Format</font></b> <font size="1" face="Verdana, Arial"><br>
      You can have time displayed in AM/PM format, or in 24-hour format.</font></td>
    <td width="45%" valign="top"> <font size="2" face="Verdana, Arial">
      <input type="radio" name="tformat" id="AMPM" value="AMPM" <?php if ($this->VARS['tformat'] == "AMPM") {echo "checked";}?>>
      <label for="AMPM">Use AM/PM Time Format</label><br>
      <input type="radio" name="tformat" id="24hr" value="24hr" <?php if ($this->VARS['tformat'] == "24hr") {echo "checked";}?>>
      <label for="24hr">User 24-Hour Format Time (eg, 23:15)</label></font></td>
  </tr>
</table>
<?php
}
if($section == 'adblock')
{ 
?>
<table>
  <tr>
    <td colspan="2" height="25" class="section">Ad Block</td>
  </tr>
  <tr>
    <td colspan="2" class="subsection">
      <b>Display an advert in your guestbook entries.</b>
      </td>
  </tr>
  <tr class="lefttd">
    <td width="55%"> <b><font size="2" face="Verdana, Arial">Position</font></b> <font size="1" face="Verdana, Arial"><br>
      Here you can specify at what position you want the ad code to appear. 1 will be the very first entry, 2 the second and so on. 
      Set this to 0 to disable the code block.</font></td>
    <td width="45%" valign="top"><select name="ad_pos" class="input">
    <?php
    for ($i=0;$i<=($this->VARS['entries_per_page'] + 1);$i++)
    {
      echo '<option value="'.$i.'"';
      if ($i == $this->VARS['ad_pos'])
      {
         echo ' selected';
      }
      echo '>'.$i."</option>\n";
    }
    ?>
    </td>
  </tr>
  <tr class="righttd">
   <td width="55%" valign="top"><b><font size="2" face="Verdana, Arial">Ad Code</font></b><br>
   <font size="2" face="Verdana, Arial">Place the code you wish to appear in the guestbook into the textarea on the right.
    <p>You can use HTML in here.</p>
    <p>A preview will appear below once you have saved your code.</p>
    </font>
   <td width="45%" valign="top"><textarea cols="41" rows="14" wrap="virtual" name="ad_code" id="ad_code" class="input"><?php echo htmlspecialchars($this->VARS['ad_code']); ?></textarea>
   <div class="textarea_resize"><img src="<?php echo $this->VARS['base_url']; ?>/img/up.gif" alt="up" title="shrink textarea" onclick="resizeTextarea('ad_code','up');" /><br /><img src="<?php echo $this->VARS['base_url']; ?>/img/down.gif" alt="up" title="enlarge textarea" onclick="resizeTextarea('ad_code','down');" /></div></td>
  </tr>
  <?php
  if (!empty($this->VARS['ad_code']))
  {
  ?>
  <tr class="lefttd">
   <td valign="top" colspan="2">
   <b><font size="2" face="Verdana, Arial">Preview</font></b><br>
   <div style="overflow: auto; border: 1px solid #000; padding: 5px; margin: 2px; background-color: #FFF;">
     <?php
       echo $this->VARS['ad_code'];
     ?>
   </div>
   </td>
  </tr> 
  <?php
  }
}
  ?>
</table>
 <br>
  <center>
    <input type="submit" class="submit" value="Submit Settings">
    <input type="reset" class="reset" value="Reset">
    <input type="hidden" value="<?php echo $this->uid; ?>" name="uid">
    <input type="hidden" value="<?php echo $this->gbsession; ?>" name="gbsession">
    <input type="hidden" value="save" name="action">
    <input type="hidden" value="style" name="panel">
    <input type="hidden" value="<?php echo $section; ?>" name="section">
  </center>
</form>
</div>
