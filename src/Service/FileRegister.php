<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileRegister {
    public function __construct($fileName, $savePath)
    {
        $this->fileName = $fileName;
        $this->savePath = $savePath;
    }


    public function registerFile()
    {
        if ($this->fileName) {
            $originalFilename = pathinfo($this->fileName->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$this->fileName->guessExtension();
            try {
                $filesystem = new Filesystem();
                $filesystem->remove($this->fileName);
                $this->fileName->move($this->getParameter($this->savePath), $newFilename);
            } catch (FileException $e) {

            }
            $chemin = 'uploads/avatar/' . $newFilename;
            $user->setAvatar($chemin);
        }
    }
    
}