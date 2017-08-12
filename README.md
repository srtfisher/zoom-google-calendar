# Zoom Google Calendar CLI Helper

Welcome to the Zoom Google Calendar CLI Helper!

This is a helpful CLI script that will pull in your calendar and open up the latest Zoom meeting URL. It will attempt to find events from the past ten minutes and the next hour (in case you're a little late or early). From there, it will find the Zoom URL and then open it (if you're on a Mac) in your browser.

### Getting Started

1. Duplicate the `.env.example` file to be `.env`. Update the file to include the proper `CALENDAR` the program should pull from. This is probably your work email address.

2. Run `composer install`.

3. Authenticate with Google by running `bin/zoom-google-calendar auth`. This will walk you through the authentication process.

4. Done!

### Opening the Zoom URL

Once you are within 10 minutes before or 60 minutes after a calendar event, you can run `bin/zoom-google-calendar open-calendar` to let the program pull in your events and connect you to a Zoom meeting. If you have multiple events that overlap within that timespan, it will let you select the event interactively.
