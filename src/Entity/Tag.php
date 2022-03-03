<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{

    public const READ = "tag:read";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({Todo::READ, Tag::READ})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(Tag::READ)
     */
    private string $title;

    /**
     * @ORM\ManyToMany(targetEntity=Todo::class, inversedBy="tags")
     * @Groups(Tag::READ)
     */
    private $todos;

    #[Pure] public function __construct()
    {
        $this->todos = new ArrayCollection();
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

    public function getTodos()
    {
        return $this->todos;
    }

    public function addTodo(Todo $todo): self
    {
        if (!$this->todos->contains($todo)) {
            $this->todos[] = $todo;
        }

        return $this;
    }

    public function removeTodo(Todo $todo): self
    {
        $this->todos->removeElement($todo);

        return $this;
    }
}
