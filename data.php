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
# AUTHOR'S NOTE
#
# This script sucks.  No, really; I barely know enough php to say
# 'Hello world!"  Chances are, the syntax is terrible and the MySQL
# is not optimized.  Please don't judge me by my awful PHP; I'm an LSL
# guy.  If you have suggestions, I'm glad to hear them in the comments
# section of the page listed above.
#
# This software is provided as-is, with no express or implied warranty.
# Because it's free, I really can't afford to offer instant support.
# However, I'll keep an eye on the comments section of this page
# listed above and answer your questions if I can.  If you have 
# questions, please ask them there.
######################################################################

######################################################################
# DATA FILE
#
# This is the file you'll actually send your requests to.
######################################################################

######################################################################
# DATABASE CONNECTION
#
# If we've configured correctly, the config.php file should have our
# database information.  Create a connection to the database server
# and select the database.
######################################################################

//if(file_exists('install.php')) die ("ERROR: INSTALL FILE NOT DELETED");

include 'config.php';

mysql_connect($dbhost, $dbuser, $dbpass) or die ('ERROR: CANNOT CONNECT TO DATABASE.');
mysql_select_db($dbname) or die('ERROR: CANNOT SELECT DATABASE.');

######################################################################
# POST AND GET VARIABLES
#
# Surely there's a better way to do this, but at the moment, I'm 
# grabbing GET variables if they exist, and resorting to POST ones if
# they do not.  You MUST have a key value and field values; if you
# don't, you'll get an error from the server. Separate field variables
# using the pipe ("|") so we can break them up into an array.  Also,
# this script won't run if you haven't deleted install.php.
######################################################################

$key = $_REQUEST['key'];
$action = strtolower($_REQUEST['action']);
$fields = $_REQUEST['fields'];
$values = $_REQUEST['values'];
$verbose = strtolower($_REQUEST['verbose']);
$reverse = strtolower($_REQUEST['reverse']);
$separator = $_REQUEST['separator'];
$password = $_REQUEST['secret'];

$action = strtolower($action);
$verbose = strtolower($verbose);
$reverse = strtolower($reverse);

if($verbose == "yes" || $verbose == "true" || $verbose == 1) {
	$verbose = true;
} else {
	$verbose = false;
}

if($reverse == "yes" || $reverse == "true" || $reverse == 1) {
	$reverse = true;
} else {
	$reverse = false;
}

if($password != $secret) {
	die("ERROR: NOT AUTHENTICATED");
}

if($key == '' or (($fields == '') && ($reverse != true))) {
	die ("ERROR: INSUFFICIENT ARGUMENTS");
}

if(($action == "put") && ($values == '')) {
	die ("ERROR: INSUFFICIENT ARGUMENTS");
}

if($separator == '') {
	$separator = '|';
}

$fields = explode($separator,$fields);
$values = explode($separator,$values);

######################################################################
# FUNCTIONS
#
# These do all the dirty work.  I'm not going to describe them each in
# detail because I'm tired and I want a snack.
######################################################################

function combine_arrays($varfields, $varvalues)
{
   if(count($varfields) != count($varvalues)) {
   		die("ERROR: UNMATCHED PARAMETERS");
   }
   
   $result = array();
   while(($key = each($varfields)) && ($val = each($varvalues)))
   {
      $result[$key[1]] = $val[1];
   }
   return($result);
} 

function update_data($table, $varkey, $data, $varverb)
{	
	foreach($data as $f => $v) {
	
		$sql = "UPDATE $table SET
				value = '$v',
				timestamp = NOW()
				WHERE uuid = '$varkey' AND field = '$f'";
		$result = mysql_query($sql) or die("ERROR: SYNTAX");
		
		if(mysql_affected_rows() == 0) {
			$sql = "INSERT INTO $table (uuid,field,value,timestamp) 
					VALUES ('$varkey', '$f','$v',NOW())";
			$result = mysql_query($sql) or die("ERROR: SYNTAX");
		}
	}
	if($varverb == true) {
		return "SUCCESS: UPDATED ".count($data)." RECORDS.";
	}
	else return "SUCCESS: ".count($data);
}

