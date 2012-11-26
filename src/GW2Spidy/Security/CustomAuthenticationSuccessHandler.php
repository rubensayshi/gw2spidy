<?php

namespace GW2Spidy\Security;

use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class CustomAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler {
    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $response = parent::onAuthenticationSuccess($request, $token);

        $response->headers->setCookie(new Cookie('logged_in', true));

        return $response;
    }
}