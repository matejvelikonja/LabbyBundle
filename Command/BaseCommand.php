<?php

namespace Velikonja\LabbyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseCommand
 *
 * @package Velikonja\LabbyBundle\Command
 * @author  Matej Velikonja <matej@velikonja.si>
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    const ROLE_LOCAL  = 'local';
    const ROLE_REMOTE = 'remote';

    /**
     * Returns all roles.
     *
     * @return string[]
     */
    public static function all()
    {
        return array(
            self::ROLE_LOCAL,
            self::ROLE_REMOTE,
        );
    }

    /**
     * @var array
     */
    private $roles = array();

    /**
     * Sets roles that are allowed to execute command.
     *
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->checkRoles();

        return parent::run($input, $output);
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    private function checkRoles()
    {
        $appRoles = $this->getContainer()->getParameter('velikonja_labby.config.roles');

        foreach ($appRoles as $appRole) {
            if (in_array($appRole, $this->roles)) {
                return true;
            }
        }

        throw new \Exception(sprintf(
            'App role ( %s ) is not allowed. Allowed roles: ( %s ).',
            implode(', ', $appRoles),
            implode(', ', $this->roles)
        ));
    }
}
