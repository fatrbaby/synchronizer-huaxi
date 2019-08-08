<?php

namespace Bridge\Service\Serializer;

class MsgpackSerializer implements SerializerInterface
{
    public function pack($data)
    {
        return msgpack_pack($data);
    }

    public function unpack($data)
    {
        return msgpack_unpack($data);
    }
}
