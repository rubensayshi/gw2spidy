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


Feedback / Help
===============
If you need help or have any feedback, you can contact me on gw2spidy@rubensayshi.com or join me on irc.gamesurge.net #gw2spidy Drakie


Date/time data
==============
As usual I didn't really think about timezones when I started this project, but now that multiple people forked the project and that I'm exporting data to some people it suddenly matters ... 
All data is stored in the server's timezone, however I've made sure that data going out (charts and API) are converted to UTC (and Highcharts converts it to the browsers timezone).

Mailing List
============
Please join the [Google Groups Mailing List](https://groups.google.com/forum/#!forum/gw2spidy) for gw2spidy so that I can keep you up-to-date of any (major) changes / new versions of the Codebase!

Environment setup
=================
The easiest way of getting started is by using Vagrant. This method will provide you with a local virtual machine with a running instance of gw2spidy in a single command. For this to work you will need three things: Virtualbox, Ruby, and the Vagrant gem. Once you have this, simply `cd` into the gw2spidy directory and run `vagrant up`. This will fetch the base virtual machine for developing (a Ubuntu Precise 64bit server), install all of the required packages, configure mysql and nginx, then forward the virtual machine's port 80 to your machine's port 8080. When it's finished, visit `localhost:8080` in a browser and you're ready to go. Note that this does not do the crawling required to populate the database.

Alternatively, follow the steps below:

I'll provide you with some short setup instructions to make your life easier if you want to run the code for yourself or contribute.
There's also a INSTALL file which contains a snippet I copy paste when I setup my VM, it should suffice ;-)

#### A LOT has changed and most likely will continue a while longer
Join the IRC channel and we can talk!  
Me (Drakie) and other people already involved for a while are happy to share our knowledge and help you, specially if you consider contributing!

Linux
-----
I run the project on a linux server and many of the requirements might not be available on windows and I have only (a tiny bit) of (negative) experience with windows.  
If you want to run this on a windows machine, for development purposes, then I strongly suggest you just run a virtual machine with linux (vmware player is free and works pretty nice).  
If you make your way to the IRC channel I have a VM image on my google drive (made by Marthisdil) with everything setup and ready to roll ;)

PHP 5.3
-------
You'll need PHP5.3 or higher for the namespace support etc.  
You'll need the following extensions installed:  
 * php5-curl
 * php5-mysql
 * php5-memcache 

MySQL / Propel
--------------
I think 4.x will suffice, though I run 5.x.  
On the PHP side of things I'm using PropelORM, thanks to that you could probably switch to PostgreSQL or MSSQL easily if you have to ;) 

Apache / Nginx / CLI
--------------------
The project will work fine with both Apache or Nginx (I actually run apache on my dev machine and nginx in production), you can find example configs in the `docs` folder of this project.  
If you want to run the code that spiders through the trade market then you'll need command line access, if you just want to run the frontend code (and get a database dump from me) then you can live without ;)

On a clean install you might need to enable apache rewrite with the command: `a2enmod rewrite` 

Memcache
--------
Using memcache daemon and PHP Memcache lib to easily cache some stuff in memory (item and type data).  
However, everything will work fine without memcache, if you have memcache installed but don't want the project to use it then define MEMCACHED_DISABLED in your config.inc.php and set it to true.  
You DO need the php5-memcache library, but it won't use memcache for anything ;)

**Note** that you need `php5-memcache` not `php5-memcached`  
**Note** that you need to have the memcache extension, even if you don't want to use it!  

Redis
-----
The spidering code uses a custom brew queue and some custom brew system to make sure we don't do more then x amount of requests.  
Both the queue and the slots are build using Redis (Predis library is already included in the `vendor` folder).  
Previously I was using MySQL for this, but using MySQL was a lot heavier on load and using Redis it's also slightly faster!  

**You need to install redis-server and it needs to be version 2.2 or higher**  
If you're using debian you need to build from source, ubuntu has an updated package with apt-get ;-)

Silex / Twig / Predis
---------------------
Just some PHP libs, already included in the `vendor` folder.

jQuery / Highcharts / Twitter Bootstrap
---------------------------------------
Just some HTML / JS / CSS libs, already included in `webroot/assets/vendor` folder.

You will need the pear library Log
----------------------------------
    pear channel-discover pear.phing.info
    pear install phing/phing
    pear install Log


Project Setup
=============

RequestSlots
------------
ArenaNet is okay with me doing this, but nonetheless I want to limit the amount of requests I'm shooting at their website or at least spread them out a bit.
I came up with this concept of 'request slots', I setup an x amount of slots, claim one when I do a request and then give it a cooldown before I can use it again.
That way I can control the flood a bit better, from the technical side this is done using Redis sorted sets.

Background Queues
-----------------
All crawling work is done in background process / daemons and the heavy lifting is done with a few queues, the queues process also handles the previously mentioned request slots.
This is also done using Redis sorted sets.

