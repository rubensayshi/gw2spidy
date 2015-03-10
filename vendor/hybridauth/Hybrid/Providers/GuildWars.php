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
}