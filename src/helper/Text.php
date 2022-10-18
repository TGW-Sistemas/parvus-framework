<?php
    /**
     * @param $prString
     * @return string
     */
    function nl2p($prString)
    {

        if ($prString != NULL)
        {

            return '<p>'.str_replace(array('<br>','<br />'),'<p>',nl2br(preg_replace("/[\r\n]+/", "\n", $prString)));

        }

    }
