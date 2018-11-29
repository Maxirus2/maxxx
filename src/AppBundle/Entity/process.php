<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * process
 *
 * @ORM\Table(name="process")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\processRepository")
 */
class process
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

   /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lotid", type="string", length=255)
     */
    private $lotid;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=255)
     */
    private $size;

     /**
     * @var string
     *
     * @ORM\Column(name="config", type="string", length=255)
     */
    private $config;

     /**
     * @var string
     *
     * @ORM\Column(name="worker", type="string", length=255)
     */
    private $worker;

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
     * Set type
     *
     * @param string $type
     *
     * @return process
     */
    public function setTypes($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getTypes()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return process
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return process
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

   /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

       /**
     * Set name
     *
     * @param string $name
     *
     * @return process
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

   /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set lotid
     *
     * @param string $lotid
     *
     * @return process
     */
    public function setLotid($lotid)
    {
        $this->lotid = $lotid;

        return $this;
    }

    /**
     * Get lotid
     *
     * @return string
     */
    public function getLotid()
    {
        return $this->lotid;
    }

   /**
     * Set size
     *
     * @param string $size
     *
     * @return process
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

   /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set config
     *
     * @param string $config
     *
     * @return process
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

   /**
     * Get config
     *
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

  /**
     * Set worker
     *
     * @param string $worker
     *
     * @return process
     */
    public function setWorker($worker)
    {
        $this->worker = $worker;

        return $this;
    }

   /**
     * Get worker
     *
     * @return string
     */
    public function getWorker()
    {
        return $this->worker;
    }
}

