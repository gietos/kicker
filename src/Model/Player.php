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
    const STATUS_ACTIVE = 'active';
    const STATUS_DELETED = 'deleted';

    const ROLE_PLAYER = 'player';
    const ROLE_ADMIN = 'admin';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $role = self::ROLE_PLAYER;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    protected $mean = GameInfo::DEFAULT_INITIAL_MEAN;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    protected $deviation = GameInfo::DEFAULT_INITIAL_STANDARD_DEVIATION;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status = self::STATUS_ACTIVE;

    /**
     * @var League
     * @ORM\ManyToOne(targetEntity="League", inversedBy="players")
     * @ORM\JoinColumn(name="league_id")
     */
    protected $league;

    public function __construct()
    {
        $this->id = Uuid::uuid1();
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

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return League
     */
    public function getLeague(): ?League
    {
        return $this->league;
    }

    /**
     * @param League $league
     */
    public function setLeague(?League $league)
    {
        $this->league = $league;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = hash('sha256', $password);
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role)
    {
        $this->role = $role;
    }
}
