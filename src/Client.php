<?php
/**
 * Class file for Zoom Calendar.
 */

namespace Zoom_Calendar;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar_Event;

class Client {
  /**
   * Google Client instance.
   */
  protected static $client;

  /**
   * Constructor
   */
  public function __construct() {
    $this->get();
  }

  /**
   * Retrieve the Google Client
   *
   * @return Google_Client
   */
  public function get() {
    if (!empty(static::$client)) {
      return static::$client;
    }

    static::$client = new Google_Client();
    static::$client->setApplicationName(APPLICATION_NAME);
    static::$client->setScopes(SCOPES);
    static::$client->setAuthConfig(CLIENT_SECRET_PATH);
    static::$client->setAccessType('offline');

    if ($this->isAuthenticated()) {
      $accessToken = json_decode(file_get_contents(CREDENTIALS_PATH), true);
      static::$client->setAccessToken($accessToken);

      // Refresh the token if it's expired.
      if (static::$client->isAccessTokenExpired()) {
        static::$client->fetchAccessTokenWithRefreshToken(static::$client->getRefreshToken());
        file_put_contents(CREDENTIALS_PATH, json_encode(static::$client->getAccessToken()));
      }
    }
  }

  /**
   * Determine if we are authenticated.
   *
   * @return bool
   */
  public function isAuthenticated() : bool {
    return file_exists(CREDENTIALS_PATH);
  }

  /**
   * Authenticate with Google
   *
   * @param OutputInterface $output Output interface.
   * @return Google_Client
   */
  public function authenticate(OutputInterface $output) {
    // Request authorization from the user.
    $authUrl = static::$client->createAuthUrl();

    // Open the URL in the browser.
    $this->openUrl($authUrl);
    $output->writeln(sprintf("Open the following link (if it did not open already) in your browser:\n%s\n", $authUrl));

    // Prompt for a verification code.
    $output->writeln('Enter verification code: ');
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = static::$client->fetchAccessTokenWithAuthCode($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname(CREDENTIALS_PATH))) {
      mkdir(dirname(CREDENTIALS_PATH), 0700, true);
    }

    file_put_contents(CREDENTIALS_PATH, json_encode($accessToken));
    $output->writeln(sprintf("Credentials saved to %s\n", $credentialsPath));

    // Store the access token to the client.
    static::$client->setAccessToken($accessToken);

    return static::$client;
  }

  /**
   * Get events
   *
   * @return Google_Service_Calendar_Events
   */
  public function getEvents() {
    $calendar_client = new \Google_Service_Calendar(static::$client);
    $events = $calendar_client->events;

    // Start/end range.
    $start_range = Carbon::now($_ENV['TIMEZONE'])->subMinutes(10);
    $end_range = Carbon::now($_ENV['TIMEZONE'])->addMinutes(60);

    return $calendar_client->events->listEvents($_ENV['CALENDAR'], [
      'timeMin' => $start_range->format(\DateTime::RFC3339),
      'timeMax' => $end_range->format(\DateTime::RFC3339)
    ]);
  }

  /**
   * Open a URL in the browser
   * Attempt to open the URL in the browser using a Apple CLI command.
   *
   * @param string $url URL to open.
   */
  protected function openUrl(string $url) {
    exec(sprintf('open "%s"', $url));
  }

  /**
   * Open an event
   */
  public function openEvent(Google_Service_Calendar_Event $event) {
    $texts = [];

    if (!empty($event->getLocation())) {
      $texts[] = $event->getLocation();
    }

    if (! empty($event->getDescription())) {
      $texts[] = $event->getDescription();
    }

    foreach ($texts as $text) {
      $zoom_url = $this->getZoomUrls($text);
      if (!empty($zoom_url)) {
        // Attempt to open the URL in the browser using a Apple CLI command.
        return $this->openUrl($zoom_url);
      }
    }
  }

  /**
   * Extract a Zoom URL from text.
   *
   * @param string $text Text to extract from.
   * @return string Zoom URL or empty string.
   */
  protected function getZoomUrls(string $text) : string {
    preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $text, $matches);

    if (empty($matches[0])) {
      return '';
    }

    foreach ($matches[0] as $match) {
      $host = parse_url($match, PHP_URL_HOST);
      if (!empty($host) && false !== strpos($host, 'zoom.us')) {
        return $match;
      }
    }

    return '';
  }
}
