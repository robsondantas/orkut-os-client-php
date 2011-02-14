README
======

What is this ?
--------------
A small project created on top of orkut-os-client-php, which will allow you to send scraps for multiple users at same time.

Why ?
------
Opensource is about philosophy, state of mind. Just figured out guys selling this library out there. This pisses me off.
So, in order to block the *illegal* activities decided to release this simple script which does the trick.

Usage:
------
As always, just download the library, configure globals.php. 
You will also need to edit send_scrap.php if you want to change directory structure. You need to required auth.php and scrap.php .

After this, just point your browser to index.php and start playing. 
If you need to hack somewhere, consider looking into orkut.js - which does almost everything. 

Important info:
----------------
Orkut developed several ACLs to block bad users from abusing and sending tons of scraps. Have in mind that Orkut will issue a captcha
challenge whenever they want, and you can´t control or avoid that.

Testing a bit, around 200 scraps per time is a good threshold. 

Take care sending multiples messages to the same user. Orkut may block your key for a few hours - happened with me several times when
testing.

More info ?
------------
http://code.google.com/p/orkut-os-client.php is a great start. There are useful informations over there. If you get stuck, join our group.

Robson Dantas
@robsondantas

