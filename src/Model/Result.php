<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="result")
 */
class Result
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @var Player[]
     * @ORM\ManyToMany(targetEntity="Player")
     * @ORM\JoinTable(name="result_winner",
     *      joinColumns={@ORM\JoinColumn(name="result_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")}
     * )
     */
    protected $winners;

    /**
     * @var Player[]
     * @ORM\ManyToMany(targetEntity="Player")
     * @ORM\JoinTable(name="result_loser",
     *      joinColumns={@ORM\JoinColumn(name="result_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")}
     * )
     */
    protected $losers;

    public function __construct()
    {
        $this->id = Uuid::uuid1();
    }

    /**
     * @return Player[]
     */
    public function getWinners()
    {
        return $this->winners;
    }

    /**
     * @param Player[] $winners
     */
    public function setWinners($winners)
    {
        $this->winners = $winners;
    }

    /**
     * @return Player[]
     */
    public function getLosers()
    {
        return $this->losers;
    }

    /**
     * @param Player[] $losers
     */
    public function setLosers($losers)
    {
        $this->losers = $losers;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}