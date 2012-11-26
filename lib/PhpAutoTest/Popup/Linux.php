<?php

namespace PhpAutoTest\Popup;

use Sh\Sh,
    PhpAutoTest\Popup;

class Linux implements Iface
{
    private $header;

    public function __construct($header)
    {
        $this->header = $header;
        $this->sh     = new Sh();
    }

    public function show($message, $status = Popup::STATUS_NONE)
    {
        $params = array('-t', Popup::POPUP_DELAY);
        switch ($status) {
            case Popup::STATUS_OK:
                $params[] = '-i';
                $params[] = __DIR__ . '/' . Popup::IMG_OK;
                break;
            case Popup::STATUS_NOK:
                $params[] = '-i';
                $params[] = __DIR__ . '/' . Popup::IMG_NOK;
                break;
        }
        $params[] = $this->header;
        $params[] = $message;
        echo $this->sh->runCommand('notify-send', $params);
    }
}