<?php

namespace App\Test\Service;

use App\Service\FileUploader;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;


class FileUploaderTestUnitTest extends TestCase
{
    
    public function testSuccessUploadBase64File(){

    
        $imageBase64 ="data:image/jpeg;base64,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";

       $data = explode(",",$imageBase64 );
        // mokear servicios 
        
        /**
         * @var FilesystemInterface&MockObject $filesystem
         */
        $filesystem = $this->getMockBuilder(FilesystemInterface::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $filesystem
            ->expects(self::exactly(1)) // se llama una sola vez
            ->method('write')  //método a moquear
            ->with($this->isType('string'), base64_decode($data[1]))  // tipos de los argumentos
            // ->willReturn('');
            ;

        
        $fileUploader = new FileUploader($filesystem);
        // este es el método a testear:
        $fileName = $fileUploader->uploadBse64File($imageBase64); 

        $this->assertNotEmpty($fileName);
        $this->assertContains('.jpeg',$fileName);

    }

}