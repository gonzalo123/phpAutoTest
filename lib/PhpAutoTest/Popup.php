<?php

namespace PhpAutoTest;

class Popup
{
    const POPUP_DELAY = 5000;
    const STATUS_OK   = 'ok';
    const STATUS_NOK  = 'nok';
    const STATUS_NONE = 'none';

    const IMG_OK  = 'ok.png';
    const IMG_NOK = 'nok.png';

    const LINUX    = 'Linux';
    const MAC_OS_X = 'Darwin';

    private $popup;

    /**
     * @param $header
     * @return Popup\Iface
     * @throws \Exception
     */
    public static function factory($header)
    {
        switch (PHP_OS) {
            case self::LINUX:
                return new Popup\Linux($header);
                break;
            case self::MAC_OS_X:
                return new Popup\Mac($header);
                break;
            default:
                throw new \Exception('Not implemented.');
        }
    }
}