<script language="JavaScript" type="text/Javascript">
<!--
var input_error_color = "$VARS[input_error_color]";
var lazFormStyle = new Array();
var flag=0;
showComs = '$LANG[BookMess12]';
hideComs = '$LANG[BookMess13]';

function reloadCaptcha()
{
  var randomnumber=Math.floor(Math.random()*1001);
  baseURL = '$GB_PG[base_url]';
  capFile = 'captcha.php';
  urlVariable = 'hash=$TIMEHASH';
  randVar = 'rand=' + randomnumber;
  document.images['lazCaptcha'].src = baseURL + '/' + capFile + '?' + urlVariable + '&amp;' + randVar;
}

function resetFlag(){
   flag = 0;
}

function checkForm() {
  var errorMessages = new Array();
  var errorNum = 0;
  for (var itm in lazFormStyle)
  {
    document.getElementById(itm).style.backgroundColor = lazFormStyle[itm];
  }
  document.getElementById('gb_user').value = trim(document.getElementById('gb_user').value);
  document.getElementById('gb_comment').value = trim(document.getElementById('gb_comment').value);
  if(document.getElementById('gb_user').value == '') {
    errorStyling('gb_user');
    errorMessages[errorNum++] = "$LANG[ErrorPost1]";
  }
  if(document.getElementById('gb_comment').value == '') {
    errorStyling('gb_comment');
    errorMessages[errorNum++] = "$LANG[ErrorPost2]";
    noComment = 1;
  }
  $EXTRAJS
  if(errorMessages.length > 0){
    errorAlert = errorMessages.join("\\n");
    alert(errorAlert);
    return false;
  }
  flag=1;
  window.onExit = resetFlag();
  return true;
}
//-->
</script>
<script type="text/javascript" src="$GB_PG[base_url]/lazjs.php?jspage=entryform"></script>
<script type="text/javascript" src="$GB_PG[base_url]/enlargeit.php"></script>
<script type="text/javascript" src="$GB_PG[base_url]/motionpackjs.php"></script>
<script type="text/javascript">
hs.graphicsDir = '$GB_PG[base_url]/img/hs/';
hs.outlineType = 'rounded-white';
</script>
<script type="text/javascript" src="$GB_PG[base_url]/dom-drag.js"></script> 
<div class="lazTop">
  <div style="padding: 3px;font-weight:bold;font-size:large;">
    $LANG[FormSelect]
  </div>
  <div style="clear: left; padding: 3px 3px 1px 3px">
    <div style="text-align:right;font-weight:bold;float:right;">
      <a href="$GB_PG[index]">$LANG[BookMess4]</a>
    </div>  
    <div>
      $LANG[BookMess9]
    </div>
  </div>
$extra_html
<form method="post" action="$GB_PG[comment]" name="book" onsubmit="return checkForm()" enctype="multipart/form-data">
  <table border="0" cellspacing="1" cellpadding="4" width="100%" align="center" bgcolor="$VARS[tb_bg_color]" class="font1">
    <tr>
     <td colspan="2" bgcolor="$VARS[tb_hdr_color]"><b><font size="2" face="$VARS[font_face]" color="$VARS[tb_text]">$LANG[BookMess3]:</font></b></td>
    </tr>

$GB_ENTRY

    <tr bgcolor="$VARS[tb_color_1]">
      <td width="32%" valign="top"><img src="$GB_PG[base_url]/img/comment.gif" width="18" height="13" alt="">$LANG[BookMess7]:<br>
      </td>
      <td>
        $display_tags
        <div id="LazSmileys" style="display: none;width:350px;padding:3px;margin:0;overflow:hidden;">
        <div id="theSmileys" style="text-align:center;">$LAZSMILEYS</div>
        </div>
        <textarea id="gb_comment" name="gb_comment" cols="41" rows="6" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);">$this->comment</textarea>
        $honeypot
      </td>
      </td>
    </tr>
    <tr bgcolor="$VARS[tb_color_1]">
      <td><img src="$GB_PG[base_url]/img/user.gif" alt="$LANG[FormName]" title="$LANG[FormName]" /> $LANG[FormName]:</td>
      <td><input type="text" name="gb_user" id="gb_user" size="42" maxlength="50" value="$this->user"></td>
    </tr>
$OPTIONAL
    <tr bgcolor="$VARS[tb_color_1]">
      <td><div align="left" class="font2">$HTML_CODE<br>$SMILE_CODE<br>$AG_CODE</div></td>
      <td>
        <input type="submit" name="action" value="$LANG[FormSubmit]" class="input" onclick="if(flag==1) return false;">
        <input type="reset" value="$LANG[FormReset]" class="input">
        <input type="hidden" name="gb_id" value="$id">
        <input type="hidden" name="gb_action$antispam" value="1">
      </td>
    </tr>
  </table>
 <script type="text/javascript">
  $footerJS
  </script> 
  </form>
  </div>
