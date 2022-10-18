<?php

    namespace Parvus;

    class Language
    {

        private static $aLanguage = array();
        private static $locale = NULL;
        private static $config = array();

        /**
         * Return the text of a key
         * @param $prText (Ex: messages.error.login, this will be a array position in the language translation file)
         * @param null $prArray (Ex: array ('name' => 'John', 'email' => 'email@email.com')
         * @return array|mixed
         */
        public final static function get ($prText,$prArray = NULL)
        {

            $locale = static::getLocale();
            $aArray = static::$aLanguage;

            /** If the language array is null */
            if (static::$aLanguage == NULL)
            {

                static::getConfig();

                if (static::$config['database'] != NULL)
                {

                    $modelName = '\\Model\\'.static::$config['database']['model'];

                    $model = new $modelName();

                    foreach ($model->where(static::$config['database']['field']['language'],$locale)->first()->{static::$config['database']['function']} as $item)
                    {

                        $aArray[$item->{static::$config['database']['field']['id']}] = $item->{static::$config['database']['field']['label']};

                    }

                }
                else
                {

                    $file = path.'app/language/'.$locale.'.php';

                    /** If the locale file not exists */
                    if (!file_exists($file))
                    {

                        throw new \RuntimeException('Locale translation file not found.',E_ERROR);

                    }

                    /** Get the content */
                    $aArray = include_once ($file);

                }

                /** Save the content to array of the class */
                static::$aLanguage = $aArray;
            }

            if (static::$config['database'] != NULL)
            {

                $aArray = $aArray[$prText];

            }
            else
            {

                /** Get the text position */
                foreach (explode('.',$prText) as $label)
                {

                    $aArray = $aArray[$label];

                }

            }

            /**
             * If the text has not found, return the original text
             */
            if ($aArray == NULL || !is_string($aArray))
            {

                return $prText;

            }

            /** Replace the macros strings by value */
            foreach ($prArray as $label => $value)
            {

                if ($value == NULL || strlen($value) == 0)
                {

                    continue;

                }

                foreach (array(mb_strtolower($label),mb_strtoupper($label),$label) as $tmp)
                {

                    $aArray = str_replace('{'.$tmp.'}',$value,$aArray);

                }

            }

            /** Return the text */
            return $aArray;

        }

        /**
         * Define the locale
         * @param $prLocale
         */
        public final static function setLocale ($prLocale)
        {

            $_SESSION['parvus-locale'] = $prLocale;

        }

        /**
         * Return the actual locale
         * @return null
         */
        public final static function getLocale ()
        {

            /** If not has defined the locale */
            if (static::$locale == NULL && $_SESSION['parvus-locale'] == NULL)
            {

                static::getConfig();

                /** If the default locale not exist */
                if (static::$config['default'] == NULL)
                {

                    throw new \RuntimeException('Default language not found in app/config/Language.php',E_ERROR);

                }

                /** Save in session */
                $_SESSION['parvus-locale'] = static::$config['default'];

            }

            /** Save in the class */
            static::$locale = $_SESSION['parvus-locale'];

            /** Return the locale */
            return static::$locale;

        }

        /**
         * Load the config
         */
        private final static function getConfig ()
        {
            
            if (static::$config != NULL)
            {
                
                return true;
                
            }

            $file = path.'app/config/Language.php';

            /** If the config file not exist */
            if (!file_exists($file))
            {

                throw new \RuntimeException('Language configuration file not found.',E_ERROR);

            }

            static::$config = include_once ($file);

        }

    }
