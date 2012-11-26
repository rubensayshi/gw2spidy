<?php

/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//    HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return
    array(
        "base_url" => getAppConfig("hybrid_auth.callback_url"),

        "providers" => array (
            // openid providers
            "OpenID" => array (
                "enabled" => false
            ),

            "AOL"  => array (
                "enabled" => false
            ),

            "Yahoo" => array (
                "enabled" => false,
                "keys"    => array ( "id" => "", "secret" => "" )
            ),

            "Google" => array (
                "enabled" => true,
                "keys"    => array ( "id" => getAppConfig("hybrid_auth.google_id"), "secret" => getAppConfig("hybrid_auth.google_secret")),
                "scope"           => implode(" ", array("https://www.googleapis.com/auth/userinfo.profile",
                                                        "https://www.googleapis.com/auth/userinfo.email")),
                "access_type"     => "online",
            ),

            "Facebook" => array (
                "enabled" => true,
                "keys"    => array ( "id" => getAppConfig("hybrid_auth.facebook_id"), "secret" => getAppConfig("hybrid_auth.facebook_secret")),
                "scope"   => "email, user_about_me, user_birthday",
            ),

            "Twitter" => array (
                "enabled" => true,
                "keys"    => array ( "key" => getAppConfig("hybrid_auth.twitter_key"), "secret" => getAppConfig("hybrid_auth.twitter_secret"))
            ),

            // windows live
            "Live" => array (
                "enabled" => false,
                "keys"    => array ( "id" => "", "secret" => "" )
            ),

            "MySpace" => array (
                "enabled" => false,
                "keys"    => array ( "key" => "", "secret" => "" )
            ),

            "LinkedIn" => array (
                "enabled" => false,
                "keys"    => array ( "key" => "", "secret" => "" )
            ),

            "Foursquare" => array (
                "enabled" => false,
                "keys"    => array ( "id" => "", "secret" => "" )
            ),
        ),

        // if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
        "debug_mode" => false,

        "debug_file" => "/tmp/hauth.log"
    );
