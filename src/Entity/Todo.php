<?php

namespace App\Entity;

use App\Repository\TodoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TodoRepository::class)
 */
class Todo
{
    public const READ = "todo:read";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({Todo::READ, Tag::READ})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(Todo::READ)
     */
    private string $title;

    /**
     * @ORM\Column(name="position", type="integer", options={"default" : 0})
     * @Groups(Todo::READ)
     */
    private int $order;

    /**
     * @ORM\Column(type="boolean")
     * @Groups(Todo::READ)
     */
    private bool $completed;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="todos")
     * @Groups(Todo::READ)
     */
    private $tags;

    #[Pure] public function __construct()
    {
        $this->order = 0;
        $this->completed = false;
        $this->tags = new ArrayCollection();
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

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder($order): void
    {
        $this->order = $order;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addTodo($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeTodo($this);
        }

        return $this;
    }



}
