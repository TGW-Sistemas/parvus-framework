<?php
	namespace Parvus;

	class Download
	{

        /**
         * Make a download of a file
         * @param $prFile
         * @param null $prName
         */
        public final static function make ($prFile, $prName = NULL)
        {

            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename='.($prName != NULL ? $prName : basename($prFile)));
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($prFile));
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Expires: 0');

            readfile($prFile);

            exit;
        }

	}