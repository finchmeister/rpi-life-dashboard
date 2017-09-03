<?php


namespace RpiLifeDashboard\Quotes;


class RandomQuote
{

    const ENDPOINT = 'http://api.forismatic.com/api/1.0/?method=getQuote&format=json&lang=en';

    public function getQuote()
    {
        return json_decode(file_get_contents(self::ENDPOINT));
    }


}