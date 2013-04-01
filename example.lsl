//***********************************************************************
// SLDB: Simple Database Storage for LSL (version 1.0)
//***********************************************************************
// Copyright (C) 2009 aubreTEC Labs
// http://aubretec.com/products/sldb
//
// This program is free software. You can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License.
//**********************************************************************

//**********************************************************************
// ABOUT THIS SCRIPT
// This is a heavily-commented example script showing you how to store, 
// retrieve, and delete data from your SLDB installation. This assumes
// you have already installed SLDB and are ready to begin storing data.
//**********************************************************************

//**********************************************************************
// VARIABLES - These are necessary variables.
//**********************************************************************

// This is the direct URL to your installation (be sure to include the 
// data.php part)
string url = "http://www.example.com/sldb/data.php";  

// This is the secret passphrase defined in your config.php 
string secret = "Luc1s4w3s0m3";

// This is the character you want to use to separate lists.  For most 
// purposes, the pipe ("|") will work; you only need to change this if you 
// have a specific reason to (if you think your stored data might have pipes 
// in it, for instance).
string separator = "|";     

// These are the keys to which you'll assign your HTTPRequests(); the 
// http_response event will use this key to distinguish what kind of
// request it's returning data for.
key put_id;
key get_id;
key del_id;

//**********************************************************************
// FUNCTIONS - There is one custom function for each action (storing
// data, retrieving it, and deleting it).  These functions are designed
// to make these tasks much easier.  You should not change these unless
// you really know what you're doing.
//**********************************************************************

// This function adds or updates the data in your database.  Send it the
// list of fields you want update, followed by a list of values (one for
// each field, in the same order as the fields.  The verbose variable
// determines how detailed your response will be.  For most purposes, 
// FALSE is just fine.

PutData(key id, list fields, list values, integer verbose)
{
    string args;
    args += "?key="+llEscapeURL(id)+"&action=put&separator="+llEscapeURL(separator);
    args += "&fields="+llEscapeURL(llDumpList2String(fields,separator));
    args += "&values="+llEscapeURL(llDumpList2String(values,separator));
    args += "&secret="+llEscapeURL(secret);
    put_id = llHTTPRequest(url+args,[HTTP_METHOD,"GET",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],"");
}

// This function retrieves the data from the database.  Send it the list
// of fields you want to retrieve. If verbose = TRUE, you will get back
// a string that looks like this: field1|value1|field2|value2. If
// verbose = FALSE, you'll get back just the values: value1|value2|value3
// To retrieve ALL of a user's data, use the list ["ALL_DATA"] in the fields
// variable (though you should DEFINITELY use verbose = TRUE for this).

GetData(key id, list fields, integer verbose)
{
    string args;
    args += "?key="+llEscapeURL(id)+"&action=get&separator="+llEscapeURL(separator);
    args += "&fields="+llEscapeURL(llDumpList2String(fields,separator))+"&verbose="+(string)verbose;
    args += "&secret="+llEscapeURL(secret);
    get_id = llHTTPRequest(url+args,[HTTP_METHOD,"GET",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],"");
}

// This function deletes the data from the database.  Send it the list
// of fields you want to delete. The verbose variable determines how 
// detailed your response will be.  For most purposes, FALSE is just fine.
// To delete ALL of a user's data, use the list ["ALL_DATA"] in the fields
// variable.

DelData(key id, list fields, integer verbose)
{
    string args;
    args += "?key="+llEscapeURL(id)+"&action=del&separator="+llEscapeURL(separator);
    args += "&fields="+llEscapeURL(llDumpList2String(fields,separator))+"&verbose="+(string)verbose;
    args += "&secret="+llEscapeURL(secret);
    del_id = llHTTPRequest(url+args,[HTTP_METHOD,"GET",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],"");
}
    

default
{
    state_entry()
    {
        //In this example, we're storing the owner's name and avatar size when the script first runs.
        PutData(llGetOwner(),["name","size"],[llKey2Name(llGetOwner()),llGetAgentSize(llGetOwner())],FALSE);
    }

    touch_start(integer total_number)
    {
        // In this example, we're fetching the owner's name and avatar size (stored above) on touch.
        GetData(llGetOwner(),["name","size"],TRUE);
    }
    
    changed(integer change)
    {
        // In this example, we're deleting all of the owner's data if the box gets colored red.  I can
        // think of no reason why you'd want to do this, but I needed SOMETHING to trigger it so I could
        // give an example.
        if((change & CHANGED_COLOR) && (llGetColor(ALL_SIDES) == <1,0,0>))
        {
            DelData(llGetOwner(),["ALL_DATA"],TRUE);
        }
    }
    
    http_response(key id, integer status, list metadata, string body)
    {
        // In this example, we're simply spitting back the data we've gotten from the server as an llOwnerSay.
        
        // First, make sure this request is one of the ones used in this script (as opposed to one being called
        // by another script in the same object).
        
        if((id != put_id) && (id != get_id) && (id != del_id)) return;
        
        // If the status isn't 200, then there was a problem connecting to your server.  Maybe the URL isn't
        // correct, or the server is offline.  Set the body to the server error so the final result is an
        // accurate account of what happened.
        if(status != 200) body = "ERROR: CANNOT CONNECT TO SERVER";
        
        // And spit out the information we got.
        llOwnerSay(body);
    }
}
