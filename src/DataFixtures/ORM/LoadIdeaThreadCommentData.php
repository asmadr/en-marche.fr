<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AutoIncrementResetter;
use AppBundle\Entity\IdeasWorkshop\ThreadComment;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class LoadIdeaThreadCommentData extends AbstractFixture implements DependentFixtureInterface
{
    public const THREAD_COMMENT_01_UUID = 'b99933f3-180c-4248-82f8-1b0eb950740d';
    public const THREAD_COMMENT_02_UUID = '60123090-6cdc-4de6-9cb3-07e2ec411f2f';
    public const THREAD_COMMENT_03_UUID = 'f716d3ba-004f-4958-af26-a7b010a6d458';
    public const THREAD_COMMENT_04_UUID = '02bf299f-678a-4829-a6a1-241995339d8d';
    public const THREAD_COMMENT_05_UUID = '001a53d0-1134-429c-8dc1-c57643b3f069';
    public const THREAD_COMMENT_06_UUID = '3fa38c45-1122-4c48-9ada-b366b3408fec';
    public const THREAD_COMMENT_07_UUID = 'ecbe9136-3dc0-477d-b817-a25878dd639a';
    public const THREAD_COMMENT_08_UUID = '37116c8b-a36e-4a0d-8346-baba91cd1330';

    public function load(ObjectManager $manager)
    {
        AutoIncrementResetter::resetAutoIncrement($manager, 'ideas_workshop_comment');

        $threadAQProblemAdherent2 = $this->getReference('thread-aq-problem');
        $threadAQCompareAdherent5 = $this->getReference('thread-aq-compare');

        $commentFromAdherent6 = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_01_UUID),
            'Aenean viverra efficitur lorem',
            $this->getReference('adherent-6'),
            $threadAQProblemAdherent2,
            new \DateTime('-35 minutes')
        );

        $commentFromAdherent7 = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_02_UUID),
            'Lorem Ipsum Commentaris',
            $this->getReference('adherent-7'),
            $threadAQProblemAdherent2,
            new \DateTime('-30 minutes')
        );

        $commentFromAdherent8 = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_03_UUID),
            'Commentaire d\'un référent',
            $this->getReference('adherent-8'),
            $threadAQProblemAdherent2,
            new \DateTime('-25 minutes')
        );

        $anotherCommentFromAdherent8 = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_04_UUID),
            'Deuxième commentaire d\'un référent',
            $this->getReference('adherent-8'),
            $threadAQProblemAdherent2,
            new \DateTime('-20 minutes')
        );

        $commentFromAdherent9 = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_05_UUID),
            'Commentaire de Laura',
            $this->getReference('adherent-9'),
            $threadAQCompareAdherent5,
            new \DateTime('-15 minutes')
        );

        $commentNotApproved = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_06_UUID),
            'Commentaire non approuvé',
            $this->getReference('adherent-9'),
            $threadAQCompareAdherent5,
            new \DateTime('-10 minutes')
        );

        $commentReported = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_07_UUID),
            'Commentaire signalé',
            $this->getReference('adherent-9'),
            $threadAQCompareAdherent5,
            new \DateTime('-5 minutes')
        );

        $commentOfAdherentToUnregistrate = ThreadComment::create(
            Uuid::fromString(self::THREAD_COMMENT_08_UUID),
            'Commentaire de l\'adhérent à desadhérer',
            $this->getReference('adherent-13'),
            $threadAQCompareAdherent5,
            new \DateTime('-1 minute')
        );

        $manager->persist($commentFromAdherent6);
        $manager->persist($commentFromAdherent7);
        $manager->persist($commentFromAdherent8);
        $manager->persist($anotherCommentFromAdherent8);
        $manager->persist($commentFromAdherent9);
        $manager->persist($commentNotApproved);
        $manager->persist($commentReported);
        $manager->persist($commentOfAdherentToUnregistrate);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LoadAdherentData::class,
            LoadIdeaThreadData::class,
        ];
    }
}