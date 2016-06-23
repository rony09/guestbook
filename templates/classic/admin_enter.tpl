<script type="text/javascript">
// Function taken from DD-WRT
function setMask(state) 
{
  var adminStyle = 'width:280px;height:35px;border:1px solid #6D6D6D;background:#FFF;margin:0;font-size:26px;color:#555;';
  var OldInput = document.getElementById('lazPassword');
  if(!OldInput) return;
  
  var val = OldInput.value;
  var val_maxlength = OldInput.maxlength;
  var val_size = OldInput.size;
  var parent = OldInput.parentNode;
  var sibling = OldInput.nextSibling;
  var newInput = document.createElement('input');
  newInput.setAttribute('value', val);
  newInput.setAttribute('name', 'password');
  newInput.setAttribute('id', 'lazPassword');
  newInput.setAttribute('size', val_size);
  newInput.setAttribute('style', adminStyle);
  newInput.style.cssText = adminStyle;
  
  if (state == true)
  	newInput.setAttribute('type', 'text');
  else
  	newInput.setAttribute('type', 'password');
  
  parent.removeChild(OldInput);
  parent.insertBefore(newInput, sibling);
  newInput.focus();
}
</script>
<div style="text-align:center; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif;font-size:16px;color:#777">
  <div style="font-size:25px;margin: 20px 0;">
    Guestbook<br />
    Administration
  </div>
  $message
  <div style="background:#FBFBFB; padding:0; width:300px; border:1px solid #6D6D6D; margin: 0 auto;" id="laz_login">
    <form method="post" action="admin.php$adminVariables" style="width: 280px; text-align: left; padding: 10px 0; margin: 0 auto;">
      <div style="margin:5px 0 0 0;width: 280px;">
        $LANG[FormUser]
      </div>
      <div style="margin:0 0 5px 0;width: 280px;">
        <input type="text" name="username" size="30" style="width:280px;height:35px;border:1px solid #6D6D6D;background:#FFF;margin:0;font-size:26px;color:#555;">
      </div>
      <div style="margin:10px 0 0 0;width: 280px;">
        $LANG[FormPass] <span id="unmaskSpan" style="display:none;">(<input type="checkbox" id="lazUnmask" value="0" onclick="setMask(this.checked)"><label for="lazUnmask">$LANG[AdminUnmask]</label> )</span>
      </div>
      <div style="margin:0 0 5px 0;width: 280px;">
        <input type="password" id="lazPassword" name="password" size="30" style="width:280px;height:35px;border:1px solid #6D6D6D;background:#FFF;margin:0;font-size:26px;color:#555;">
      </div>
      <div style="margin: 10px 0 0 0;width: 280px;">
        <div style="float:right;">
          <input type="submit" value="$LANG[FormSubmit]" class="input">
        </div>
        <label for="remember">$LANG[AdminRemember]</label> <input type="checkbox" name="remember" id="remember">
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  document.getElementById('laz_login').style.MozBorderRadius = '5px';
  document.getElementById('laz_login').style.WebkitBorderRadius = '5px';
  if(document.getElementById('admin_error'))
  {
    document.getElementById('admin_error').style.MozBorderRadius = '5px';
    document.getElementById('admin_error').style.WebkitBorderRadius = '5px';
  }
  document.getElementById('unmaskSpan').style.display = '';
</script>
<br />