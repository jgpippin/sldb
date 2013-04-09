# SLDB: SIMPLE MYSQL STORAGE FOR LSL (version 1.1)

## Overview
This set of scripts is designed to run on your own LAMP (Linux, Apache, MySQL, PHP) web server, in order to allow you to easily store and retrieve information from Second Life using the llHTTPRequest() function.  It is designed to allow you to use an off-world MySQL database as persistent storage for Second Life scripts.

## Requirements
You will need your own PHP hosting account with at least one MySQL database. It is preferable that mod_rewrite be enabled, but not necessary.  Most hosts fit this bill; I use [A Small Orange](http://asmallorange.com) or [Site5](http://site5.com).

## Installation

### Step 1: Create a MySQL database and user

You'll need to create a MySQL database and a user account to manage that database.  If you're on a cPanel account, this is best accomplished by clicking the "MySQL Database Wizard" icon. Otherwise, follow your hosting service's instructions.

You should give your user full privileges to the database you create (at the very least SELECT, INSERT, UPDATE, and DELETE). Then make a note of the database name, the user name, and the password. 

Let's assume your database details are as follows:

            Database Name: dimwit_sldb
            Database User: dimwit_sldb
            Database Pass: sekret

Okay, you're ready.  Let's move on to...

### Step 2: Edit the config.php file

Unzip the files in the package and open "config.php" in your favorite plain text editor.  For our example:     

            $db_host = 'localhost';  // Localhost is usually fine.
            $db_user = 'dimwit_sldb';
            $db_pass = 'sekret';
            $db_name = 'dimwit_sldb';  // This is the database name.
            $db_table = 'data';  // The default value here is usually fine.

### Step 3: Upload the files

Upload the sldb directory to your webserver.  Note the location of this.  For the purposes of this guide, we'll assume it's this:
                
                    http://www.example.com/sldb/

You don't have to call the folder sldb, but remember the name of it. 

Typically speaking, this folder should go in the /www/ or /public_html/ folder (different for some hosts).

### Step 4: Install the table

Visit http://www.example.com/sldb/install to install the table on the database. You should get a confirmation.

## Usage

Now that you're all uploaded and stuff, you can store and retrieve data from http://www.example.com/sldb/data.php using llHTTPRequest(). An example script, example.lsl, is included to walk you through using external HTTP request calls to store and read data.

Each record stored has three variables:

* uuid -   The key variable is generally designed to store data by SL user keys or uuids. 
* field - This is an identifier for the data you're storing.
* value - And this is the actual information you want to store.
    
For instance, if you were using this to store a user's preferences for prim shoes, you might want to store the following fields: color, size, and laces. Assuming the user has selected an unlaced, size 8, red shoe, you would store these as three separate records:

     5ff653f6-1044-479c-8482-9049c7b8b79f, color, <1,0,0>
     5ff653f6-1044-479c-8482-9049c7b8b79f, size, 8
     5ff653f6-1044-479c-8482-9049c7b8b79f, laced, no

This is about as flexible a storage solution as I could come up with without getting into your individual needs.  If you need something more flexible, you should write your own script.

