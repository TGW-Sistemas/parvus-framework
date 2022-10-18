<?php
    namespace Parvus;

    class URLMatcher extends \Symfony\Component\Routing\Matcher\UrlMatcher
    {

        public final function collection ($pathinfo)
        {

            return $this->matchCollection(rawurldecode($pathinfo),$this->routes);

        }

    }