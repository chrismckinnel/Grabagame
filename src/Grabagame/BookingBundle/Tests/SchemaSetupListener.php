<?php

namespace Labour\CampaignDbBundle\Tests;

use DoctrineExtensions\PHPUnit\Event\EntityManagerEventArgs,
    Doctrine\ORM\Tools\SchemaTool,
    Doctrine\ORM\EntityManager;

class SchemaSetupListener
{
    public function preTestSetUp(EntityManagerEventArgs $eventArgs)
    {
        static $count = 0;
        $em = $eventArgs->getEntityManager();

        $schemaTool = new SchemaTool($em);

        $cmf = $em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }
}
