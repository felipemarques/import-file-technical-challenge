<?php

namespace App\Entity;

use App\Repository\ObjectPropsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ObjectPropsRepository::class)
 */
class ObjectProps
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Objects::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $object_id;

    /**
     * @ORM\ManyToOne(targetEntity=Fields::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $field_id;

    /**
     * @ORM\Column(type="string")
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectId(): ?Objects
    {
        return $this->object_id;
    }

    public function setObjectId(?Objects $object_id): self
    {
        $this->object_id = $object_id;

        return $this;
    }

    public function getFieldId(): ?Fields
    {
        return $this->field_id;
    }

    public function setFieldId(?Fields $field_id): self
    {
        $this->field_id = $field_id;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
