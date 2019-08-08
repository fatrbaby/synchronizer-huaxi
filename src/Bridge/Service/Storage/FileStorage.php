<?php

namespace Bridge\Service\Storage;

use Bridge\Service\Serializer\SerializerInterface;

class FileStorage implements StorageInterface
{
    private $serializer = null;
    private $dir = null;

    public function __construct(SerializerInterface $serializer, $dir)
    {
        $this->dir = realpath($dir);

        if (!is_dir($this->dir)) {
            throw new \RuntimeException("Directory does not exists");
        }

        $this->serializer = $serializer;
    }

    public function read($file)
    {
        $file = $this->absolute($file);

        if (!$this->exists($file)) {
            throw new \RuntimeException(sprintf('%s does not exists', $file));
        }

        $handle = fopen($file, 'rb');
        $content = stream_get_contents($handle);
        fclose($handle);

        return $this->serializer->unpack($content);
    }

    public function write($file, $content)
    {
        $file = $this->absolute($file);
        $handle = fopen($file, 'wb+');

        if ($handle === false) {
            throw new \RuntimeException(sprintf('open %s failed', $file));
        }

        $wrote = fwrite($handle, $this->serializer->pack($content));
        fclose($handle);

        return $wrote;
    }

    public function exists($file)
    {
        return file_exists($file);
    }

    public function remove($file)
    {
        $file = $this->absolute($file);

        if (!$this->exists($file)) {
            return false;
        }

        return unlink($file);
    }

    public function lists()
    {
        $lists = glob($this->dir . '/*');

        $files = array_map(function ($file) {
            return ltrim(strtr($file, [$this->dir => '']), DIRECTORY_SEPARATOR);
        }, $lists);

        return $files;
    }

    public function absolute($file)
    {
        return rtrim($this->dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }

}

