<?php

namespace UserBundle\Command;

use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Entity\ClientManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UserBundle\Entity\Client;

class GetClientCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('client:get')

            // the short description shown while running "php bin/console list"
            ->setDescription('List of the OAuth clients.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command lists the OAuth clients.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '<info>List of the clients :',
        ]);

        /** @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        foreach($em->getRepository('UserBundle:Client')->findAll() as $client) {
            /** @var $client Client */
            $output->writeln([
                'Id: '.$client->getId().' - Secret : '.$client->getSecret().' - RandomId : '.$client->getRandomId().' - PublicId : '.$client->getPublicId().' - GrandType : '.implode(', ', $client->getAllowedGrantTypes())

            ]);
        }

        $output->writeln([
            '</info>'
        ]);
    }
}