# Zoom Google Calendar CLI Helper

Welcome to the Zoom Google Calendar CLI Helper!

This is a helpful CLI script that will pull in your calendar and open up the latest Zoom meeting URL. It will attempt to find events from the past ten minutes and the next hour (in case you're a little late or early). From there, it will find the Zoom URL and then open it (if you're on a Mac) in your browser.

### Getting Started

1. Duplicate the `.env.example` file to be `.env`. Update the file to include the proper `CALENDAR` the program should pull from. This is probably your work email address.

2. Run `composer install`.

3. Create Google Developers Credentials (from Google Calendar Quickstart):

	1. Use [this wizard](https://console.developers.google.com/start/api?id=calendar) to create or select a project in the Google Developers Console and automatically turn on the API. Click Continue, then Go to credentials.
	2. On the Add credentials to your project page, click the Cancel button.
	3. At the top of the page, select the OAuth consent screen tab. Select an Email address, enter a Product name if not already set, and click the Save button.
	4. Select the Credentials tab, click the Create credentials button and select OAuth client ID.
	5. Select the application type Other, enter the name "Zoom Google Calendar", and click the Create button.
	6. Click OK to dismiss the resulting dialog.
	7. Click the file_download (Download JSON) button to the right of the client ID.
	8. Move this JSON file to `.credentials/client_secret.json` inside of your working directory.

4. Authenticate with Google by running `bin/zoom-google-calendar auth`. This will walk you through the authentication process.

4. Done!

### Opening the Zoom URL

Once you are within 10 minutes before or 60 minutes after a calendar event, you can run `bin/zoom-google-calendar open-calendar` to let the program pull in your events and connect you to a Zoom meeting. If you have multiple events that overlap within that timespan, it will let you select the event interactively.

### Alias

To make this easier for you, you can add an alias to your bash initialization script to make this even better.

```bash
ALIAS zgc='~/path/to/zoom-google-calendar/bin/zoom-google-calendar open-calendar'
```
