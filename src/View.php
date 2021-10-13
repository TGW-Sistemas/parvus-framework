<?php
    namespace Parvus;

    class View
    {

        /**
         * @param $prView
         * @param array $prArray
         * @return mixed
         */
        public final function render ($prView,$prArray = array())
        {
            
            /** If cache folder not exists, create this */
            if (!is_dir(path.'vendor/cache'))
            {

                mkdir(path.'vendor/cache',0775,true);

            }

            $blade = new \Philo\Blade\Blade(path.'app/view', path.'vendor/cache');

            return $blade->view()->make($prView,$prArray)->render();

        }

    }
