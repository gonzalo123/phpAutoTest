<?php

namespace PhpAutoTest\Popup;

interface Iface
{
    public function __construct($header);

    public function show($message, $status = Popup::STATUS_NONE);
}