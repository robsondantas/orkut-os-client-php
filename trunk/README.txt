README
=======

What is this?
----------------
It´s a opensource project which allows you to interact with Orkut (e.g send remote commands) without having to develop an opensocial application. 
This is done by the same way Facebook and Twitter does. It is designed for being easy to use and extend, with some real examples working. 
See more details below.

IF YOU HAVE PAID for this, ask your money back.

Why?
-----
Lots of people were asking on our list (opensocial-orkut) to alternatives (php, python, etc) to 3-legged OAuth Java lib developed by Orkut team. 
As a result of studying the source code, figured out that porting to another language was going to be pretty straightforward, since it's basically a RPC wrapper. 
Opensocial-php-client-library was used as a benchmark to build up this library, since it has a builtin 3legged oauth structure. 

Thanks to Chris Chabot and guys from Google for delivering such a great project.

My work here was packing up everything needed to run 3legged oauth, doing some reverse engineering on Orkut OS Client and FotoScrapr .

Live sample
-------------
Uploaded a test on my website, see: http://www.dxs.com.br/os-3leg/test/

Features available
-------------------
* Read friends and profile data
* Read and write scraps
* Read and post activities


Basic knowledge
----------------
* Google authentication screen "cannot" be removed, since it's part of 3legged OAuth process. 
  Quoted the word "cannot", because technically you can do a workaround using curl to simulate the process. 
  UPDATE: released a small script which allows you to do that, read more here and use this with caution.

* If you are looking for a deep understanding about OAuth mechanism, read this explanation, and also check Google OAuth Playground.

How to use it
--------------
* Download the source code using SVN. It can be done using your favorite SVN client/GUI. 
  Just point to http://orkut-os-client-php.googlecode.com/svn/trunk/ .
* Setup an Apache + PHP 5+ environment with Curl support. Will not cover the steps required to setup it.
* Get OAuth keys for your app on http://code.google.com/intl/pt-BR/apis/accounts/docs/RegistrationForWebAppsAuto.html#new . 
  You will get two things: OAuth consumer key (usually your domain url) and OAuth consumer secret (a hash).
* Edit globals.php, modifying everything you need, which is basically:
  g_base_dir - path where your app is stored (physically);
  g_timezone - your timezone;
  g_orkut_consumer_key and g_orkut_consumer_secret which you got on the step described above;
  g_captcha_handler - virtual path for captcha.php
* Once you finish those steps, point to http://<<server>>/<<lib>>/test/ . It has a really basic test screen, with some json calls for getting friends and sending scraps. 
Try sending a scrap with an url inside to test captcha.

Want to help or having doubts ?
--------------------------------
Join our discussion list: http://groups.google.com/group/orkut-php-client

Who is using ? 
---------------

(let me know if you are using this lib in order to update project page)

Here is a list of countries making use of this library:

Brazil
India
United States
Ukraine
Argentina
China
Germany
Israel
Colombia
Australia
Pakistan
Vietnam
France
Portugal
United Kingdom
Japan
Canada
Spain
Mexico
Belgium
South Korea
Switzerland
Turkey
Finland
Nepal
Chile
Russia
Philippines
New Zealand
Armenia
Indonesia
Italy


Enjoy!

Robson Dantas
biu.dantas@gmail.com
Twitter: @robsondantas