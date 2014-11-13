<?php

namespace Velikonja\LabbyBundle\Test\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Velikonja\LabbyBundle\Command\DumpCommand;

class DumpCommandTest extends CommandTestCase
{
    /**
     * Test simple execute of dump command to standard output.
     */
    public function testExecuteToStandardOutput()
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

    /**
     * Test simple execute of dump command to file.
     *
     * @param bool $compress
     *
     * @dataProvider getCompressedOptions
     */
    public function testExecuteToFile($compress)
    {
        $fileExt = $compress ? 'zip' : 'sql';
        $path    = $this->tmpDir . '/dump-to-file-test.' . $fileExt;

        $exitCode = $this->tester->run(
            array(
                'command' => DumpCommand::COMMAND_NAME,
                'file'    => $path,
                '--compress'  => $compress,
            ),
            array(
                'verbosity' => OutputInterface::VERBOSITY_DEBUG
            )
        );

        $display = trim($this->tester->getDisplay());

        $this->assertEquals(0, $exitCode, $display);

        $this->assertRegExp(
            sprintf('/%s`!$/', basename($path)),
            $display,
            'Wrong output of dump command detected.'
        );

        $this->assertFileExists($path, sprintf('File was not dumped to `%s`.', $path));
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
