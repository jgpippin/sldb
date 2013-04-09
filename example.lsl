// *****************************************************************************
// SLDB: SIMPLE DATABASE STORAGE FOR LSL
//
// Copyright (C) 2009 aubreTEC Labs
// http://aubretec.com/products/sldb
//
// This program is free software. You can redistribute it and/or modify it under
// the terms of the GNU General Public License as published by the Free Software
// Foundation; either version 2 of the License.
//
// ABOUT THIS SCRIPT
// This is a heavily-commented example script showing you how to store,
// retrieve, and delete data from your SLDB installation. This assumes you have
// already installed SLDB and are ready to begin storing data.
// *****************************************************************************


// *****************************************************************************
// PlACEHOLDER VARIABLES - There's no need to change these.
// *****************************************************************************

// These are the keys to which you'll assign your HTTPRequests(); the
// http_response event will use this key to distinguish what kind of request
// it's returning data for.
key update_id;
key read_id;
key delete_id;


// *****************************************************************************
// SETTINGS VARIABLES - Edit these for your own installation.
// *****************************************************************************

// This is the direct URL to your installation
string url = "http://www.example.com/sldb";

// This is the secret passphrase defined in your config.php
string secret = "Luc1s4w3s0m3";

// Select two characters to separate lists. This is only used for the readData
// function, but is helpful in parsing list strings. Select two separators,
// because if verbose is set to TRUE you will need two levels of separation. For
// instance, if the separators are ["&", "="] your (verbose) results will look
// like this: "field1=value1&field2=value2."
//
// And your non-verbose results will look like this: "value1&value2"
//
// For this example, we've set both separators to pipes, so your verbose
// response will look like this: "field1|value1|field2|value2" (easy to split
// with strided lists) and your non-verbose response will look like this:
// "value1|value2|etc."
list separators = ["|", "|"];


// *****************************************************************************
// FUNCTIONS - There is one custom function for each action (updating data,
// reading it, and deleting it).  These functions are designed to make these
// tasks much easier.  You should not change these unless you really know what
// you're doing.
// *****************************************************************************

// Add or update data in the database
//
// @param key uuid
//   The user's uuid. This can be any unique string, but is meant to be a UUID.
//
// @return
//   Return value
updateData(key uuid, list fields, list values, integer verbose) {
    string args = "secret=" + llEscapeURL(secret);
    integer i;
    for (i = 0; i < llGetListLength(fields); i++) {
        args += "&fields[" + llEscapeURL(llList2String(fields, i)) + "]=" + llEscapeURL(llList2String(values, i));
    }
    update_id = llHTTPRequest(url + "update/" + (string)uuid + "?" + args,[HTTP_METHOD,"GET",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],"");
}

// Read data from the database.
//
// @param key uuid
//   The user's uuid. This can be any unique string, but is meant to be a UUID.
// @param list fields
//   The list of fields to retrieve. The results will be given back in the same
//   order as requested.
// @param int verbose
//   TRUE for a verbose return ('field_name=field_value'), FALSE for just a list
//   of values.
readData(key uuid, list fields, integer verbose) {
    string args = "secret=" + llEscapeURL(secret);
    integer i;
    for (i = 0; i < llGetListLength(fields); i++) {
        args += "&fields[]=" + llEscapeURL(llList2String(fields, i));
    }
    args += "&separators[]=" . llList2String(separators, 0) . "&separators[]=" . llList2String(separators, 0);
    read_id = llHTTPRequest(url + "read/" + (string)uuid + "?" + args,[HTTP_METHOD,"GET",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],"");
}

// Delete data from the database.
//
// @param key uuid
//   The user's uuid. This can be any unique string, but is meant to be a UUID.
// @param list fields
//   The list of fields to retrieve. The results will be given back in the same
//   order as requested.
// @param int verbose
//   TRUE for a verbose return, otherwise FALSE.
deleteData(key uuid, list fields, integer verbose) {
    string args;
    args += "?key="+llEscapeURL(id)+"&action=del&separator="+llEscapeURL(separator);
    args += "&fields="+llEscapeURL(llDumpList2String(fields,separator))+"&verbose="+(string)verbose;
    args += "&secret="+llEscapeURL(secret);
    delete_id = llHTTPRequest(url+args,[HTTP_METHOD,"GET",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],"");
}


default
{
    state_entry()
    {
        //In this example, we're storing the owner's name and avatar size when
        //the script first runs.
        updateData(llGetOwner(),["name","size"],[llKey2Name(llGetOwner()),llGetAgentSize(llGetOwner())],FALSE);
    }

    touch_start(integer total_number)
    {
        // In this example, we're fetching the owner's name and avatar size
        // (stored above) on touch.
        readData(llGetOwner(),["name","size"],TRUE);
    }

    changed(integer change)
    {
        // In this example, we're deleting all of the owner's data if the box
        // gets colored red.  I can think of no reason why you'd want to do
        // this, but I needed SOMETHING to trigger it so I could give an
        // example.
        if((change & CHANGED_COLOR) && (llGetColor(ALL_SIDES) == <1,0,0>))
        {
            deleteData(llGetOwner(),["ALL_DATA"],TRUE);
        }
    }

    http_response(key id, integer status, list metadata, string body)
    {
        // In this example, we're simply spitting back the data we've gotten
        // from the server as an llOwnerSay.

        // First, make sure this request is one of the ones used in this script
        // (as opposed to one being called by another script in the same
        // object).
        if((id != update_id) && (id != read_id) && (id != delete_id)) return;

        // If the status isn't 200, then there was a problem connecting to your
        // server.  Maybe the URL isn't correct, or the server is offline.  Set
        // the body to the server error so the final result is an accurate
        // account of what happened.
        if(status != 200) body = "ERROR: CANNOT CONNECT TO SERVER";

        // And spit out the information we got.
        llOwnerSay(body);
    }
}
