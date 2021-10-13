<?php
    /**
     * @param $prItem
     * @param bool $prExit
     */
    function pre_print_r ($prItem, $prExit = true)
    {

        print('<pre>');
            print_r($prItem);
        print('</pre>');

        if ($prExit)
        {
            exit;
        }

    }

    /**
     * @param $prItem
     * @param bool $prExit
     */
    function pre_var_dump ($prItem, $prExit = true)
    {

        print('<pre>');
            var_dump($prItem);
        print('</pre>');

        if ($prExit)
        {
            exit;
        }

    }