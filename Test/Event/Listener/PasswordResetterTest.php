<?php

namespace Velikonja\LabbyBundle\Test\Event\Listener;

use FOS\UserBundle\Util\UserManipulator;
use Velikonja\LabbyBundle\Event\Listener\PasswordResetter;
use Velikonja\LabbyBundle\Event\SyncEvent;

class PasswordResetterTest extends \PHPUnit_Framework_TestCase
{
    public function testIfManipulatorChangesPasswordForAllUsers()
    {
        $users = array(
            array('username' => 'test', 'password' => 'password'),
            array('username' => 'test2', 'password' => 'password2'),
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject | UserManipulator $manipulator */
        $manipulator = $this
            ->getMockBuilder('FOS\UserBundle\Util\UserManipulator')
            ->disableOriginalConstructor()
            ->getMock();

        $manipulator
            ->expects($this->exactly(count($users)))
            ->method('changePassword');

        $resetter = new PasswordResetter($manipulator, $users);
        $resetter->onPostImport(new SyncEvent());
    }
}
