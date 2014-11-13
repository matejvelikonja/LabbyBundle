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
     * Test simple execute of import command.
     *
     * @param bool $compress
     *
     * @dataProvider getCompressedOptions
     */
    public function testExecute($compress)
    {
        $fileExt = $compress ? 'zip' : 'sql';
        $path    = $this->getFixturesDir() . '/test-dump.' . $fileExt;

        $exitCode = $this->tester->run(
            array(
                'command' => ImportCommand::COMMAND_NAME,
                'file'    => $path,
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
            'Wrong output of import command detected.'
        );
    }

    /**
     * @return array|boolean[][]
     */
    public function getCompressedOptions()
    {
        return array(
            array(false),
            array(true),
        );
    }
}
