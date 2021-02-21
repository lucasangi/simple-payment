<?php

declare(strict_types=1);

namespace SimplePayment\Tests\Framework;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;

use function assert;
use function sprintf;

class DoctrineTestCase extends KernelTestCase
{
    protected ObjectManager $entityManager;
    protected Application $application;

    protected function setUp(): void
    {
        static::bootKernel();

        $doctrine = static::$kernel->getContainer()->get('doctrine');
        assert($doctrine instanceof Registry);

        $em = $doctrine->getManager();
        assert($em instanceof ObjectManager);
        $this->entityManager = $em;

        $this->application = new Application(static::$kernel);
        $this->application->setAutoExit(false);

        $this->executeCommand('doctrine:database:drop --force');
        $this->executeCommand('doctrine:database:create');
        $this->executeCommand('doctrine:schema:create');

        parent::setUp();
    }

    protected function executeCommand(string $command): void
    {
        $command = sprintf('%s --quiet', $command);

        $this->application->run(new StringInput($command));
    }
}
