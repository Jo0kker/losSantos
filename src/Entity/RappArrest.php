<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RappArrestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RappArrest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateArrest;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     minMessage="Trop court",
     *     max=13,
     *     maxMessage="Trop Long"
     * )
     */
    private $userArrest;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     minMessage="Trop court",
     *     max=15,
     *     maxMessage="Trop long"
     * )
     */
    private $lieux;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max=20,
     *     maxMessage="Trop de caractère"
     * )
     */
    private $otherUsers;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min="2",
     *     max="15",
     *     minMessage="Trop court",
     *     maxMessage="Trop long"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=3,
     *     minMessage="Trop court",
     *     max=15,
     *     maxMessage="Trop long"
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max=25,
     *     maxMessage="Trop long"
     * )
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permiNum;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $birthDay;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max=10,
     *     maxMessage="Trop de caractère"
     * )
     */
    private $sexe;

    /**
     * @ORM\Column(type="text")
     */
    private $charge;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     minMessage="Trop court",
     *     max=10,
     *     maxMessage="Trop long"
     * )
     */
    private $amande;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=1,
     *     minMessage = "Ne peu etre vide",
     *     max=10,
     *     maxMessage = "Trop long"
     * )
     */
    private $peine;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @throws \Exception
     */
    public function initDateTime(): void
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateArrest(): ?\DateTimeInterface
    {
        return $this->dateArrest;
    }

    public function setDateArrest(\DateTimeInterface $dateArrest): self
    {
        $this->dateArrest = $dateArrest;

        return $this;
    }

    public function getUserArrest(): ?string
    {
        return $this->userArrest;
    }

    public function setUserArrest(string $userArrest): self
    {
        $this->userArrest = $userArrest;

        return $this;
    }

    public function getLieux(): ?string
    {
        return $this->lieux;
    }

    public function setLieux(string $lieux): self
    {
        $this->lieux = $lieux;

        return $this;
    }

    public function getOtherUsers(): ?string
    {
        return $this->otherUsers;
    }

    public function setOtherUsers(string $otherUsers): self
    {
        $this->otherUsers = $otherUsers;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPermiNum(): ?string
    {
        return $this->permiNum;
    }

    public function setPermiNum(?string $permiNum): self
    {
        $this->permiNum = $permiNum;

        return $this;
    }

    public function getBirthDay(): ?string
    {
        return $this->birthDay;
    }

    public function setBirthDay(string $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getCharge(): ?string
    {
        return $this->charge;
    }

    public function setCharge(string $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getAmande(): ?string
    {
        return $this->amande;
    }

    public function setAmande(string $amande): self
    {
        $this->amande = $amande;

        return $this;
    }

    public function getPeine(): ?string
    {
        return $this->peine;
    }

    public function setPeine(string $peine): self
    {
        $this->peine = $peine;

        return $this;
    }
}
