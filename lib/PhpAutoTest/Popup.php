<?php

namespace PhpAutoTest;

class Popup implements Popup\Iface
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

    public function __construct($header)
    {
        switch (PHP_OS) {
            case self::LINUX:
                $this->popup = new Popup\Linux($header);
                break;
            case self::MAC_OS_X:
                $this->popup = new Popup\Mac($header);
                break;
        }
    }

    public function show($message, $status = Popup::STATUS_NONE)
    {
        $this->popup->show($message, $status);
    }
}