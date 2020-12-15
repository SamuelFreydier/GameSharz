<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Length(
     *      min = 3,
     *      max = 20,
     *      minMessage = "Votre nom d'utilisateur doit faire au minimum {{ limit }} caractères de long.",
     *      maxMessage = "Votre nom d'utilisateur doit faire au maximum {{ limit }} caractères de long."
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Votre mot de passe doit faire au minimum {{limit}} caractères de long."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 0,
     *      max = 200,
     *      minMessage = "Votre biographie doit faire au minimum {{limit}} caractères de long.",
     *      maxMessage = "Votre biographie doit faire au maximum {{limit}} caractères de long."
     * )
     */
    private $img;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=DownloadUserPost::class, mappedBy="user", orphanRemoval=true)
     */
    private $downloadUserPosts;

    /**
     * @ORM\OneToMany(targetEntity=LikeUserPost::class, mappedBy="user", orphanRemoval=true)
     */
    private $likeUserPosts;

    /**
     * @ORM\OneToMany(targetEntity=Commentary::class, mappedBy="user", orphanRemoval=true)
     */
    private $commentaries;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->downloadUserPosts = new ArrayCollection();
        $this->likeUserPosts = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

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

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DownloadUserPost[]
     */
    public function getDownloadUserPosts(): Collection
    {
        return $this->downloadUserPosts;
    }

    public function addDownloadUserPost(DownloadUserPost $downloadUserPost): self
    {
        if (!$this->downloadUserPosts->contains($downloadUserPost)) {
            $this->downloadUserPosts[] = $downloadUserPost;
            $downloadUserPost->setUser($this);
        }

        return $this;
    }

    public function removeDownloadUserPost(DownloadUserPost $downloadUserPost): self
    {
        if ($this->downloadUserPosts->contains($downloadUserPost)) {
            $this->downloadUserPosts->removeElement($downloadUserPost);
            // set the owning side to null (unless already changed)
            if ($downloadUserPost->getUser() === $this) {
                $downloadUserPost->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LikeUserPost[]
     */
    public function getLikeUserPosts(): Collection
    {
        return $this->likeUserPosts;
    }

    public function addLikeUserPost(LikeUserPost $likeUserPost): self
    {
        if (!$this->likeUserPosts->contains($likeUserPost)) {
            $this->likeUserPosts[] = $likeUserPost;
            $likeUserPost->setUser($this);
        }

        return $this;
    }

    public function removeLikeUserPost(LikeUserPost $likeUserPost): self
    {
        if ($this->likeUserPosts->contains($likeUserPost)) {
            $this->likeUserPosts->removeElement($likeUserPost);
            // set the owning side to null (unless already changed)
            if ($likeUserPost->getUser() === $this) {
                $likeUserPost->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentary[]
     */
    public function getCommentaries(): Collection
    {
        return $this->commentaries;
    }

    public function addCommentary(Commentary $commentary): self
    {
        if (!$this->commentaries->contains($commentary)) {
            $this->commentaries[] = $commentary;
            $commentary->setUser($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->contains($commentary)) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getUser() === $this) {
                $commentary->setUser(null);
            }
        }

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
}
