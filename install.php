<?php

######################################################################
# SLDB: Simple Database Storage for LSL 1.02
######################################################################
# Copyright (C) 2009 aubreTEC Labs
# http://aubretec.com/products/sldb
#
# This program is free software. You can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License.
######################################################################

######################################################################
# INSTALL FILE
#
# This file creates the tables and fields necessary to operate this
# set of scripts. It gets the values it needs from the config.php file,
# so if you're getting errors it's probably because config.php isn't
# filled out with the right data.  Once you're done creating the table
# you must delete the install.php file, otherwise anyone could come
# along and wipe all your data, which would royally suck.
######################################################################

include 'config.php';

$action = $_POST['action'];

function install($table) {
	$sql = "DROP TABLE IF EXISTS $table;";
	$result = mysql_query($sql) or die("Error creating table: ".mysql_error());
	
	$sql = "CREATE TABLE IF NOT EXISTS $table (
  			`id` int(11) NOT NULL auto_increment,
  			`uuid` varchar(36) default NULL,
  			`field` varchar(100) default NULL,
  			`value` varchar(1024) default NULL,
  			`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54;";
		
	$result = mysql_query($sql) or die("Error formatting table: ".mysql_error());
	return $result;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title></title>
</head>
<body
 style="margin: 100px auto auto; color: rgb(51, 51, 51); background-color: rgb(204, 204, 255); width: 700px; font-family: lucida grande,arial; font-size: 12px;"
 link="#000099" alink="#000099" vlink="#990099">
<h1 style="margin: 0px;">SL&#187;DB INSTALLATION
</h1>
<table
 style="border: 1px solid grey; background-color: white; text-align: left; width: 100%; padding:10px;"
 cellpadding="2" cellspacing="2">
  <tbody>
    <tr>
      <td>
      
      <?php 
      	mysql_connect($dbhost, $dbuser, $dbpass) or die ();
mysql_select_db($dbname) or die("Error selecting database: ".mysql_error());
      	if($action == 'confirm') {
      		if(install($dbtable)) { ?>
      			<p>Your table has been formatted!  You <b>must</b> delete this file (install.php) now.</p>
      		<? }
			else {
				echo "Oops! There were some problems. You should check your config.php file and try again.";
			}
      	} else {?>
      
      <p>This file installs your database table (<?php echo $dbtable; ?>). &nbsp;If
this table already exists, all of your data will be wiped clean.  This action cannot be reversed.</p>
<p>Are you sure you want to do this?</p>
      <br>
      <form method="post" action="install.php"
 name="install">
        <input name="action" value="confirm" type="hidden"><input
 value="Confirm" type="submit">
      </form>
      <?php } ?>
      
      </td>
    </tr>
  </tbody>
</table>
<br>
</body>
</html>


