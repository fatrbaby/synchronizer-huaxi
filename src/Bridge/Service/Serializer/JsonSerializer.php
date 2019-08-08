<?php

namespace Bridge\Service\Serializer;

class JsonSerializer implements SerializerInterface
{
    public function pack($data)
    {
        return json_encode($data);
    }

    public function unpack($data)
    {
        return json_decode($data);
    }

}
