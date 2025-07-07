<?php

namespace App\Entity;

use App\Repository\SoldeCongeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoldeCongeRepository::class)]
class SoldeConge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nb_jours_total = null;

    #[ORM\Column]
    private ?int $nb_jours_pris = null;

    #[ORM\OneToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id", unique: true)]
    private ?Utilisateur $utilisateur = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbJoursTotal(): ?int
    {
        return $this->nb_jours_total;
    }

    public function setNbJoursTotal(int $nb_jours_total): static
    {
        $this->nb_jours_total = $nb_jours_total;
        return $this;
    }

    public function getNbJoursPris(): ?int
    {
        return $this->nb_jours_pris;
    }

    public function setNbJoursPris(int $nb_jours_pris): static
    {
        $this->nb_jours_pris = $nb_jours_pris;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
}
