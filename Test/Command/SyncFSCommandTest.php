<?php

namespace Velikonja\LabbyBundle\Test\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Command\SyncFSCommand;

class SyncFSCommandTest extends CommandTestCase
{
    /**
     * Prepare environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->fs->mkdir(
            array(
                $this->tmpDir . '/src',
            )
        );
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
            '/Finished in [0-9\.]+ seconds!/',
            $display,
            'Wrong output of sync:fs command detected.'
        );

        $this->assertTrue(
            file_exists($this->tmpDir . '/dst'),
            'Did not sync. Have you changed config.yml?'
        );
    }
}
