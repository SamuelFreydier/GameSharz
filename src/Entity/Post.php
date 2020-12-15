<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "Votre titre doit faire au minimum {{ limit }} caractères de long.",
     *      maxMessage = "Votre titre doit faire au maximum {{ limit }} caractères de long.",
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *      min = 0,
     *      max = 100,
     *      maxMessage = "Votre résumé doit faire au maximum {{ limit }} caractères de long."
     * )
     */
    private $description;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lien;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=DownloadUserPost::class, mappedBy="post", orphanRemoval=true)
     */
    private $downloadUserPosts;

    /**
     * @ORM\OneToMany(targetEntity=LikeUserPost::class, mappedBy="post", orphanRemoval=true)
     */
    private $likeUserPosts;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *      min=0,
     *      max=300,
     *      maxMessage = "Votre description doit faire au maximum {{ limit }} caractères de long."
     * )
     */
    private $text;

    private $likes;

    private $downloads;

    private $userassociated;

    /**
     * @ORM\OneToMany(targetEntity=Commentary::class, mappedBy="post", orphanRemoval=true)
     */
    private $commentaries;

    private $datestring;

    public function __construct()
    {
        $this->downloadUserPosts = new ArrayCollection();
        $this->likeUserPosts = new ArrayCollection();
        $this->nbLikes = 0;
        $this->commentaries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLikes() {
        return $this->likes;
    }

    public function setLikes($nbLikes) {
        $this->likes = $nbLikes;
    }

    public function getDownloads() {
        return $this->downloads;
    }

    public function setDownloads($nbDownloads) {
        $this->downloads = $nbDownloads;
    }

    public function getUserAssociated() {
        return $this->userassociated;
    }

    public function setUserAssociated($user) {
        $this->userassociated = $user;
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
            $downloadUserPost->setPost($this);
        }

        return $this;
    }

    public function removeDownloadUserPost(DownloadUserPost $downloadUserPost): self
    {
        if ($this->downloadUserPosts->contains($downloadUserPost)) {
            $this->downloadUserPosts->removeElement($downloadUserPost);
            // set the owning side to null (unless already changed)
            if ($downloadUserPost->getPost() === $this) {
                $downloadUserPost->setPost(null);
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
            $likeUserPost->setPost($this);
        }

        return $this;
    }

    public function removeLikeUserPost(LikeUserPost $likeUserPost): self
    {
        if ($this->likeUserPosts->contains($likeUserPost)) {
            $this->likeUserPosts->removeElement($likeUserPost);
            // set the owning side to null (unless already changed)
            if ($likeUserPost->getPost() === $this) {
                $likeUserPost->setPost(null);
            }
        }

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

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
            $commentary->setPost($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->contains($commentary)) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getPost() === $this) {
                $commentary->setPost(null);
            }
        }

        return $this;
    }

    public function getDateString() {
        return $this->datestring;
    }

    public function setDateString($datestring) {
        $this->datestring = $datestring;
    }
}
