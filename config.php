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
# DATABASE CONFIGURATION
# 
# SLDB assumes you will be using a MySQL database.  For other database
# types, some adjustments may be necessary.
#
# dbhost:   Database hostname
# dbuser:   Database username
# dbpass:   Database password
# dbname:   Database name
######################################################################

$dbhost = 'localhost';
$dbuser = 'your_username';
$dbpass = 'your_password';
$dbname = 'your_database';
$dbtable = 'sldb_data';

######################################################################
# SECRET PASSPHRASE
# 
# Define a passphrase used in the requests to thwart sneaky people who
# may try to access your data by guessing the address of your sldb
#
#
# This isn't exactly military-grade security, by the way.  If you're 
# storing sensitive data like names, passwords, or credit card 
# numbers, boy are YOU using the wrong setup.
#
# Note: because of limitations of passing this through GET, the secret
# passphrase cannot contain certain symbols. Best to use captial and
# lowercase letters and numbers if you're having trouble.
######################################################################

$secret = "Luc1s4w3s0m3"

######################################################################
#End of File:  That's it!  You're done with the config.
######################################################################

?>