<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InventDim
 *
 * @ORM\Table(name="invent_dim")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InventDimRepository")
 */
class InventDim
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
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

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
     * @var int
     *
     * @ORM\Column(name="warehouse_id", type="integer")
     */
    private $warehouseId;

	 /**
     * @var int
     *
     * @ORM\Column(name="quarty", type="integer")
     */
    private $quarty;

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
     * Set color
     *
     * @param string $color
     *
     * @return InventDim
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
     * Set size
     *
     * @param string $size
     *
     * @return InventDim
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
     * @return InventDim
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
     * Set warehouseId
     *
     * @param integer $warehouseId
     *
     * @return InventDim
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    /**
     * Get warehouseId
     *
     * @return int
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

     /**
     * Set quarty
     *
     * @param integer $quarty
     *
     * @return InventDim
     */
    public function setquarty($quarty)
    {
        $this->quarty = $quarty;

        return $this;
    }

    /**
     * Get quarty
     *
     * @return int
     */
    public function getquarty()
    {
        return $this->quarty;
    }
}

