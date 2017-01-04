<?php
/**
 * Created by PhpStorm.
 * User: keneanung
 * Date: 09/03/15
 * Time: 21:54
 */

class Hybrid_Providers_GuildWars extends Hybrid_Provider_Model_OAuth2 {

    /**
     * @var string default scopes
     */
    public $scope = "account offline";

    /**
     * IDp wrappers initializer
     */
    function initialize()
    {
        parent::initialize();

        // Provider api end-points
        $this->api->authorize_url  = "https://account.guildwars2.com/oauth2/authorization";
        $this->api->token_url      = "https://account.guildwars2.com/oauth2/token";
        $this->api->curl_authenticate_method = "GET";
    }

    function getUserProfile()
    {
        // refresh tokens if needed
        $this->refreshToken();

        // ask api for user info
        $response = $this->api->api("https://api.guildwars2.com/v2/account", "GET", array(), array("Authorization: Bearer {$this->api->access_token}"));

        if (!isset($response->id) || isset($response->error)) {
            throw new Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        }

        $profile = new Hybrid_User_Profile();
        $profile->identifier = $response->id;
        $profile->displayName = $response->name;
        $profile->email = $response->name . "@guildwars.com";
        return $profile;
    }
}