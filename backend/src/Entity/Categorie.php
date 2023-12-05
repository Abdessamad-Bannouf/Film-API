<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "app_api_categorie_single",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"categorie:single", "categorie:list"})
 * )
 * 
 * @Hateoas\Relation(
 *     name = "categories",
 *     embedded = @Hateoas\Embedded(
 *         "expr(object.getFilms())",
 *         exclusion = @Hateoas\Exclusion(groups={"categorie:single", "categorie:list"})
 *     )
 * )
 */
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:list", "film:single", "film:patch", "film:put", "categorie:single", "categorie:list"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({"film:list", "film:single", "film:patch", "film:put", "categorie:single", "categorie:list"})
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @Serializer\Expose
     * @Serializer\Groups({"categorie:single"})
     */
    #[ORM\ManyToMany(targetEntity: Film::class, inversedBy: 'categories')]
    private Collection $films;

    public function __construct()
    {
        $this->films = new ArrayCollection();
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

    /**
     * @return Collection<int, Film>
     */
    public function getFilms(): Collection
    {
        return $this->films;
    }

    public function addFilm(Film $film): self
    {
        if (!$this->films->contains($film)) {
            $this->films->add($film);
        }

        return $this;
    }

    public function removeFilm(Film $film): self
    {
        $this->films->removeElement($film);

        return $this;
    }
}
