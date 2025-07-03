<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderHelper
{
    private $uploadsPath;
    private $slugger;
    public function __construct(string $uploadsPath, SluggerInterface $slugger)
    {
        $this->uploadsPath = $uploadsPath;
        $this->slugger = $slugger;
    }
    public function uploadProductImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath . '/product_images';

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename);

        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();


        $uploadedFile->move($destination, $newFilename);

        return $newFilename;
    }
    public function getTargetDirectory(): string
    {
        return $this->uploadsPath. '/product_images';
    }
    public function deleteProductImage(string $filename): void
    {
        $filePath = $this->getTargetDirectory() . '/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

}
