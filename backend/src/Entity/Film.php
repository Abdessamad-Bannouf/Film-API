<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "app_api_film_single",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"film:single", "film:list"})
 * )
 * 
 * @Hateoas\Relation(
 *     name = "categories",
 *     embedded = @Hateoas\Embedded(
 *         "expr(object.getCategories())",
 *         exclusion = @Hateoas\Exclusion(groups={"film:single", "film:list"})
 *     )
 * )
 */
#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:list", "film:single", "film:delete"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:list", "film:single", "film:delete", "film:patch", "film:put"})
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:list", "film:single", "film:delete", "film:patch", "film:put"})
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:list", "film:single", "film:delete", "film:patch", "film:put"})
     */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:list", "film:single", "film:delete", "film:patch", "film:put"})
     */
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $note = null;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:single", "film:patch", "film:put"})
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'films', cascade: ["persist"])]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addFilm($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeFilm($this);
        }

        return $this;
    }
}
