<?php
/**
 * Class file for Console Command
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
class Calendar extends Command {
  protected function configure() {
    $this
      ->setName('open-calendar')
      ->setDescription('Open the next Zoom event on your calendar.');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    // Initialize the client.
    $client = new Client;

    // Ensure they are authenticated.
    if (!$client->isAuthenticated()) {
      return $output->writeln('<error>Not authenticated.</error>');
    }

    $output->writeln('Pulling up events...');

    // Retrieve the events.
    $events = $client->getEvents();

    if (count($events) > 0) {
      $output->writeln('Multiple events found.' . PHP_EOL);

      $helper = $this->getHelper('question');
      foreach ($events as $event) {
        $question = new ConfirmationQuestion(sprintf('Open "%s"? ', trim($event->summary)), false);
        if ($helper->ask($input, $output, $question)) {
          return $client->openEvent($event);
        }
      }

      $output->writeln(PHP_EOL . 'No other events found.');
    } else {
      $output->writeln('Single event found! Opening...');
      $client->openEvent(array_shift($events));
    }
  }
}