function retrieve_values($table, $varkey, $data, $varverb, $varsep) {
	$return = array();
	
	if(in_array('ALL_DATA',$data))
	{
		$sql = "SELECT * FROM $table 
				WHERE uuid = '$varkey'";
		$result = mysql_query($sql) or die("ERROR: SYNTAX");
		while($row = mysql_fetch_assoc($result)) 
		{
			if($row['value'] == '') {
				$row['value'] = 'NO_DATA';
			}
			if($varverb) {
				$return[] = $row['field'];
			}
			$return[] = $row['value'];
   		}
	} else {
		foreach($data as $f) {
		
			$sql = "SELECT * FROM $table 
					WHERE uuid = '$varkey' AND field = '$f'";
			$result = mysql_query($sql) or die("ERROR: SYNTAX");
			$row = mysql_fetch_assoc($result);
			if(empty($row)) {
				$row['value'] = "NO_DATA";
			}
			if($varverb) {
				$return[] = $f;
			}
			$return[] = $row['value'];
		}
	}
	if(count($return) < 1) {
		return "NO_DATA";
	} else {
	 	return implode($varsep,$return);
	}
}

function retrieve_fields($table, $varkey, $data, $varverb, $varsep) {
	$return = array();
	
	if(in_array('ALL_DATA',$data))
	{
		$sql = "SELECT * FROM $table 
				WHERE uuid = '$varkey'";
		$result = mysql_query($sql) or die("ERROR: SYNTAX");
		while($row = mysql_fetch_assoc($result)) 
		{
			if($row['field'] == '') {
				$row['field'] = 'NO_DATA';
			}
			if($varverb) {
				$return[] = $row['value'];
			}
			$return[] = $row['field'];
   		}
	} else {
		foreach($data as $f) {
		
			$sql = "SELECT * FROM $table 
					WHERE uuid = '$varkey' AND value = '$f'";
			$result = mysql_query($sql) or die("ERROR: SYNTAX");
			$row = mysql_fetch_assoc($result);
			if(empty($row)) {
				$row['field'] = "NO_DATA";
			}
			if($varverb) {
				$return[] = $f;
			}
			$return[] = $row['field'];
		}
	}
	if(count($return) < 1) {
		return "NO_DATA";
	} else {
	 	return implode($varsep,$return);
	}
}

function delete_values($table, $varkey, $data, $varverb) {
	$rows;
	if(in_array('ALL_DATA',$data)) {
		$sql = "DELETE FROM $table 
				WHERE uuid = '$varkey'";
		$result = mysql_query($sql) or die("ERROR: SYNTAX");
		$rows += mysql_affected_rows();
	} else {
		foreach($data as $f) {
			$sql = "DELETE FROM $table 
					WHERE uuid = '$varkey' AND field = '$f'";
			$result = mysql_query($sql) or die("ERROR: SYNTAX");
			$rows += mysql_affected_rows();
		}
	}
	if($varverb == true) {
		return "SUCCESS: DELETED ".$rows." RECORDS.";
	}
	else return "SUCCESS: ".$rows;
}

function delete_fields($table, $varkey, $data, $varverb) {
	$rows;
	if(in_array('ALL_DATA',$data)) {
		$sql = "DELETE FROM $table 
				WHERE uuid = '$varkey'";
		$result = mysql_query($sql) or die("ERROR: SYNTAX");
		$rows += mysql_affected_rows();
	} else {
		foreach($data as $f) {
			$sql = "DELETE FROM $table 
					WHERE uuid = '$varkey' AND value = '$f'";
			$result = mysql_query($sql) or die("ERROR: SYNTAX");
			$rows += mysql_affected_rows();
		}
	}
	if($varverb == true) {
		return "SUCCESS: DELETED ".$rows." RECORDS.";
	}
	else return "SUCCESS: ".$rows;
}

######################################################################
# ACTIONS
#
# This section parses the "action" parameter and decides what to do.
######################################################################

switch($action) {
	case 'put':
		echo update_data($dbtable, $key,combine_arrays($fields, $values), $verbose);
		break;
	case 'get':
		if($reverse == true) {
			echo retrieve_fields($dbtable, $key, $values, $verbose, $separator);
		} else {
			echo retrieve_values($dbtable, $key, $fields, $verbose, $separator);
		}
		break;
	case 'del':
		if($reverse == true) {
			echo delete_fields($dbtable, $key, $values, $verbose);
		} else {
			echo delete_values($dbtable, $key, $fields, $verbose);
		}
		break;
}

?>