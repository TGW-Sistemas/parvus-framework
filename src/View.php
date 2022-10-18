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
            $blade = new \Philo\Blade\Blade(path.'app/view', path.'cache');

            return $blade->view()->make($prView,$prArray)->render();
        }

    }
