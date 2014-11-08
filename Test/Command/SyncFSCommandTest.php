<?php

namespace Velikonja\LabbyBundle\Test\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Velikonja\LabbyBundle\Test\App\AppKernel;
use Velikonja\LabbyBundle\Command\SyncFSCommand;

class SyncFSCommandTest extends CommandTestCase
{
    /**
     * Temporary test dir, set also in config.yml
     *
     * @var string
     */
    private $tmpDir;

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

        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
        $fs->mkdir(
            array(
                $this->tmpDir,
                $this->tmpDir . '/src',
            )
        );
    }

    /**
     * Clean up.
     */
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
    }

    /**
     * Test simple execute of dump command.
     */
    public function testExecute()
    {
        $exitCode = $this->tester->run(
            array(
                'command' => SyncFSCommand::COMMAND_NAME
            ),
            array(
                'verbosity' => OutputInterface::VERBOSITY_DEBUG
            )
        );

        $display = trim($this->tester->getDisplay());

        $this->assertEquals(0, $exitCode, $display);

        $this->assertRegExp(
            '/Finished!/',
            $display,
            'Wrong output of dump command detected. Did it print the SQL dump?'
        );

        $this->assertTrue(
            file_exists($this->tmpDir . '/dst'),
            'Did not sync. Have you changed config.yml?'
        );
    }

}
