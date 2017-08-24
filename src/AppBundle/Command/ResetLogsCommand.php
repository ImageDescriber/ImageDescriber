<?php

namespace AppBundle\Command;

use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Entity\ClientManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UserBundle\Entity\Client;

class ResetLogsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('reset:logs')

            // the short description shown while running "php bin/console list"
            ->setDescription('Reset DB content')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Reset DB content')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '<info>Starting reset ...</info>',
        ]);

        /** @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        foreach($em->getRepository('AppBundle:Log')->findAll() as $entity) {
            $em->remove($entity);
        }
        $em->flush();

        $output->writeln([
            '<info>Done.</info>',
        ]);
    }
}