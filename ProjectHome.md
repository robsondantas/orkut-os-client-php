# What is this? #

Orkut client for PHP is a library which allows you to interact with Orkut (e.g send remote commands) without having to develop an opensocial application. This is done by the same way Facebook and Twitter does. It is designed for being easy to use and extend, with some real examples working. See more details below.

UPDATES:

  * Scrapall released under samples. Allows you to send scraps to everybody on your list, dealing with captcha, using Javascript.
  * New samples section. Subprojects will be added to give more real-working code.
  * Code improvements, structure modifications

.

# Donations #

This library is a result of uncountable hours of reverse engineering. If you like this, considering donating. Any amount is appreciated.

<a href='https://www.paypal.com/cgi-bin/webscr?hosted_button_id=YK4GPJ7BDL8NL&cmd=_s-xclick'><img src='https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif' border='0' /></a>

.

# Why? #

Lots of people were asking on our list ([opensocial-orkut](http://groups.google.com/group/opensocial-orkut)) to alternatives (php, python, etc) to 3-legged OAuth Java lib developed by Orkut team. As a result of studying the source code, figured out that porting to another language was going to be pretty straightforward, since it's basically a RPC wrapper. [Opensocial-php-client-library](http://code.google.com/p/opensocial-php-client/) was used as a benchmark to build up this library, since it has a builtin 3legged oauth structure. Thanks to Chris Chabot and guys from Google for delivering such a great project.

My work here was packing up everything needed to run 3legged oauth, doing some reverse engineering on [Orkut OS Client](http://code.google.com/p/orkut-os-client) and [FotoScrapr](http://code.google.com/p/orkut-greetings) .

# Live sample #
  * Uploaded a test on my website, see: [click here](http://www.dxs.com.br/os-3leg/test/)

# Features available #
  * Read friends and profile data
  * Read and write scraps
  * Read and post activities


# Basic knowledge #
  * Google authentication screen "cannot" be removed, since it's part of 3legged OAuth process. Quoted the word "cannot", because technically you can do a workaround using curl to simulate the process. UPDATE: released a small script which allows you to do that, read more [here](https://groups.google.com/d/topic/orkut-php-client/BxTQ9xHGHQI/discussion) and use this with caution.

  * If you are looking for a deep understanding about OAuth mechanism, [read this explanation](http://code.google.com/intl/pt-BR/apis/gdata/articles/oauth.html), and also check [Google OAuth Playground](http://googlecodesamples.com/oauth_playground/).

# How to use it #

  * Download the source code using SVN. It can be done using your favorite SVN client/GUI. Just point to http://orkut-os-client-php.googlecode.com/svn/trunk/ .

  * Setup an Apache + PHP 5+ environment with Curl support. Will not cover the steps required to setup it.

  * Get OAuth keys for your app on http://code.google.com/intl/pt-BR/apis/accounts/docs/RegistrationForWebAppsAuto.html#new . You will get two things: OAuth consumer key (usually your domain url) and OAuth consumer secret (a hash).

  * Edit globals.php, modifying everything you need, which is basically:
    * g\_base\_dir - path where your app is stored (physically);
    * g\_timezone - your timezone;
    * g\_orkut\_consumer\_key and g\_orkut\_consumer\_secret which you got on the step described above;
    * g\_captcha\_handler - virtual path for captcha.php

  * Once you finish those steps, point to `http://<<server>>/<<lib>>/test/` . It has a really basic test screen, with some json calls for getting friends and sending scraps. Try sending a scrap with an url inside to test captcha.

# Want to help or having doubts ? #

  * Join our discussion list: [Orkut php client](http://groups.google.com/group/orkut-php-client)
  * This is a first release and many things must be done. Read our roadmap and known issues to get a deep understand.

# Who is using ? #

Here is a list of countries making use of this library:

  * Brazil
  * India
  * United States
  * Ukraine
  * Argentina
  * China
  * Germany
  * Israel
  * Colombia
  * Australia
  * Pakistan
  * Vietnam
  * France
  * Portugal
  * United Kingdom
  * Japan
  * Canada
  * Spain
  * Mexico
  * Belgium
  * South Korea
  * Switzerland
  * Turkey
  * Finland
  * Nepal
  * Chile
  * Russia
  * Philippines
  * New Zealand
  * Armenia
  * Indonesia
  * Italy

Enjoy!

Robson Dantas

Twitter: [@robsondantas](http://www.twitter.com/robsondantas)