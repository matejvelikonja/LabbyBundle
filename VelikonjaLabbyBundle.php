<?php

namespace Velikonja\LabbyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Velikonja\LabbyBundle\DependencyInjection\Compiler\ExecutorCompilerPass;

class VelikonjaLabbyBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(
            new ExecutorCompilerPass()
        );
    }
}
