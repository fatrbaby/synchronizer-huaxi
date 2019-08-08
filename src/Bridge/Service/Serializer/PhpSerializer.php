<?php


namespace Bridge\Service\Serializer;


class PhpSerializer implements SerializerInterface
{
    public function pack($data)
    {
        return serialize($data);
    }

    public function unpack($data)
    {
        return unserialize($data);
    }
}
