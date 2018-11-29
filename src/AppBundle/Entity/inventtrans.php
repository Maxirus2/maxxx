<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * inventtrans
 *
 * @ORM\Table(name="inventtrans")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\inventtransRepository")
 */
class inventtrans
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
     * @ORM\Column(name="direction", type="string", length=255)
     */
    private $direction;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=255)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="item_id", type="integer")
     */
    private $itemId;

    /**
     * @var int
     *
     * @ORM\Column(name="inventdim_id", type="integer")
     */
    private $inventdimId;

    /**
     * @var string
     *
     * @ORM\Column(name="qty", type="string", length=255)
     */
    private $qty;


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
     * Set direction
     *
     * @param string $direction
     *
     * @return inventtrans
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return inventtrans
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return inventtrans
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set inventdimId
     *
     * @param integer $inventdimId
     *
     * @return inventtrans
     */
    public function setInventdimId($inventdimId)
    {
        $this->inventdimId = $inventdimId;

        return $this;
    }

    /**
     * Get inventdimId
     *
     * @return int
     */
    public function getInventdimId()
    {
        return $this->inventdimId;
    }

    /**
     * Set qty
     *
     * @param string $qty
     *
     * @return inventtrans
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return string
     */
    public function getQty()
    {
        return $this->qty;
    }
}

