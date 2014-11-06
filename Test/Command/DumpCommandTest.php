<?php

namespace Velikonja\LabbyBundle\Test\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\ApplicationTester;
use Velikonja\LabbyBundle\Command\DumpCommand;
use Velikonja\LabbyBundle\Test\App\AppKernel;


class DumpCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application(
            new AppKernel('test', true)
        );
        $application->setAutoExit(false);

        $tester   = new ApplicationTester($application);
        $exitCode = $tester->run(
            array(
                'command' => DumpCommand::COMMAND_NAME
            ),
            array(
                'verbosity' => OutputInterface::VERBOSITY_DEBUG
            ));

        $this->assertEquals(0, $exitCode, trim($tester->getDisplay()));
    }
}
 