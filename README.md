GW2Spidy - Trade Market Graphs
==============================
This project aims to provide you with graphs of the sale and buy listings of items on the Guild Wars 2 Trade Market.


How does it work?
=================
ArenaNet has build the Trade Market so that it's loaded into the game from a website.  
You can also access this website with a browser and use your game account to login and view all the items and listings.

Now what I've build is some tools which will run constantly to automatically login to that website and record all data we can find,  
as a result I can record the sale listings for all the items about every hour and with that data I can create graphs with the price changing over time! 


Contributing
============
Everyone is very much welcome to contribute, 99% chance you're reading this on github so it shouldn't be to hard to fork and do pull requests right :) ?

If you need any help with setup of the project or using git(hub) then just contact me and I'll be glad to help you!  
If you want a dump of the database, since that's a lot easier to work with, then just contact me ;)


Date/time data
==============
As usual I didn't really think about timezones when I started this project, but now that multiple people forked the project and that I'm exporting data to some people it suddently matters ... 
so I'll refactor the code soon to ensure all date/time data is stored as UTC ... atm all data is stored in the server's timezone, in my case Europe/Amsterdam!


Project setup
=============
I'll provide you with some short setup instructions to make your life easier if you want to run the code for yourself or contribute.
There's also a INSTALL file which contains a snippet I copy paste when I setup my VM, it should suffice ;-)

Environment
-----------
### Linux
I run the project on a linux server and many of the requirements might not be available on windows and I have only (a tiny bit) of (negative) experience with windows.  
If you want to run this on a windows machine, for development purposes, then I strongly sugest you just run a virtual machine with linux (virtualbox is free and works pretty nice).

### PHP 5.3
You'll need PHP5.3 or higher for the namespace support etc.  
You'll need the following extensions installed:  
 * php5-curl
 * php5-mysql
 * php5-memcache 

### MySQL / Propel
I think 4.x will suffice, though I run 5.x.  
On the PHP side of things I'm using PropelORM, thanks to that you could probally switch to PostgreSQL or MSSQL easily if you have to ;) 

### Apache / Nginx / CLI
The project will work fine with both Apache or Nginx (I actually run apache on my dev machine and nginx in production), you can find example configs in the `docs` folder of this project.  
If you want to run the code that spiders through the trade market then you'll need command line access, if you just want to run the frontend code (and get a database dump from me) then you can live without ;)

On a clean install you might need to enable apache rewrite with the command: `a2enmod rewrite` 

### Memcache
Using memcache daemon and PHP Memcache lib to easily cache some stuff in memory (item and type data).  
However, everything will work fine without memcache, if you have memcache installed but don't want the project to use it then define MEMCACHED_DISABLED in your config.inc.php and set it to true.  
You DO need the php5-memcache library, but it won't use memcache for anything ;)

**Note** that you need `php5-memcache` not `php5-memcached`  
**Note** that you need to have the memcache extension, even if you don't want to use it!  

### Redis
The spidering code uses a custom brew queue and some custom brew system to make sure we don't do more then x amount of requests.  
Both the queue and the slots are build using Redis (Predis library is already included in the `vendor` folder).  
Previously I was using MySQL for this, but using MySQL was a lot heavier on load and using Redis it's also slightly faster!  

### Silex / Twig / Predis
Just some PHP libs, already included in the `vendor` folder.

### jQuery / Highcharts / Twitter Bootstrap
Just some HTML / JS / CSS libs, already included in `webroot/assets/vendor` folder.

### You will need a pear library Log
pear channel-discover pear.phing.info
pear install phing/phing
pear install Log

RequestSlots
------------
ArenaNet is okay with me doing this, but nonetheless I want to limit the amount of requests I'm shooting at their website or at least spread them out a bit.  
I came up with this concept of 'request slots', I setup an x amount of slots, claim one when I do a request and then give it a cooldown before I can use it again.  
That way I can control the flood a bit better.

This is done using Redis sorted sets.

WorkerQueue
-----------
All spidering work is done through the worker queue, the queue process also handles the previously mentioned request slots.

This is also done using Redis sorted sets.

Database Setup
--------------
In the `config` folder there's a `config/schema.sql` (generated by propel based of `config/schema.xml`, so database changes should be made to the XML and then generating the SQL file!).  
You should create a database called 'gw2spidy' and load the `config/schema.sql` in.

