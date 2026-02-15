<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
// Restricción única a nivel de base de datos
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
// Validación única a nivel de aplicación (formulario)
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Identificador único del usuario (clave primaria)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nombre visible del usuario
    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    // Email del usuario (se usa como identificador de login)
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * Roles del usuario (ej: ROLE_USER, ROLE_ADMIN)
     * Symfony exige que siempre exista al menos ROLE_USER
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    // Contraseña hasheada (nunca se almacena en texto plano)
    #[ORM\Column]
    private ?string $password = null;

    // Fecha de registro del usuario
    #[ORM\Column]
    private \DateTimeImmutable $fechaRegistro;

    // Relación OneToMany con Pedido
    // Un usuario puede tener múltiples pedidos
    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Pedido::class)]
    private Collection $pedidos;

    // Relación OneToMany con Reseña
    // Un usuario puede escribir múltiples reseñas
    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Resena::class)]
    private Collection $resenas;

    public function __construct()
    {
        // Por defecto todo usuario tiene ROLE_USER
        $this->roles = ['ROLE_USER'];

        // Se asigna automáticamente la fecha de registro
        $this->fechaRegistro = new \DateTimeImmutable();

        // Inicialización de colecciones
        $this->pedidos = new ArrayCollection();
        $this->resenas = new ArrayCollection();
    }

    // =========================
    // Getters y setters básicos
    // =========================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Método obligatorio de UserInterface
    // Devuelve el identificador único del usuario (email)
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // Devuelve los roles del usuario
    // Se asegura de que siempre exista ROLE_USER
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Permite asignar roles manualmente
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Devuelve la contraseña hasheada
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Guarda la contraseña ya hasheada
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getFechaRegistro(): \DateTimeImmutable
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(\DateTimeImmutable $fechaRegistro): static
    {
        $this->fechaRegistro = $fechaRegistro;
        return $this;
    }

    /**
     * @return Collection<int, Pedido>
     */
    public function getPedidos(): Collection
    {
        return $this->pedidos;
    }

    /**
     * @return Collection<int, Resena>
     */
    public function getReseñas(): Collection
    {
        return $this->resenas;
    }

    // Método requerido por Symfony para limpiar datos sensibles temporales
    // En este caso no se almacenan datos temporales adicionales
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // No se almacenan datos sensibles temporales
    }
}
