<?php

declare(strict_types=1);

namespace Service;

use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>service.Message</code>
 */
class Message extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string msg = 1;</code>
     */
    private $msg = '';

    /**
     * Constructor.
     *
     * @param  array  $data  {
     *                       Optional. Data for populating the Message object.
     *
     * @type string $msg
     *              }
     */
    public function __construct($data = null)
    {
        \GPBMetadata\Test::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string msg = 1;</code>
     *
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Generated from protobuf field <code>string msg = 1;</code>
     *
     * @param  string  $var
     * @return $this
     */
    public function setMsg($var)
    {
        GPBUtil::checkString($var, true);
        $this->msg = $var;

        return $this;
    }
}
