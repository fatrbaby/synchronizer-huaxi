<?php

namespace Bridge\Service\Serializer;

interface SerializerInterface
{
    public function pack($data);
    public function unpack($data);
}
