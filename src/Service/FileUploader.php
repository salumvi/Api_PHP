<?php
namespace App\Service;

use League\Flysystem\FilesystemInterface;

class FileUploader {

    private $defaultStorage;
    public function __construct(FilesystemInterface $defaultStorage) {
        $this->defaultStorage = $defaultStorage;
    }


    public function uploadBse64File(string $base64file): string 
    {
        $extension =explode('/',explode( ';', $base64file)[0])[1];
        $data = explode(',', $base64file)[1];
        $filename = sprintf('%s.%s',uniqid('book_', true), $extension);
        // guardamos la imagen en lacarpeta definida en la configuracion de flysystem.yaml
        $this->defaultStorage->write($filename, base64_decode($data));

        return $filename;
    }
}