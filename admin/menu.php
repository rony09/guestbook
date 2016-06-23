<div id="sidemenu">
<ul id="menutitles">
  <li class="menutitle"><div>ADMIN</div>
    <ul class="menulinks">
      <li><a href="<?php echo $this->SELF.'?gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid; ?>">home</a></li>
      <li><a href="<?php echo $this->SELF.'?action=logout&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid; ?>">close admin</a></li>
<?php 
if(!empty($_COOKIE['lgu']) && !empty($_COOKIE['lgp']))
{ ?>
      <li><a href="<?php echo $this->SELF.'?action=logout&amp;gbsession=' . $this->gbsession . '&amp;delcookie=true&amp;uid=' . $this->uid; ?>">logout</a></li>
<?php 
} 
?>      
    </ul>
  </li>
  <li class="menutitle"><div>MESSAGES</div>
    <ul class="menulinks">
      <li><a href="<?php echo $this->SELF.'?action=show&amp;tbl=priv&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid; ?>">private</a></li>
      <li><a href="<?php echo $this->SELF.'?action=show&amp;tbl=gb&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid; ?>">public</a></li>
    </ul>
  </li>
  <li class="menutitle"><div>SETTINGS</div>
    <ul class="menulinks">
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=general&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=general'; ?>">general</a></li>
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=general&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=fields'; ?>">fields</a></li>
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=general&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=email'; ?>">email</a></li>
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=general&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=security'; ?>">security</a></li>
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=style&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=style'; ?>">style</a></li>
      <li><a href="<?php echo $this->SELF. '?action=smilies&amp;gbsession=' . $this->gbsession . '&amp;uid='.$this->uid; ?>">smilies</a></li>
      <li><a href="<?php echo $this->SELF. '?action=template&amp;gbsession=' . $this->gbsession . '&amp;uid='.$this->uid; ?>">templates</a></li>
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=style&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=date'; ?>">date / time</a></li>
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=style&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=adblock'; ?>">ad block</a></li>
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=pwd&amp;gbsession=' . $this->gbsession . '&amp;uid='.$this->uid; ?>">change password</a></li>
    </ul>
  </li>
  <li class="menutitle"><div>INFO</div>
    <ul class="menulinks">
      <li><a href="<?php echo $this->SELF. '?action=settings&amp;panel=style&amp;gbsession=' . $this->gbsession . '&amp;uid=' . $this->uid . '&amp;section=include'; ?>">include code</a></li>
      <li><a href="<?php echo $this->SELF. '?action=info&amp;section=stats&amp;gbsession=' . $this->gbsession .'&amp;uid=' . $this->uid ?>">statistics</a></li>
    </ul>
  </li>
  <li class="menutitle"><div>SUPPORT</div>
    <ul class="menulinks">
      <li><a href="http://carbonize.co.uk/Lazarus/Forum/" target=_"blank">forum</a></li>
      <li>&nbsp;</li>
      <li><a href="http://carbonize.co.uk/Lazarus/donate.php" target="_blank"><img src="img/Paypal.gif" alt="Donate via PayPal" title="Donate to Lazarus" style="margin-left: 16px;" /></a></li>
    </ul>
  </li>
</ul>
</div>