Config / Env
------------
Think of a name that represents your machine / evn, eg *ruben-vm1* or *gw2spidy-prod-vps2*.  
Copy the `config/cnf/example-custom-cnf.json` to `config/cnf/<your-chosen-name>.json` an edit it to set the values for *auth_email* and *auth_password*.

Copy `config/cnf/example-env` to `config/cnf/env` and edit it, it contains a line for each config file it should load from `config/cnf/<name>.json`  
Replace the first line (*ruben-vm1*) with the name you had previously chosen, leave the *dev* and *default*, those are other config files it should load too (or change *dev* to *prod* if you don't want debug mode.

The config files you specify `config/cnf/env` will be loaded (in reverse order), overwriting the previous ones.
For overloading other config values (like database login etc.), check `config/cnf/default.json` for all options which you could also se in your custom config file.

### The `config/cnf/env` and any `config/cnf/*.json` other then `default.json`, `dev.json` and `prod.json` are on .gitignore so they won't be versioned controlled 

Database Setup
--------------
In the `config` folder there's a `config/schema.sql` (generated by propel based of `config/schema.xml`, so database changes should be made to the XML and then generating the SQL file!).  
You should create a database called 'gw2spidy' and load the `config/schema.sql` in.

RequestSlots Setup
------------------
Run `tools/setup-request-slots.php` to create the initial request slots, you can also run this during development to reinitiate the slots so you can instantly use them again if they are all on cooldown.

Building The Item Database
--------------------------
The first time you'll have to run `daemons/worker-types.php` to fetch all the item (sub)types (after that I run it nightly, just to ensure there aren't any changes, none in the past month).  
After that is done, run `daemons/fill-queue-item-db.php`, this will enqueue a job for each (sub)type to start fetch item information.  
Then run `daemons/worker-queue-item-db.php`, you'll have to do it a few times since it fetches 1 page (10 items) per queue-item, about ~22 times.

Ater this you should have a full item database, without any listings yet.

Crawling The Tradingpost
------------------------
The crawling can be done in 3 ways and I'm gonna explain them a bit before continueing your journey how to use GW2Spidy ;)

### listings.json
A request to **/ws/listings.json?id=<item-id>** gives back all the buy and sell listings for a single item.  
Atm I grab the lowest and don't even store the other except summing up their total quantity, this is because I'm not using the other listings and the database is getting too big to just carelessly store them.  

This method is always the most accurate and guaranteed to work because it's what the game relies on heavily.  

The disadvantage of this method is that we have to do 1 request for every item to update their price, with around 20k items and ArenaNet who prefers if we could stay near 5 requests / second we can't do more frequent updates then 1 per hour this way.  
I created a priority system (read below) to update more interesting items more often then the less interesting items to work with this.  
Another problem with this method is that we need a session_key from the game client, read below for more information GW2 Sessions.

### search.json
A request to **/ws/search.json?type=<type-id>&page=<page>** is what we also use to build up the item database and gives us data for 10 items in 1 request.  
However ArenaNet had a lot of bugs which made the prices unreliable (ingame too), so I started using listings.json a few weeks ago!  
They however fixed the bugs in the past patch for this method and we can use it again to build listings data on too.  

### search.json?ids=
There's another way to use search.json, namely a request to **/ws/search.json?ids=<csv-ids-max-250>** which allows us to get up to 250 items in 1 request!  
This was (even more) buggy too and ArenaNet hasn't fixed it propely yet, mostly because they themselves only use them when you click on an item on the homepage of the TP (click the Unidentified Dye ingame, you'll notice the price is wrong).  

I got a tip from *shroud* how to get around this bug, but he didn't want me to share it with anyone because he feels it might allow a lot of other people to use this to play the market a bit too well.  
I respect that and am really happy he at least shared it with me for gw2spidy, because 250 items in request is superb to the other 2 methods, by a large margin! 

### Choices To Make
So for the real gw2spidy database I want to use the *search.json?ids=* method and I build the code so that I can use it and still have a fallback to *listings.json* in case ArenaNet messes up again and I know *listings.json* will always be as accurate as possible.  
However since ArenaNet only fixed the normal *search.json* without the *shroud-magic* the *search.json?ids=* method ain't really viable for other people to use, unless you like weird inaccurate spikes in your data once in a while.  

Before all this madness, I always used the normal *search.json*, I suggest others should do that too atm until I can either release *shrouds* magic or ArenaNet fixes it themselves.  
Or use *listings.json* but you'll have a lot lower frequency!

### How To Configure it
The default config will use the *listings.json* method if you use the listingsDB worker, to match how it was working before I reimplemented all this, you can instead disabled 'use_listings-json' in the config to use *search.json?ids=* if you want too.  
However, the best way atm to go is with the 'save_listing_from_item_data' enabled (default enabled) and only use the itemDB worker!

