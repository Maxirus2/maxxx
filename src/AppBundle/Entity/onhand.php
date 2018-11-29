<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * onhand
 *
 * @ORM\Table(name="onhand")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\onhandRepository")
 */
class onhand
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
     * @ORM\Column(name="itemid", type="string", length=255)
     */
    private $itemid;

    /**
     * @var int
     *
     * @ORM\Column(name="inventdim_id", type="integer")
     */
    private $inventdimId;


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
     * Set itemid
     *
     * @param string $itemid
     *
     * @return onhand
     */
    public function setItemid($itemid)
    {
        $this->itemid = $itemid;

        return $this;
    }

    /**
     * Get itemid
     *
     * @return string
     */
    public function getItemid()
    {
        return $this->itemid;
    }

    /**
     * Set inventdimId
     *
     * @param integer $inventdimId
     *
     * @return onhand
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
}

