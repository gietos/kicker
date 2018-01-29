<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="league",uniqueConstraints={@ORM\UniqueConstraint(name="name_uniq", columns={"name"})})
 */
class League
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var Player[]
     * @ORM\OneToMany(targetEntity="Player", fetch="EXTRA_LAZY", mappedBy="league")
     */
    protected $players;

    public function __construct()
    {
        $this->id = Uuid::uuid1();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return Player
     */
    public function getOwner(): Player
    {
        return $this->owner;
    }

    /**
     * @param Player $owner
     */
    public function setOwner(Player $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param Player[] $players
     */
    public function setPlayers(array $players)
    {
        foreach ($players as $player) {
            $player->setLeague($this);
        }

        $this->players = $players;
    }
}
