<?php
/**
 * Class file for Authenticate Console Command
 */

namespace Zoom_Calendar\Command;
use Zoom_Calendar\Client;
use Symfony\Component\Console\Command\Command as Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Console Command
 */
class Authenticate extends Command {
  protected function configure() {
    $this
      ->setName('auth')
      ->setDescription('Authenticate with Google.');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    // Initialize the client.
    $client = new Client;

    // Check if authenticated.
    if ($client->isAuthenticated()) {
      return $output->writeln('<error>Already authenticated.</error>');
    }

    // Log in with the client.
    $client->authenticate($output);
  }
}
