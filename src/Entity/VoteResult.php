<?php

namespace AppBundle\Entity;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use AppBundle\Entity\Election\ListTotalResult;
use AppBundle\Entity\Election\VoteResultListCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VoteResultRepository")
 *
 * @Algolia\Index(autoIndex=false)
 */
class VoteResult
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var VotePlace
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VotePlace", inversedBy="voteResults")
     * @ORM\JoinColumn(nullable=false)
     */
    private $votePlace;

    /**
     * @var ElectionRound
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ElectionRound")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $electionRound;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $registered = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $abstentions = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $voters = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $expressed = 0;

    /**
     * @var ListTotalResult[]|Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Election\ListTotalResult", mappedBy="voteResult", cascade={"all"})
     */
    private $listTotalResults;

    /**
     * @var Adherent|null
     *
     * @Gedmo\Blameable(on="create")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Adherent")
     */
    protected $createdBy;

    /**
     * @var Adherent|null
     *
     * @Gedmo\Blameable(on="update")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Adherent")
     */
    protected $updatedBy;

    public function __construct(VotePlace $votePlace, ElectionRound $electionRound)
    {
        $this->votePlace = $votePlace;
        $this->electionRound = $electionRound;

        $this->listTotalResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVotePlace(): ?VotePlace
    {
        return $this->votePlace;
    }

    public function setVotePlace(VotePlace $votePlace): void
    {
        $this->votePlace = $votePlace;
    }

    public function getElectionRound(): ?ElectionRound
    {
        return $this->electionRound;
    }

    public function setElectionRound(ElectionRound $electionRound): void
    {
        $this->electionRound = $electionRound;
    }

    public function getRegistered(): ?int
    {
        return $this->registered;
    }

    public function setRegistered(int $registered): void
    {
        $this->registered = $registered;
    }

    public function getAbstentions(): ?int
    {
        return $this->abstentions;
    }

    public function setAbstentions(int $abstentions): void
    {
        $this->abstentions = $abstentions;
    }

    public function getVoters(): ?int
    {
        return $this->voters;
    }

    public function setVoters(int $voters): void
    {
        $this->voters = $voters;
    }

    public function getExpressed(): ?int
    {
        return $this->expressed;
    }

    public function setExpressed(int $expressed): void
    {
        $this->expressed = $expressed;
    }

    public function getAbstentionsPercentage(): ?float
    {
        if (0 === $this->registered) {
            return null;
        }

        return ($this->abstentions / $this->registered) * 100;
    }

    public function getExpressedPercentage(): ?float
    {
        if (0 === $this->registered) {
            return null;
        }

        return ($this->expressed / $this->registered) * 100;
    }

    public function getVotersPercentage(): ?float
    {
        if (0 === $this->registered) {
            return null;
        }

        return ($this->voters / $this->registered) * 100;
    }

    public function isComplete(): bool
    {
        return $this->registered && $this->abstentions && $this->expressed && $this->voters && !empty($this->lists);
    }

    public function isPartial(): bool
    {
        return $this->registered || $this->abstentions || $this->expressed || $this->voters || !empty($this->lists);
    }

    /**
     * @return ListTotalResult[]
     */
    public function getListTotalResults(): array
    {
        return $this->listTotalResults->toArray();
    }

    public function updateLists(VoteResultListCollection $listCollection): void
    {
        $currentLists = $this->listTotalResults->map(static function (ListTotalResult $totalResult) {
            return $totalResult->getList();
        });

        foreach ($listCollection->getLists() as $newListToAdd) {
            if (!$currentLists->contains($newListToAdd)) {
                $listTotalResult = new ListTotalResult($newListToAdd);
                $listTotalResult->setVoteResult($this);

                $this->listTotalResults->add($listTotalResult);
            }
        }
    }

    public function setListTotalResults(array $listTotalResults): void
    {
        $this->listTotalResults = new ArrayCollection($listTotalResults);
    }

    public function getCreatedBy(): ?Adherent
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Adherent $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getUpdatedBy(): ?Adherent
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?Adherent $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}