ItemDB Worker
-------------
The ItemDB Worker itself is this script: `daemons/worker-queue-item-db.php`.  
It processes items from the 'item-db-queue' queue and that queue is filled by `daemons/fill-queue-item-db.php`.  

When you want to use the *search.json?ids=* or *listings.json* method this worker and queue are only to find new items, never seen before items on the tradingpost and in that case should just run the fill-queue script nightly and have 1 worker running to process that queue.  

When you want to use the normal *search.json* method (you should atm) we'll (ab)use this worker and you should run the fill-queue script at a frequency at which you want your listing data to be at (15min ~ 30min is a fair frequency).  
And you should have a couple (2~4) worker-queue scripts running to processes and keep up with your desired frequency, the queue should generally be empty before it's (re)filled.  

ItemListingDB Worker
--------------------
The ItemListingDB Worker itself is this script: `daemons/worker-queue-item-listing-db.php`.  
It will pop items off the listing queue and process them, these queue-items are automatically requeue'd with their priority so you should only haveto run `daemons/fill-queue-item-listing-db.php` once to get the initial batch in.  
When 'use_listings-json' is enabled and there's a game session_key (see the GW2Session section below) it will just process 1 item at a time and use *listings.json* method to retrieve it.  
When it's not enabled it will use *search.json?ids=* and process the configured 'items-per-request' amount of items at 1 time (max 250!).  

However if the script fails we might sometimes loose a queue-item or new items might be added to the database at some point so there's a `daemons/supervise-queue-item-listing-db.php` script which makes sure that the queue is still filled properly.

There's a priority system in place so that some items (like weapons above certain rarity / level) are processed more often then others (like salvage kits, which nobody buys from the TP ...).  
See the Priority System section below for more info on that!


Gem Worker
----------
The `daemons/worker-gem.php` script does 2 requests to the gem-exchange site to retrieve the exchange rates and volume and then sleeps for 180 seconds (3 minutes).
This script also requires a game session_key (see GW2Session section again).

Running The Workers
-------------------
The workers all do 100 loops to do their specific task or if no tasks they do short sleeps waiting for a task.  
They will also sleep if there are no slots available.

Previously I used to run 4 workers in parallel using `while [ true ]; do php daemons/worker-queue-item-listing-db.php >> /var/log/gw2spidy/worker.1.log; echo "restart"; done;`  
Where I replace the .1 with which number of the 4 it is so I got 4 logs to tail.

I now added some bash scripts in the `bin` folder to `bin/start-workers.sh <num-listing-workers> <num-item-workers> <num-gem-workers>` and `bin/stop-workers.sh <now>` to manage them.  
You should check the bash scripts and understand them before running them imo ;) but you could also trust me on my blue eyes and just run it xD

Priority System
---------------
Our amount of request we do are limited by our requestslot system, unfortunatly we're now bound by doing 1 item per request (previously we could combine up to 250).  
So I created a priority system to process 'important' items more often, in the this spreadsheet I calculated the priorities:  
https://docs.google.com/a/rubensayshi.com/spreadsheet/ccc?key=0Alq65aekWXJmdGotSmdBYXJPZ0NKbHBhdzVZMlh5Q1E#gid=0

**this has been changed slightly, I need to update the spreadsheet and should write some stuff here soon**

GW2 Sessions
============
When spidering we used to access the tradingpost using a session created by logging into accounts.guildwars2.com.  
Aftering logging in it gives us a session_key which allows access to the tradingpost, however limited to only being able to get the lists of items!  

When you open the tradingpost from inside the game you access it using a session_key generated from the game login, these sessions have access to more features of the tradingpost!  
With that session you can also see the list of offers for the various prices, instead of only the lowest sell and highest buy!  
For the gem-exchange the ingame session_key allows you to calculate conversions, while the accounts.guildwars2.com session only gives you a rounded average (which is kinda useless).  

As of late ArenaNet has messed up the item lists (search results and such) to be very unaccurate (due to caching), you can also see this ingame.  
I also want to collect gem-exchange data ...  
So I needed a way to be able to use an ingame session_key when spidering!  

You can intercept the session_key by either using Fiddle2r to intercept the HTTPS trafic or using some custom tools to grab the URLs from share memory ...  
I've added a table to the database named `gw2session` and a form on `/admin/session` to insert the ingame session_key, it requires you to also fill in a 'secret' which is equal to what you configure in the config or not required on dev envs    

**I don't know exactly what you can do with someone elses session_key** thus I rely on myself not slacking and updating the session_key regularly and will not accept other people giving me their session_key!  
I know for a fact that combined with a charid you can place buy orders and such from outside of the game, so you should be careful with this information ;)  

I do have a small tool (provided by someone else) that quickly grabs the session_key (by seaching for it in shared memory) without much hassle, I won't be sharing it publicly but you could consider joining the IRC channel and asking for it ;)

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
