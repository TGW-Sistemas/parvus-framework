<?php
	namespace Parvus;

	class Text
	{

		/**
		 * Make a string to camel case
		 * @param $prString
		 * @param bool $prFirstUpper
		 * @return string
		 */
		public final static function camelCase ($prString, $prFirstUpper = false)
		{

			if( $prFirstUpper == true )
			{

				$prString[0] = mb_strtoupper($prString[0],'UTF-8');

			}

			$function = create_function('$c', 'return strtoupper($c[1]);');

			return preg_replace_callback('/_([a-z])/', $function, preg_replace_callback('/-([a-z])/', $function, $prString));

		}

	}