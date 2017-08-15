<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="gain")
 */
class Gain
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
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    protected $player;

    /**
     * @var Result
     * @ORM\ManyToOne(targetEntity="Result")
     * @ORM\JoinColumn(name="result_id", referencedColumnName="id")
     */
    protected $result;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $gain;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->id = Uuid::uuid1();
        $this->createdAt = new \DateTime;
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
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    /**
     * @param Result $result
     */
    public function setResult(Result $result)
    {
        $this->result = $result;
    }

    /**
     * @return int
     */
    public function getGain(): int
    {
        return $this->gain;
    }

    /**
     * @param int $gain
     */
    public function setGain(int $gain)
    {
        $this->gain = $gain;
    }
}
