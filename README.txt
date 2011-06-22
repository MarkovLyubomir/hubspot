Requirements
------------

This module currently requires Drupal 6.x or 7.x, Webform 3.x, and a HubSpot
account with access to the HubSpot API. The cURL module for PHP must be
installed on your server.

Installation
------------

Upload and install the Webform module before this module. Install this module
as usual, then head to the HubSpot integration settings under Configuration
(admin/config/content/hubspot). Enter your HubSpot API key and the JavaScript
tracking code. The API key is required if you want to use the "latest leads"
dashboard block, but is not required for submitting leads from Webform.

You can request an API key from HubSpot through their website:

https://hubapi.com/keys/get

The JavaScript tracking code can be found on the HubSpot Settings page, under
External Site Traffic Logging. Copy and paste it directly into the
configuration page and it will automatically be inserted into your site.

If Webform submissions don't seem to be working, check the Drupal error log or
enable the debugging system in this module's configuration page. Any HubSpot
errors will then be emailed to you directly so you can diagnose the issue.

If you need to uninstall the module, disable it and uninstall it completely
through the Uninstall tab. When the module is disabled, the Webform module
won't know how to handle the HubSpot POST URL field and may throw some errors;
when you uninstall this module completely, it will automatically remove the
POST URL field to prevent any issues. Alternately, you can manually remove
the POST URL field from each affected Webform.

Further Information
-------------------

You can find more detailed help on how to use HubSpot with Webforms in this
module's Help page in the Drupal administrator interface. Head to Help and
select "HubSpot Drupal integration" for complete instructions.

Extended documentation, with instructions on using HubSpot's Salesforce
integration and details on automated receipt emails, is available in the Drupal
handbook pages:
  http://drupal.org/node/1195370

HubSpot API documentation is available at:
  http://docs.hubapi.com/wiki/Main_Page

Sponsorship
-----------

This project is sponsored by Digett, a Drupal-friendly San Antonio web design
company with a focus on inbound marketing. Need additional features or custom
styling? Contact us.
  http://www.digett.com/
