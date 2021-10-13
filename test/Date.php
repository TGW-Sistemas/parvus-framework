<?php
    include_once '../vendor/autoload.php';

    ini_set('display_errors', 1);

    $date = '2015-10-11 13:00:55';

    print('<p>DATE BASE: '.$date);

    print('<hr />');

    print('<p>DATE: '.\Parvus\Date::date($date));
    print('<p>TIME: '.\Parvus\Date::time($date));
    print('<p>TIME WITH TIME ONLY: '.\Parvus\Date::time(date('H:i:s')));
    print('<p>DATETIME: '.\Parvus\Date::datetime($date));
    print('<p>SHOW: '.\Parvus\Date::show($date));

    print('<hr />');

    print('<p>DIFF IN MONTHS: '.\Parvus\Date::diff($date,NULL,'%m'));
    print('<p>DIFF IN YEARS: '.\Parvus\Date::diff($date,NULL,'%Y'));
    print('<p>DIFF IN DAYS: '.\Parvus\Date::diff($date,NULL,'%a'));