The `config/runtime-conf.xml` contains the database credentials, be careful that it's not on .gitignore, so don't commit your info!!  
The `config/gw2spidy-conf.php` is generated from that XML, you should manually change the info in there for now, again be careful not to commit it!!  
If you do by excident, backup your code and delete your whole repo from github xD - I'll come up with a better way soon...

Spider Config Setup
-------------------
Copy the `config/config.inc.example.php` to `config/config.inc.php` and change the account info, this file is on .gitignore so you can't commit it by excident ;)

RequestSlots Setup
------------------
Run `tools/setup-request-slots.php` to create the initial request slots, you can also run this during development to reinitiate the slots so you can instantly use them again if they are all on cooldown.

First Spider Run
----------------
The first time you'll have to run `daemons/fill-queue-daily.php` to enqueue a job which will fetch all the item (sub)types.  
Then run `daemons/worker-queue.php` to execute that job.  
After that is done, run  `daemons/fill-queue-listings.php` again, this will enqueue a job for each (sub)type to start fetch item information.  
Then run `daemons/worker-queue.php` again until it's done (needs to fetch about 600~650 pages of items).

The Worker Queue
----------------  
When you run `daemons/worker-queue.php` the script will do 50 loops to either fetch an item from the queue to execute or if none it will sleep.  
It will also sleep if there are no slots available.

Previously I used to run 4 of these in parallel using `while [ true ]; do php daemons/worker-queue.php >> /var/log/gw2spidy/worker.1.log; echo "restart"; done;`  
Where I replace the .1 with which number of the 4 it is so I got 4 logs to tail.

I now added some bash scripts in the `bin` folder to `bin/start-workers.sh 4` and `bin/stop-workers.sh` to manage them.  
You have to `mkdir -p /var/run/gw2spidy/` first though since I manage the processIDs there.

Fill Queue Listings
-------------------
The `daemon/fill-queue-listings.php` atm does the same as the fill queue daily since we can no longer fetch listings directly.  
We just do it more frequent then daily xD

Fill Queue Daily
----------------
The `daemon/fill-queue-daily.php` script enqueues a job for every (sub)type in the database to fetch the first page of items,  
that job then requeues itself until all the pages are fetched.

Fill Queue Gems
---------------
The `daemon/fill-queue-gems.php` script enqueues one job which does 2 requests to the gem-exchange site to retrieve the exchange rates and volume.


GW2 Sessions
============
When spidering we're accessing the tradingpost using a session created by logging into accounts.guildwars2.com.  
Aftering logging in it gives us a session_key which allows access to the tradingpost, however limited to only being able to get the lists of items!  

When you open the tradingpost from inside the game you access it using a session_key generated from the game login, these sessions have access to more features of the tradingpost!  
With that session you can also see the list of offers for the various prices, instead of only the lowest sell and highest buy!  
For the gem-exchange the ingame session_key allows you to calculate conversions, while the accounts.guildwars2.com session only gives you a rounded average (which is kinda useless).  

Because I've added gem-exchange now, I needed a way to be able to use an ingame session_key when spidering!  
You can intercept the session_key by either using Fiddler to intercept the HTTPS trafic or using some custom tools to grab the URLs from share memory ...  
I've added a table to propel named `gw2session` and a form on `/admin/session` to insert the ingame session_key, it requires you to also fill in a 'secret' which is equal to what you configure by defining the 'ADMIN_SECRET' constant  

The previous ingame session_key expires when you login to the game client, so you need to use the new session_key whenever you login.

**I don't know exactly what you can do with someone elses session_key** thus I rely on myself not slacking and updating the session_key everytime I login.  
I think it's fairly harmless, since the game client handles all sell/buy actions (and those are related/linked to your character too).

**As a fallback** whenever there's no ingame session_key available to use (or it has died) we'll generate one from accounts.guildwars2.com, this gives us enough access for the tradingpost, but won't be able to gather gem-exchange data.

I do have a small tool (provided by someone else) that quickly grabs the session_key without much hassle, but it can be optimized a bit and if I were to share it then I'd want to be able to share the source code to to ensure it's safe!

Copyright and License
=====================
Copyright (c) 2012, Ruben de Vries  
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met: 

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer. 
2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution. 

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those
of the authors and should not be interpreted as representing official policies, 
either expressed or implied, of the gw2spidy project.


Copyright and License - Apendix
===============================
The above BSD license will allow you to use this open source project for anything you like.  
However, I would very much appreciate it when you decide to use the code if you could contribute your improvements back to this project
and / or contact me so that I'm aware (and proud) of my project being used by other people too :-)
