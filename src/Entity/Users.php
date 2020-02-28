<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @UniqueEntity(
 *     fields={"discord"},
 *     message="Adresse dicord déjà enregistrer"
 * )
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/(.*)#(\d{4})/",
     *     message="Merci d'adopter le bon format (ex:Jhon#1234)"
     * )
     */
    private $discord;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pwd;

    public $pwdConf;


    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": "assets/img/avatars/avatar5.jpeg"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $job;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contact", mappedBy="Author", orphanRemoval=true)
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mail", mappedBy="author")
     */
    private $mails;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Mail", mappedBy="dest")
     */
    private $mailsRecep;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MailSend", mappedBy="author", orphanRemoval=true)
     */
    private $mailSends;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MailSend", mappedBy="dest")
     */
    private $mailSendsDest;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RappInter", mappedBy="author")
     */
    private $rappInters;


    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->lspdRappArrests = new ArrayCollection();
        $this->mails = new ArrayCollection();
        $this->mailsRecep = new ArrayCollection();
        $this->mailSends = new ArrayCollection();
        $this->mailSendsDest = new ArrayCollection();
        $this->rappInters = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(string $discord): self
    {
        $this->discord = $discord;

        return $this;
    }

    public function getPwd(): ?string
    {
        return $this->pwd;
    }

    public function setPwd(string $pwd): self
    {
        $this->pwd = $pwd;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);

    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
        return $this->pwd;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
        return $this->discord;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setAuthor($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            // set the owning side to null (unless already changed)
            if ($contact->getAuthor() === $this) {
                $contact->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Mail[]
     */
    public function getMails(): Collection
    {
        return $this->mails;
    }

    public function addMail(Mail $mail): self
    {
        if (!$this->mails->contains($mail)) {
            $this->mails[] = $mail;
            $mail->setAuthor($this);
        }

        return $this;
    }

    public function removeMail(Mail $mail): self
    {
        if ($this->mails->contains($mail)) {
            $this->mails->removeElement($mail);
            // set the owning side to null (unless already changed)
            if ($mail->getAuthor() === $this) {
                $mail->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Mail[]
     */
    public function getMailsRecep(): Collection
    {
        return $this->mailsRecep;
    }

    public function addMailsRecep(Mail $mailsRecep): self
    {
        if (!$this->mailsRecep->contains($mailsRecep)) {
            $this->mailsRecep[] = $mailsRecep;
            $mailsRecep->addDest($this);
        }

        return $this;
    }

    public function removeMailsRecep(Mail $mailsRecep): self
    {
        if ($this->mailsRecep->contains($mailsRecep)) {
            $this->mailsRecep->removeElement($mailsRecep);
            $mailsRecep->removeDest($this);
        }

        return $this;
    }

    /**
     * @return Collection|MailSend[]
     */
    public function getMailSends(): Collection
    {
        return $this->mailSends;
    }

    public function addMailSend(MailSend $mailSend): self
    {
        if (!$this->mailSends->contains($mailSend)) {
            $this->mailSends[] = $mailSend;
            $mailSend->setAuthor($this);
        }

        return $this;
    }

    public function removeMailSend(MailSend $mailSend): self
    {
        if ($this->mailSends->contains($mailSend)) {
            $this->mailSends->removeElement($mailSend);
            // set the owning side to null (unless already changed)
            if ($mailSend->getAuthor() === $this) {
                $mailSend->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MailSend[]
     */
    public function getMailSendsDest(): Collection
    {
        return $this->mailSendsDest;
    }

    public function addMailSendsDest(MailSend $mailSendsDest): self
    {
        if (!$this->mailSendsDest->contains($mailSendsDest)) {
            $this->mailSendsDest[] = $mailSendsDest;
            $mailSendsDest->addDest($this);
        }

        return $this;
    }

    public function removeMailSendsDest(MailSend $mailSendsDest): self
    {
        if ($this->mailSendsDest->contains($mailSendsDest)) {
            $this->mailSendsDest->removeElement($mailSendsDest);
            $mailSendsDest->removeDest($this);
        }

        return $this;
    }

    public function getMailRecepNoRead()
    {
        $result = [];
        foreach ($this->mailsRecep as $mailMini) {
            if ($mailMini->getlu() === false){
                foreach ($mailMini->getDest() as $destinataire){
                    if ($this->getId() === $destinataire->getId()) {
                        $result[] = $mailMini;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @return Collection|RappInter[]
     */
    public function getRappInters(): Collection
    {
        return $this->rappInters;
    }

    public function addRappInter(RappInter $rappInter): self
    {
        if (!$this->rappInters->contains($rappInter)) {
            $this->rappInters[] = $rappInter;
            $rappInter->setAuthor($this);
        }

        return $this;
    }

    public function removeRappInter(RappInter $rappInter): self
    {
        if ($this->rappInters->contains($rappInter)) {
            $this->rappInters->removeElement($rappInter);
            // set the owning side to null (unless already changed)
            if ($rappInter->getAuthor() === $this) {
                $rappInter->setAuthor(null);
            }
        }

        return $this;
    }

}
