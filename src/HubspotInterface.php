<?php

namespace Drupal\hubspot;

/**
 * Provides an interface defining constants for hubspot.
 */
interface HubspotInterface {

  /**
   * Hubspot Client ID.
   *
   * @var string
   */
  const HUBSPOT_CLIENT_ID = '734f89bf-1b88-11e1-829a-3b413536dd4c';

  /**
   * Hubspot Scope.
   *
   * @var string
   */
  const HUBSPOT_SCOPE = 'leads-rw contacts-rw offline';

}
