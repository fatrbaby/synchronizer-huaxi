<?php

namespace Bridge\Service\Storage;

interface StorageInterface
{
    public function read($file);
    public function write($file, $content);
    public function exists($file);
    public function remove($file);
    public function lists();
}
