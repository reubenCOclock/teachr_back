<?php

namespace App\Entity;

use App\Repository\TeachrRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TeachrRepository::class)
 */
class Teachr
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("read")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("read")
     */
    private $created_at;

    public function __construct(){
        $this->created_at=new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
