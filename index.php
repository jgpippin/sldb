<?php

/**
 * SLDB: SIMPLE DATABASE STORAGE FOR LSL 1.1
 * Copyright (C) 2009 aubreTEC Labs
 * http://aubretec.com/products/sldb
 *
 * This program is free software. You can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License.
 */

// Include the config file.
require_once('config.php');

// Extract the arguments from the path.
$path = explode('/', strtok($_SERVER['REQUEST_URI'], '?'));
$self = explode('/', $_SERVER['PHP_SELF']);
end($self);
$args = array_slice($path, key($self));

// Break apart the path to find the endpoint.
$action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : strtolower($args[0]);

// Ensure that non-install requests have all the required fields.
if ($action != 'install') {

	// Ensure that the request is authenticated.
	if ($_REQUEST['secret'] != $secret) {
		die("ERROR: NOT AUTHENTICATED");
	}

	// Extract the UUID and fields from the request. Verbose and reverse can be 
	// 'true', 'yes', or 1.
	$uuid   = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : $args[1];
	$fields = $_REQUEST['fields'];
	$verbose = in_array(strtolower($_REQUEST['verbose']), array('yes', 'true', 1));

	// If no key is provided, or the request is a reverse lookup without fields, or
	// the request is a put without values, this will fail.
	if ((empty($uuid) || (empty($fields) && $action != 'read'))) {
		die("ERROR: INSUFFICIENT ARGUMENTS");
	}

}


// Start a new request.
require_once('sldb.php');
$request = new sldbRequest($db_host, $db_user, $db_pass, $db_name, $db_table);

// Take an action; these are all based on the CRUD model.
switch ($action) {
	case 'install':
		$request->createTable();
		break;

	case 'create':
	case 'update':
		$request->updateData($uuid, $fields, $verbose);
		break;

	case 'read':
		$request->readData($uuid, $fields, $verbose);
		break;

	case 'delete':
		$request->deleteData($uuid, $fields, $verbose);
		break;
}

print $request->getOutput();

?>