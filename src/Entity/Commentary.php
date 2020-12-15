<?php

namespace App\Entity;

use App\Repository\CommentaryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentaryRepository::class)
 */
class Commentary
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentaries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min=0,
     *      max=255,
     *      maxMessage = "Votre commentaire doit faire au maximum {{ limit }} caractères de long."
     * )
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="commentaries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    private $datestring;

    public function getDateString() {
        return $this->datestring;
    }

    public function setDateString($datestring) {
        $this->datestring = $datestring;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
