# PHP guestbook

Lazarus Guestbook
By Stewart Souter
URL: http://carbonize.co.uk
Advanced Guestbook 2.3.x (PHP/MySQL)
Copyright (c)2001 Chi Kien Uong
URL: http://www.proxy2.de

Requirements:

  - MySQL 3.22.x or higher
  - PHP 5.2.x or higher

#Installation:

1. Open the configuration file 'config.inc.php' with a text editor
   and set up your MySQL database settings. 

2. Upload the guestbook to your website.

3. Run the install script http://www.yourDomain.com/install.php or http://localhost/<project_folder_name>/install.php
   and follow the instructions.

4. Give write permissions to these directories:

    - public - 777 (drwxrwxrwx) (directory)
    - tmp    - 777 (drwxrwxrwx) (directory)
    
5. The default account is:

   Username : test
   Password : 123

   But you can specify different ones in the install process.

6. Delete the install.php file.

#Updating:

1. Make a backup of your current guestbook files and if you know how the database.

2. Upload new files to your guestbook folder replacing the current ones.

3. Visit install.php in your web browser and choose update.

4. Delete the install.php file.
