<?php

namespace Velikonja\LabbyBundle\Event\Listener;

use FOS\UserBundle\Util\UserManipulator;
use Velikonja\LabbyBundle\Event\SyncEvent;

class PasswordResetter
{
    /**
     * @var UserManipulator
     */
    private $manipulator;

    /**
     * @var array
     */
    private $users;

    /**
     * @param UserManipulator $manipulator
     * @param array           $users
     */
    public function __construct(UserManipulator $manipulator, array $users)
    {
        $this->manipulator = $manipulator;
        $this->users       = $users;
    }

    /**
     * Changes password for all users.
     *
     * @param SyncEvent $event
     */
    public function onPostImport(SyncEvent $event)
    {
        foreach ($this->users as $user) {
            $this->manipulator->changePassword($user['username'], $user['password']);
            $event->getOutput()->writeln(sprintf('Password resetted for user `%s`.', $user['username']));
        }
    }
}
