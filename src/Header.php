<?php
    namespace Parvus;

    class Header
    {

        /**
         * Header PDF file
         */
        public static function pdf ()
        {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/pdf');
        }

        /**
         * Header a CSV file with name
         * @param $prFileName
         */
        public final static function CSV ($prFileName)
        {

            if (mb_substr(mb_strtolower($prFileName,'UTF-8'),-4) != '.csv')
            {

                $prFileName.= '.csv';

            }

            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="'.$prFileName);
        }

        /**
         * Header a JSON with array
         * @param null $prArray
         */
        public final static function JSON ($prArray = NULL)
        {
            header('Content-Type: application/json');

            if (is_array($prArray))
            {

                exit(json_encode($prArray));

            }
        }

    }
