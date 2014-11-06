<?php

namespace Velikonja\LabbyBundle\Test\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Command\DumpCommand;

class DumpCommandTest extends CommandTestCase
{
    /**
     * Test simple execute of dump command.
     */
    public function testExecute()
    {
        $exitCode = $this->tester->run(
            array(
                'command' => DumpCommand::COMMAND_NAME
            ),
            array(
                'verbosity' => OutputInterface::VERBOSITY_DEBUG
            )
        );

        $display = trim($this->tester->getDisplay());

        $this->assertEquals(0, $exitCode, $display);

        $this->assertRegExp(
            '/-- Dump completed on/',
            $display,
            'Wrong output of dump command detected. Did it print the SQL dump?'
        );
    }


}
 