<?php

namespace Velikonja\LabbyBundle\Test\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Velikonja\LabbyBundle\Test\App\AppKernel;

abstract class CommandTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApplicationTester
     */
    protected $tester;

    /**
     * Temporary test dir, set also in config.yml
     *
     * @var string
     */
    protected $tmpDir;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * Prepare environment.
     */
    public function setUp()
    {
        $application = new Application(
            new AppKernel('test', true)
        );
        $application->setAutoExit(false);

        $this->tester = new ApplicationTester($application);
        $this->tmpDir = '/tmp/labby-bundle-tests';
        $this->fs     = new Filesystem();

        $this->dropTestDB();
        $this->createTestDB();
        $this->createTempDir();
    }

    /**
     * Clean up.
     */
    public function tearDown()
    {
        $this->dropTestDB();
        $this->removeTempDir();
    }

    /**
     * Drops the test database.
     */
    private function dropTestDB()
    {
        $exitCode = $this->tester->run(array(
            'command' => 'doctrine:database:drop',
            '--force' => true
        ));

        # 0 for successful exit
        # 1 for exiting if database does not exists
        if (! in_array($exitCode, array(0, 1))) {
            throw new \RuntimeException(trim($this->tester->getDisplay()));
        }

    }

    /**
     * Create test database.
     */
    private function createTestDB()
    {
        $exitCode = $this->tester->run(array(
            'command' => 'doctrine:database:create'
        ), array(
            'verbosity' => OutputInterface::VERBOSITY_DEBUG
        ));

        if ($exitCode) {
            throw new \RuntimeException(trim($this->tester->getDisplay()));
        }
    }

    /**
     * Creates temporary folder.
     */
    private function createTempDir()
    {
        $this->fs->remove($this->tmpDir);
        $this->fs->mkdir(
            array(
                $this->tmpDir,
            )
        );
    }

    /**
     * Remove temporary folder.
     */
    private function removeTempDir()
    {
        $this->fs->remove($this->tmpDir);
    }

    /**
     * @return string
     */
    protected function getFixturesDir()
    {
        return __DIR__ . '/../fixtures';
    }
}
