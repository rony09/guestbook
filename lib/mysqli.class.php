<?php 
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Wed, 17 February 2016 20:00:19 GMT
 * ----------------------------------------------
 */

class gbook_sql
{

  var $conn_id;
  var $result;
  var $record;
  var $db = array();
//  var $port;

  function __CONSTRUCT()
  {
    global $GB_DB;
    $this->db =& $GB_DB;
  }

  function connect()
  {
    $this->conn_id = @mysqli_connect($this->db['host'],$this->db['user'],$this->db['pass']);
    if (!$this->conn_id)
    {
      $this->sql_error('Database Server Connection Error');
    }
    if (!mysqli_select_db($this->conn_id, $this->db['dbName']))
    {
      $this->sql_error('Database Selection Error');
    }
    return $this->conn_id;
  }

  function query($query_string)
  {
    $this->result = @mysqli_query($this->conn_id, $query_string);
    if (!$this->result && !defined('IS_INSTALLER'))
    {
      $this->sql_error('Database Query Error', $query_string);
    }
    return $this->result;
  }

  function fetch_array($query_id)
  {
    $this->record = mysqli_fetch_assoc($query_id);
    return $this->record;
  }
  
  function fetch_row($query_id)
  {
    $this->record = mysqli_fetch_row($query_id);
    return $this->record;
  }

  function num_rows($query_id)
  {
    return ($query_id) ? mysqli_num_rows($query_id) : 0;
  }

  function num_fields($query_id)
  {
    return ($query_id) ? mysqli_num_fields($query_id) : 0;
  }

  function free_result($query_id)
  {
    return mysqli_free_result($query_id);
  }

  function affected_rows()
  {
    return mysqli_affected_rows($this->conn_id);
  }
  
  function insert_id()
  {
    return mysqli_insert_id($this->conn_id);
  }
  
  function escape_string($str)
  {
    return mysqli_real_escape_string($this->conn_id, $str);
  }

  function close_db()
  {
    if($this->conn_id)
    {
      return mysqli_close($this->conn_id);
    }
    else
    {
      return false;
    }
  }
  
  function selfURL() 
  { 
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), '/')).$s; 
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); 
    return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; 
  }

  function sql_error($message, $query_string='')
  {
    global $TEC_MAIL;
    if(!defined('IS_INSTALLER'))
    {
      // Some plain English messages for known errors
      $knownCodes = array(
        1044 => 'The account you are using does not have access to the database specified.',
        1045 => 'Either the database username or password you have supplied in the config.inc.php file is incorrect<br />or the account you are using does not have access to the database specified.',
        1146 => 'One or more database tables are missing. Have you installed them? If not you need to visit install.php.'
      );
      $description = ($this->conn_id) ? mysqli_error($this->conn_id) : 'unknown error';
      $number = ($this->conn_id) ? mysqli_errno($this->conn_id) : 0;
      if (mysqli_connect_errno()) 
      {
        $description = mysqli_connect_error();
        $number = mysqli_connect_errno();
      }
      echo (isset($knownCodes[$number])) ? '<h3>'.$message.'</h3><h4>'.$knownCodes[$number].'</h4>' : '<h3>'.$message.'</h3><h4>'.$description.'</h4>';
      $error  = "<b>MySQL Error </b> : $message<br />\n";
      $error .= (!empty($query_string)) ? "<b>Query</b>        : $query_string<br />\n" : '';
      $error .= "<b>Error Number</b> : $number $description<br />\n";
      $error .= "<b>Requested URI</b>: ".$this->selfURL()."<br />\n";
      $error .= "<b>Date</b>         : ".date("D, F j, Y H</b>:i</b>:s")."<br />\n";
      $error .= "<b>IP</b>           : ".$_SERVER['REMOTE_ADDR']."<br />\n";
      $error .= "<b>Browser</b>      : ".$_SERVER['HTTP_USER_AGENT']."<br />\n";
      $error .= "<b>Referer</b>      : ".getenv("HTTP_REFERER")."<br />\n";
      $error .= "<b>PHP Version</b>  : ".PHP_VERSION."<br />\n";
      $error .= "<b>OS</b>           : ".PHP_OS."<br />\n";
      $error .= "<b>Server</b>       : ".getenv("SERVER_SOFTWARE")."<br />\n";
      $error .= "<b>Server Name</b>  : ".getenv("SERVER_NAME")."<br />\n";
      //echo '<pre>'.$error.'</pre>';
      if (preg_match('/^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\\.)+[a-z]{2,5}$/i', $TEC_MAIL))
      {
        $headers = 'From: '.$TEC_MAIL."\nX-Mailer: Lazarus Guestbook\nContent-type: text/html;";
        $error = (isset($knownCodes[$number])) ? $knownCodes[$number] . "\n\n" . $error : $description . "\n\n" . $error;
        @mail($TEC_MAIL,'Guestbook - Error', $error, $headers);
      }
      exit();
    }
  }
}