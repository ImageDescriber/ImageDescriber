<?php

namespace UserBundle\Command;

use FOS\OAuthServerBundle\Entity\ClientManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CreateClientCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('client:create')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new client for OAuth.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a new client for the OAuth authentication...')
            ->addArgument('url', InputArgument::REQUIRED, 'The url of the client.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Client Creator',
            '============',
        ]);

        /** @var $clientManager ClientManager */
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setAllowedGrantTypes(array('password', 'refresh_token'));
        $clientManager->updateClient($client);

        $output->writeln([
            '<info>It\'s done!',
            'Keep the following information carefully:',
            'Id: '.$client->getRandomId(),
            'Secret: '.$client->getSecret().'</info>'
        ]);
    }
}