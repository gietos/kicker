<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\Mapping as ORM;
use Moserware\Skills\GameInfo;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="player")
 */
class Player
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    protected $mean;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    protected $deviation;

    public function __construct()
    {
        $this->id = Uuid::uuid1();
        $this->mean = GameInfo::DEFAULT_INITIAL_MEAN;
        $this->deviation = GameInfo::DEFAULT_INITIAL_STANDARD_DEVIATION;
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

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMean()
    {
        return $this->mean;
    }

    /**
     * @param mixed $mean
     */
    public function setMean($mean)
    {
        $this->mean = $mean;
    }

    /**
     * @return mixed
     */
    public function getDeviation()
    {
        return $this->deviation;
    }

    /**
     * @param mixed $deviation
     */
    public function setDeviation($deviation)
    {
        $this->deviation = $deviation;
    }

    public function getScore()
    {
        return round(($this->mean - (3.0 * $this->deviation)) * 100);
    }
}
