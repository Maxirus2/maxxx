<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * wherehouse
 *
 * @ORM\Table(name="wherehouse")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\wherehouseRepository")
 */
class wherehouse
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
     * @ORM\Column(name="name_sklad", type="string", length=255)
     */
    private $nameSklad;

       /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=1000)
     */
    private $img;

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
     * Set nameSklad
     *
     * @param string $nameSklad
     *
     * @return wherehouse
     */
    public function setNameSklad($nameSklad)
    {
        $this->nameSklad = $nameSklad;

        return $this;
    }

    /**
     * Get nameSklad
     *
     * @return string
     */
    public function getNameSklad()
    {
        return $this->nameSklad;
    }

    /**
     * Set img
     *
     * @param string $img
     *
     * @return wherehouse
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }
}

