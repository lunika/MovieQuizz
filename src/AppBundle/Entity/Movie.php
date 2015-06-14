<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Movie
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRepository")
 */
class Movie
{
    const IMAGE_PATH = "https://image.tmdb.org/t/p/w300";
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="movies", cascade={"persist"})
     * @ORM\JoinTable(name="movie_person")
     */
    private $persons;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="text")
     */
    private $picture;

    public function __construct()
    {
        $this->persons = new ArrayCollection();
    }

    /**
     * @param $id integer
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Movie
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return Movie
     */
    public function setPicture($picture)
    {
        $path = sprintf('%s%s', self::IMAGE_PATH, $picture);

        $this->picture = $path;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @return ArrayCollection
     */
    public function getPersons()
    {
        return $this->persons;
    }

    public function isActor(Person $person)
    {
        return $this->persons->contains($person) ?: $this->persons->containsKey($person->getId());
    }

    public function setPerson(Person $person)
    {
        echo "foo";
    }

    public function addPerson(Person $person)
    {
        if (false === $this->isActor($person)) {
            $person->addMovie($this);
            $this->persons->set($person->getId(), $person);
        }


        return $this;
    }

    /**
     * Remove persons
     *
     * @param \AppBundle\Entity\Person $persons
     */
    public function removePerson(Person $persons)
    {
        $this->persons->removeElement($persons);
    }
}
