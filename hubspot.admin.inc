<?php
/**
 * @file
 * Provides admin settings page to adjust HubSpot API key.
 */

function hubspot_admin_settings() {
  $form = array();

  $form['hapi'] = array(
    '#title' => t('HubSpot API Settings'),
    '#type' => 'fieldset'
  );

  $form['hapi']['hubspot_apikey'] = array(
    '#title' => t('HubSpot API Key'),
    '#type' => 'textfield',
    '#default_value' => variable_get('hubspot_apikey', ''),
    '#description' => t('This can be requested from HubSpot and is required for integration to function.')
  );
  
  $form['hapi']['hubspot_log_code'] = array(
    '#title' => t('HubSpot Logging Code'),
    '#type' => 'textarea',
    '#default_value' => variable_get('hubspot_log_code', ''),
    '#description' => t('To enable HubSpot traffic logging on your site, paste the HubSpot logging code here.')
  );

  return system_settings_form($form);
}
