<?php

namespace App\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class RepositorySetup extends KernelTestCase
{
    protected EntityManagerInterface $em;
    protected Container $container;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();

        $this->em = $this->container->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($metadata);
    }

    public function tearDown(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);

        $this->em->clear();
        $this->em->getConnection()->close();

        parent::tearDown();
    }
}
