<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php
$_GET['show'] = htmlspecialchars($_GET['show']);
echo( $_GET['show']);
?></title>
<meta content="text/html; charset=windows-1252" http-equiv=Content-Type>
<style type="text/css">
<!--
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; }
-->
</style>
</head>
<body bgColor="#ffffff" link="#000080" text="#000000" vLink="#000080">
<script language="javascript" type="text/javascript">
<!--
function emoticon(text) {
	text = ' ' + text + ' ';
	if (opener.document.forms['book'].gb_comment.createTextRange && opener.document.forms['book'].gb_comment.caretPos) {
		var caretPos = opener.document.forms['book'].gb_comment.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		opener.document.forms['book'].gb_comment.focus();
	} else {
	opener.document.forms['book'].gb_comment.value  += text;
	opener.document.forms['book'].gb_comment.focus();
	}
}
//-->
</script>
<center>
<?php if($_GET['show'] == 'smilies')
{
?>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25"> W H A T &nbsp;&nbsp;&nbsp;A R E&nbsp;&nbsp;&nbsp;S M I L I E S ?</td>
    </tr>
    <tr>
      <td>
        <p>'Smilies', or emoticons, are small graphical images that can be used to convey an
          emotion or feeling. If you have used email or internet chat, you are
          likely familiar with the smiley concept. Certain standard strings are
          automatically converted into smilies. Try twisting your head on one
          side if you do not get smilies; using a bit of imagination should reveal
          a face of some description.</p>
        <p> Here's the list of currently accepted smilies: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border="0" width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color="#FFFFFF"><b>What to Type</b></font></td>
      <td bgcolor="#996699"><font color="#FFFFFF"><b>Graphic That Will Appear</b></font></td>
      <td bgcolor="#996699"><font color="#FFFFFF"><b>Emotion</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
<?php 
}
elseif ($_GET['show'] == 'agcode')
{
?>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">W H A T &nbsp;&nbsp;&nbsp;I S &nbsp;&nbsp;&nbsp;A D V A
        N C E D &nbsp;&nbsp;&nbsp;G U E S T B O O K &nbsp;&nbsp;&nbsp;C O D E ? </td>
    </tr>
    <tr>
      <td>
        <p>AGCode is a variation on the HTML tags you may already be familiar
          with. Basically, it allows you to add functionality or style to your
          message that would normally require HTML. You can use AGCode even if
          HTML is not enabled for the guestbook. You may want to use
          AGCode as opposed to HTML, even if HTML is enabled for the guestbook,
          because there is less coding required and it is safer to use (incorrect
          coding syntax will not lead to as many problems).
        <p>Current AGCodes:
      </td>
    </tr>
  </table>
<table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
  <tbody>
  <tr>
    <td bgcolor="#000000">
      <table border="0" cellpadding="4" cellspacing="1" width="100%">
        <tbody>
        <tr bgcolor="#0099CC">
          <td><b><font color="#FFFFFF">URL Hyperlinking</font></b></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>If AGCode is enabled, you no longer need to use the
            [URL] code to create a hyperlink. Simply type the complete URL in
            either of the following manners and the hyperlink will be created
            automatically:
            <ul>
              <li><font color="#800000">http://www.yourURL.com </font>
              <li><font color="#800000">www.yourURL.com </font>Notice that you can
                either use the complete http:// address or shorten it to the www
                domain. If the site does not begin with &quot;www&quot;, you must use the
                complete &quot;http://&quot; address. Also, you may use https and ftp URL
                prefixes in auto-link mode (when AGCode is ON). <br>
                <br>
              <li>You can also have true hyperlinks using the [url] code. Just
                use the following format: <br><br>
                <center>
                  <font color="#FF0000">[url=http://www.proxy2.de]</font>hyperlink<font color="#FF0000">[/url]</font>
                </center><br><br>
              <li>
                <p>The old [URL] code will still work, as detailed below. Just
                  encase the link as shown in the following example (AGCode is
                  in <font color="#FF0000">red</font>).
                <p>
                  <center>
                    <font color="#FF0000">[url]</font>http://www.proxy2.de<font color="#FF0000">[/url]</font>
                  </center>
                <p>In the examples above, the AGCode automatically generates
                  a hyperlink to the URL that is encased. It will also ensure
                  that the link is opened in a new window when the user clicks
                  on it. Note that the &quot;http://&quot; part of the URL is completely
                  optional. In the second example above, the URL will hypelink
                  the text to whatever URL you provide after the equal sign. Also
                  note that you should NOT use quotation marks inside the URL
                  tag. </p>
              </li>
            </ul>
          </td>
        <tr bgcolor="#0099CC">
          <td><b><font color="#FFFFFF">Email Links</font></b></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>To add a hyperlinked email address within your message, just encase
            the email address as shown in the following example (AGCode is in
            <font color="#FF0000">red</font>).
            <p>
              <center>
                <font color="#FF0000">[email]</font>webmaster@proxy2.de<font color="#FF0000">[/email]</font>
              </center>
            <p>In the example above, the AGCode automatically generates a hyperlink
              to the email address that is encased. </p>
          </td>
        </tr>
        <tr bgcolor="#0099CC">
          <td><b><font color="#FFFFFF">Bold and Italics</font></b></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>You can make italicized text or make text bold by encasing the applicable
            sections of your text with either the [b] [/b] or [i] [/i] tags.
            <p>
              <center>
                Hello, <font color="#FF0000">[b]</font><b>John</b><font color="#FF0000">[/b]</font><br><br>
                Hello, <font color="#FF0000">[i]</font><i>Maria</i><font color="#FF0000">[i]</font>
              </center>
          </td>
        </tr>
        <tr bgcolor="#0099CC">
          <td><b><font color="#FFFFFF">Adding Images</font></b></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>To add a graphic within your message, just encase the URL of the
            graphic image as shown in the following example (AGCode is in <font color="#FF0000">red</font>).
            <p>
              <center>
                <font color="#FF0000">[img]</font>http://www.yourURL.com/image/logo.gif<font color="#FF0000">[/img]</font>
              </center>
            <p>In the example above, the AGCode automatically makes the graphic
              visible in your message. Note: the &quot;http://&quot; part of the URL is
              REQUIRED for the <font color="#FF0000">[img]</font> code.</p>
          </td>
        </tr>
        <tr bgcolor="#0099CC">
          <td><b><font color="#FFFFFF">Adding Flash</font></b></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>To use flash within your message, just encase the URL of the
            flash file as shown in the following example (AGCode is in <font color="#FF0000">red</font>).
            <p>
              <center>
                <font color="#FF0000">[flash]</font>http://www.yourURL.com/image/funny.swf<font color="#FF0000">[/flash]</font><br>
                <font color="#FF0000">[flash]</font>http://www.youtube.com/watch?v=pz0XNGJ-ep8<font color="#FF0000">[/flash]</font>
              </center>
            <p>In the example above, the AGCode automatically makes the flash
              visible in your message. Note: the &quot;http://&quot; part of the URL is
              REQUIRED for the <font color="#FF0000">[flash]</font> code.</p>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  </tbody>
</table>
<table width="95%" border="0" cellspacing="1" cellpadding="4" align="center">
  <tr>
    <td><font color="#800000">Of Note</font><br>
      You must not use both HTML and AGCode to do the same function. Also note
      that the AGCode is not case-sensitive (thus, you could use <font color="#FF0000">[URL]</font>
      or <font color="#FF0000">[url]</font>).<br><br>
      <font color="#800000">Incorrect AGCode Usage:</font> <br>
       <font color="#ff0000">[url]</font> www.proxy2.de <font color="#FF0000">[/url]</font> - don't put spaces between the bracketed code and
        the text you are applying the code to.<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@proxy2.de<font color="#FF0000">[email]</font> - the end brackets must include a forward slash (<font color="#FF0000">[/email]</font>)
    </td>
  </tr>
</table>
<?php
 } 
 else
 { 
 echo ("Nothing requested");
 }
 ?>
</center>
<br>
</body>
</html>