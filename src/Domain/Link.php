<?php

namespace WebLinks\Domain;

class Link 
{
    /**
     * Link id.
     *
     * @var integer
     */
    private $id;

    /**
     * Link title.
     *
     * @var string
     */
    private $title;

    /**
     * Link url.
     *
     * @var string
     */
    private $url;
    
    /**
     * Link author.
     *
     * @var \WebLinks\Domain\User
     */
    private $author;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }
}
