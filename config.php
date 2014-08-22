<?php

/**
 * SLDB: SIMPLE DATABASE STORAGE FOR LSL
 *
 * Copyright (C) 2009 aubreTEC Labs
 * http://aubretec.com/products/sldb
 *
 * This program is free software. You can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License.
 */


/**
 * DATABASE CONFIGURATION
 *
 * SLDB assumes you will be using a MySQL database.  For other database
 * types, some adjustments may be necessary.
 *
 * db_host:   Database hostname
 * db_user:   Database username
 * db_pass:   Database password
 * db_name:   Database name
 */

$db_host  = 'localhost';
$db_user  = 'root';
$db_pass  = 'root';
$db_name  = 'sldb';
$db_table = 'data';


/**
 * SECRET PIN
 *
 * Define an integer used in the requests to thwart sneaky people who may try
 * to access your data by guessing the address of your sldb.
 *
 * This isn't exactly military-grade security, by the way.  If you're storing
 * sensitive data like names, passwords, or credit card numbers, boy are YOU
 * using the wrong setup.
 *
 * Note: because of limitations of passing this through HTTP request, the secret
 * passphrase cannot contain certain symbols. Best to use captial and lowercase
 * letters and numbers if you're having trouble.
 */

$secret = 1123;

?>