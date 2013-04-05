# SLDB: SIMPLE MYSQL STORAGE FOR LSL (version 1.1)

Copyright (C) 2013 aubreTEC Labs  
<http://aubretec.com/products/sldb>

This program is free software. You can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License.

## Overview
This set of scripts is designed to run on your own webserver, in order to allow you to easily store and retrieve information from Second Life using the llHTTPRequest() function.  It allows you to store data per Second Life user in field/data pairs.  You don't have to use it this way, but that's what it was designed for.

## Basics
Each record stored has three variables:  KEY, FIELD, VALUE.

* UUID -   The key variable is generally designed to store data by SL user keys or uuids. 
* FIELD - This is an identifier for the data you're storing.
* VALUE - And this is the actual information you want to store.
    
For instance, if you were using this to store a user's preferences for prim shoes, you might want to store the following fields: color, size, and laces. Assuming the user has selected an unlaced, size 8, red shoe, you would store these as three separate records:

     [uuid], color, <1,0,0>
     [uuid], size, 8
     [uuid], laced, no

where [uuid] is the user's uuid.  

This is about as flexible a storage solution as I could come up with without getting into your individual needs.  If you need something more flexible, you should write your own script.

## Requirements
You will need your own hosting account with PHP/MySQL. If you don't know what those are, go learn and come back when you do. Typically speaking, you can find PHP/MySQL hosting on the cheap (less than $5/mo) all over the place.  

Most of the detailed instructions here assume you're running a hosting account with cPanel, but that's just because I'm lazy and can't be bothered to learn about other shared hosting setups.  As long as your account has PHP and MySQL, you're good to go - read the documentation on your hosting account to learn how to set up your MySQL database.

You'll also need an FTP client of some kind (or cPanel's File Manager) and know how to use it. It probably wouldn't hurt to learn how to use phpMyAdmin either, but you don't need it to set up this script.

You don't need your own domain name, but this guide assumes you have one, so you'll need to adjust the instructions accordingly.  Also, domain names are only $10, so cough up and get one, you cheap bastard.

## Installation

This part looks really long, but that's because there's a lot of extra details added in for folks who are new to this stuff.  If you've ever
installed Wordpress, it's mostly the same stuff, only with less files to upload and less stuff to fill out.

### Step 1: Create a MySQL database and user

You'll need to create a MySQL database and a user account to manage that database.  If you're on a cPanel account, this is best accomplished by clicking the "MySQL Database Wizard" icon. Otherwise, follow your hosting service's instructions.

You should give your user full privileges to the database you create (at the very least SELECT, INSERT, UPDATE, and DELETE). Then make a note of the database name, the user name, and the password. 

IMPORTANT: If you have a cPanel account, you should know that your  database name is actually a combination of your login name and the  name you chose. The same is true of your database username.  So if  your username is "dimwit" and you create a database named "sldb" and  a user named "sldbadmin" the actual names are "dimwit_sldb" and  "dimwit_sldbadmin," respectively.

Let's assume your database details are as follows:

            Database Name: dimwit_sldb
            Database User: dimwit_sldbadmin
            Database Pass: sekret
            
Okay, you're ready.  Let's move on to...

### Step 2: Config File

Unzip the files in this package (though chances are you've already done that if you're reading this README) and open "config.php" in your fave plain text editor (if you open it in Microsoft Word, I'll punch you in the mouth).

Fill out the values listed in that file with the database details you just created.  For the most part, you don't need to change "localhost."

You can also select a database table name, though it's really not necessary.  You can leave the default of "sldb_data."

In fact, if you know enough about PHP/MySQL to NEED to change that value, you should probably be writing your own solution rather than using my crappy one.

### Step 3: Upload the files

Upload the three files (config.php, data.php, and install.php) to a folder on your webserver.  Note the location of this.  For the purposes  of this guide, we'll assume it's this:
                
                    http://www.example.com/sldb/

You don't have to call the folder sldb, but remember the name of it. 

Typically speaking, this folder should go in the /www/ or /public_html/  folder (different for some hosts).

### Step 4: Run install.php

Visit install.php on your server (in our example, it should be at http://www.example.com/sldb/install.php).  Confirm that you want
to install the table.

*IMPORTANT: Don't install twice.  If data already exists in that table, running install.php will wipe it clean.*

Once you're done installing the table, delete the install.php file, like, immediately.

### Step 5: Have a mojito

You're done.

## Usage

Now that you're all uploaded and stuff, you can store and retrieve data from http://www.example.com/sldb/data.php using llHTTPRequest() (or cURL, but if you knew cURL you probably wouldn't need this, so I'll skip that stuff).

The fastest way to use this is by using the example.lsl script included in this package.  This script includes four custom functions you can call to store/retrieve/delete data in various ways.  Just copy the functions into the top of your LSL script (along with a couple of required variables) and you'll be ready to go, or use the example to  make your own adjustments.

However, it's good, on the whole, to know how the system works, so we're including a guide here:  http://aubretec.com/support/manuals/sldbkit

