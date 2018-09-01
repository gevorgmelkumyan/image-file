<?php

namespace GM;

use \Exception;

class ImageFile {

    private $base64String;
    private $imageFileString;
    private $defaultPath;
    private $type;

    /**
     * ImageFile constructor.
     *
     * @param string $base64String
     * @throws Exception
     */
    public function __construct(string $base64String) {

        $this->validateBase64String($base64String);

        $base64WithoutDataType = explode('data:image/', $base64String);

        $content = explode(';base64,', $base64WithoutDataType[1]);

        if ($content === false) {
            throw new Exception(Error::WRONG_FORMAT);
        }

        $this->type = $content[0];
        $this->base64String = $content[1];
        $this->imageFileString = base64_decode($this->base64String);

        if ($this->imageFileString === false) {
            throw new Exception(Error::WRONG_FORMAT);
        }

        $this->defaultPath = '/';
    }

    /**
     * Return the string representation of the image.
     *
     * @return string
     */
    public function getImageString() : string {
        return $this->imageFileString;
    }

    /**
     * Store the image to the storage given by $path and give him a random name started by $prefix.
     *
     * @param null|string $path format: 'path/to/the/folder/', default path: '/'
     * @param null|string $prefix
     * @return null|string
     * @throws Exception
     */
    public function store(?string $path = null, ?string $prefix = null) : ?string {

        $fileName = uniqid($prefix ?? 'IM') . '.' . $this->type;
        $fullPath = $fileName;

        $fileStream = fopen($fullPath, 'wb');

        if ($fileStream === false) {
            throw new Exception(Error::FILESTREAM_ERROR);
        }

        if (fwrite($fileStream, $this->imageFileString) === false) {
            throw new Exception(Error::FILESTREAM_ERROR);
        }

        fclose($fileStream);

        return $fullPath;
    }

    private function validateBase64String(string $base64String) : void {

        if (strpos($base64String, 'data:image/') === false ||
            strpos($base64String, ';base64,') === false) {

            throw new Exception(Error::WRONG_IMAGE_FORMAT);
        }
    }

}