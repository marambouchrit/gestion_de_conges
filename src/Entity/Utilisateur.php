<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mot_de_passe = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: "id_role", referencedColumnName: "id")]
    private ?Role $role = null;

    #[ORM\ManyToOne(targetEntity: Departement::class)]
    #[ORM\JoinColumn(name: "id_departement", referencedColumnName: "id")]
    private ?Departement $departement = null;

    // === GETTERS / SETTERS ===

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }

    public function setNom(?string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }

    public function setPrenom(?string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getMotDePasse(): ?string { return $this->mot_de_passe; }

    public function setMotDePasse(?string $mot_de_passe): static { $this->mot_de_passe = $mot_de_passe; return $this; }

    public function getRole(): ?Role { return $this->role; }

    public function setRole(?Role $role): static { $this->role = $role; return $this; }

    public function getDepartement(): ?Departement { return $this->departement; }

    public function setDepartement(?Departement $departement): static { $this->departement = $departement; return $this; }

    // === OBLIGATOIRE POUR UserInterface ===

    public function getRoles(): array
    {
        return [$this->role ? $this->role->getNomRole() : 'ROLE_USER'];
    }

    // Méthode obligatoire de PasswordAuthenticatedUserInterface
    public function getPassword(): ?string
    {
        return $this->mot_de_passe;
    }

    // Méthode obligatoire de UserInterface depuis Symfony 5.3
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    // Pour compatibilité, retourne l’identifiant utilisateur (ancienne méthode)
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    
    public function eraseCredentials(): void
    {
        
}
}