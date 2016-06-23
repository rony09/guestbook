<?php 
/* 
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.2 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 20:03:34 GMT
 * ----------------------------------------------
 */

class gb_session extends gbook_sql
{

   var $expire = 7200;
   var $include_path;
   var $table;

   function __CONSTRUCT($path = '')
   {
      global $GB_TBL;
      $this->table =& $GB_TBL;
      parent::__CONSTRUCT();
      $this->connect();
      $this->include_path = $path;
   }

   function isValidSession($gbsession,$user_id)
   {
      $this->query("SELECT session, last_visit from ".LAZ_TABLE_PREFIX."_auth WHERE session='".addslashes($gbsession)."' and ID='".intval($user_id)."'");
      $row = $this->fetch_array($this->result);
      if ($row)
      {
         return ($this->expire + $row['last_visit'] > time()) ? $row['session'] : false;
      }
      else
      {
         return false;
      }
   }

   function isValidUser($user_id)
   {
      $this->query("SELECT username FROM ".LAZ_TABLE_PREFIX."_auth WHERE ID='".intval($user_id)."'");
      $this->fetch_array($this->result);
      return ($this->record) ? true : false;
   }

   function changePass($user_id,$new_password)
   {
      $this->query("UPDATE ".LAZ_TABLE_PREFIX."_auth SET password=PASSWORD('$new_password') WHERE ID='".intval($user_id)."'");
      return ($this->record) ? true : false;
   }

   function generateNewSessionID($user_id)
   {
      srand((double)microtime()*1000000);
      $gbsession = md5 (uniqid (rand()));
      $timestamp = time();
      $this->query("UPDATE ".LAZ_TABLE_PREFIX."_auth SET session='$gbsession', last_visit='$timestamp' WHERE ID='".intval($user_id)."'");
      return $gbsession;
   }

   function checkPass($username,$password)
   {
      $this->query("SELECT ID FROM ".LAZ_TABLE_PREFIX."_auth WHERE username='" . $this->escape_string($username) . "' and password=PASSWORD('" . $this->escape_string($password) . "')");
      $this->fetch_array($this->result);
      return ($this->record) ? $this->record['ID'] : false;
   }
   
   function checkCookiePass($username,$password)
   {
      global $GB_DB;
      $this->query("SELECT * FROM ".LAZ_TABLE_PREFIX."_auth");
      $this->fetch_array($this->result);
      if ((sha1($this->record['username'] . $GB_DB['user']) === $username) && (sha1($this->record['password']. $GB_DB['user']) === $password))
      {
         return $this->record['ID'];
      }
      else
      {
         return 0;
      }
   }   

   function checkSessionID()
   {
      global $username, $password, $gbsession, $uid, $GB_DB;
      if (isset($gbsession) && isset($uid)) 
      {
         return ($this->isValidSession($gbsession,$uid)) ? array('session' => $gbsession, 'uid' => $uid) : false;
      }
      elseif (isset($username) && isset($password))
      {
         if (get_magic_quotes_gpc())
         {
            $username = stripslashes($username);
            $password = stripslashes($password);
         }
         $ID = $this->checkPass($username,$password);
         if ($ID)
         {
            $gbsession = $this->generateNewSessionID($ID);
            if (isset($_POST['remember']))
            {
               $this->query("SELECT password FROM " . LAZ_TABLE_PREFIX . "_auth WHERE username='" . $this->escape_string($username) . "' and password=PASSWORD('" . $this->escape_string($password) . "')");
               $this->fetch_array($this->result);
               $thepass = $this->record['password'];
               setcookie('lgu', sha1($username . $GB_DB['user']), time() + 31536000);
               setcookie('lgp', sha1($thepass . $GB_DB['user']), time() + 31536000);
            }
            return array('session' => $gbsession, 'uid' => $ID);
         }
         else
         {
            return false;
         }
      }
      elseif (isset($_COOKIE['lgu']) && isset($_COOKIE['lgp']))
      {
         $lgu = preg_replace('/\W/','',$_COOKIE['lgu']);
         $lgp = preg_replace('/\W/','',$_COOKIE['lgp']);
         $ID = $this->checkCookiePass($lgu,$lgp);
         if ($ID)
         {
            setcookie('lgu', $lgu, time() + 31536000);
            setcookie('lgp', $lgp, time() + 31536000);            
            $gbsession = $this->generateNewSessionID($ID);
            return array('session' => $gbsession, 'uid' => $ID);
         }
         else
         {
            return false;
         }
      }      
      else
      {
         return false;
      }
   }
}
?>