<?php
    /**
     * @param $prText
     * @param null $prArray
     * @return array|mixed
     */
    function lang ($prText,$prArray = NULL)
    {

        return \Parvus\Language::get($prText,$prArray);

    }
