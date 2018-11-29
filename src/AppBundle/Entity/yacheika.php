<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * yacheika
 *
 * @ORM\Table(name="yacheika")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\yacheikaRepository")
 */
class yacheika
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name_yacheika", type="string", length=255)
     */
    private $nameYacheika;

    /**
     * @var int
     *
     * @ORM\Column(name="wherehouse_id", type="integer")
     */
    private $wherehouseId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nameYacheika
     *
     * @param string $nameYacheika
     *
     * @return yacheika
     */
    public function setNameYacheika($nameYacheika)
    {
        $this->nameYacheika = $nameYacheika;

        return $this;
    }

    /**
     * Get nameYacheika
     *
     * @return string
     */
    public function getNameYacheika()
    {
        return $this->nameYacheika;
    }

    /**
     * Set wherehouseId
     *
     * @param integer $wherehouseId
     *
     * @return yacheika
     */
    public function setWherehouseId($wherehouseId)
    {
        $this->wherehouseId = $wherehouseId;

        return $this;
    }

    /**
     * Get wherehouseId
     *
     * @return int
     */
    public function getWherehouseId()
    {
        return $this->wherehouseId;
    }
}

