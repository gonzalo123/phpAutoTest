<?php

namespace PhpAutoTest\Popup;

class Mac implements Iface
{
    private $header;

    public function __construct($header)
    {
        throw new \Exception('Not implemented. Wanna help with MAC_OS_X version? contact with me');
    }

    public function show($message, $status = Popup::STATUS_NONE)
    {
    }
}