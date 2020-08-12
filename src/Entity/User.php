<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields={"email"},
 * message="L'email que vous avez indiqué est déjà utilisé !"
 * )
 * @UniqueEntity(
 * fields={"username"},
 * message="Le nom d'utilisateur que vous avez indiqué est déjà utilisé !"
 * )
 */
class User implements UserInterface
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Le mot de passe doit faire minimum 8 caractères !")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vos mot de passes ne sont pas identiques !")
     */
    private $checkedPassword;

    /**
     * @ORM\ManyToOne(targetEntity=Ranking::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rank;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCheckedPassword(): ?string
    {
        return $this->checkedPassword;
    }

    public function setCheckedPassword(string $checkedPassword): self
    {
        $this->checkedPassword = $checkedPassword;

        return $this;
    }

    public function getRank(): ?Ranking
    {
        return $this->rank;
    }

    public function setRank(?Ranking $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function eraseCredentials() {}

    public function getSalt() {}

    public function getRoles() {
        return ['ROLE_USER'];
    }
}
