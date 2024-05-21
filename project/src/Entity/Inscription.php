<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $learner;

    /**
     * @ORM\ManyToOne(targetEntity=Formation::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;

    /**
     * @ORM\Column(type="integer")
     */
    private $validated=0;


    public function existsWithLearnerAndFormation($learner, $formation)
    {
        return $this->learner === $learner && $this->formation === $formation;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLearner(): ?User
    {
        return $this->learner;
    }

    public function setLearner(?User $learner): self
    {
        $this->learner = $learner;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    public function isValidated(): ?int
    {
        return $this->validated;
    }

    public function setValidated(int $validated): self
    {
        $this->validated = $validated;

        return $this;
    }
}
