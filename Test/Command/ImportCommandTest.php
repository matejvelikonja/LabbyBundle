<?php

namespace Velikonja\LabbyBundle\Test\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Command\ImportCommand;

class ImportCommandTest extends CommandTestCase
{
    /**
     * Test file not found.
     */
    public function testIfCommandFailsWhenFileIsNotFound()
    {
        $exitCode = $this->tester->run(
            array(
                'command' => ImportCommand::COMMAND_NAME,
                'file'    => '/non-existing-file.hopefully',
            )
        );

        $this->assertEquals(1, $exitCode);
    }

    /**
     * Test simple execute of dump command.
     */
    public function testExecute()
    {
        $exitCode = $this->tester->run(
            array(
                'command' => ImportCommand::COMMAND_NAME,
                'file'    => $this->getFixturesDir() . '/test-dump.sql',
            ),
            array(
                'verbosity' => OutputInterface::VERBOSITY_DEBUG
            )
        );

        $display = trim($this->tester->getDisplay());

        $this->assertEquals(0, $exitCode, $display);

        $this->assertRegExp(
            '/^Database successfully imported!$/',
            $display,
            'Wrong output of dump command detected. Did it print the SQL dump?'
        );
    }

}
