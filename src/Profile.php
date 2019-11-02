<?php

namespace NicholasCar\ConnegP;

class Profile
{
    public $title;
    public $description;
    public $mediatypes;
    public $mediatype_default;

    /**
     * Profile constructor. Sets all variables in Profile
     */
    public function __construct($title, $description, $mediatypes, $mediatype_default)
    {
        $this->title = $title;
        $this->description = $description;
        $this->mediatypes = $mediatypes;
        $this->mediatype_default = $mediatype_default;
    }
}