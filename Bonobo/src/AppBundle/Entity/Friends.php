<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Friends
 *
 * @ORM\Table(name="friends")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FriendsRepository")
 */
class Friends
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
     * @var int
     *
     * @ORM\Column(name="id_auth", type="integer")
     */
    private $idAuth;

    /**
     * @var int
     *
     * @ORM\Column(name="id_dest", type="integer")
     */
    private $idDest;


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
     * Set idAuth
     *
     * @param integer $idAuth
     *
     * @return Friends
     */
    public function setIdAuth($idAuth)
    {
        $this->idAuth = $idAuth;

        return $this;
    }

    /**
     * Get idAuth
     *
     * @return int
     */
    public function getIdAuth()
    {
        return $this->idAuth;
    }

    /**
     * Set idDest
     *
     * @param integer $idDest
     *
     * @return Friends
     */
    public function setIdDest($idDest)
    {
        $this->idDest = $idDest;

        return $this;
    }

    /**
     * Get idDest
     *
     * @return int
     */
    public function getIdDest()
    {
        return $this->idDest;
    }
}
