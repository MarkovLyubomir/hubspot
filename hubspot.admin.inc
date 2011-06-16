<?php
/**
 * @file
 * Provides admin settings page to adjust HubSpot API key, debugging settings,
 * and JavaScript embedding.
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
  
  $form['debug'] = array(
    '#title' => t('HubSpot Debugging Settings'),
    '#type' => 'fieldset'
  );
  
  $form['debug']['hubspot_debug_on'] = array(
    '#title' => t('Debugging enabled'),
    '#type' => 'checkbox',
    '#default_value' => variable_get('hubspot_debug_on', 0),
    '#description' => t('If debugging is enabled, HubSpot errors will be emailed to the address below. Otherwise, they will be logged to the regular Drupal error log.')
  );
  
  $form['debug']['hubspot_debug_email'] = array(
    '#title' => t('Debugging email'),
    '#type' => 'textfield',
    '#default_value' => variable_get('hubspot_debug_email', variable_get('site_mail', '')),
    '#description' => t('Email error reports to this address if debugging is enabled')
  );
  
  return system_settings_form($form);
}

/**
 * Validation for hubspot_admin_settings form. Ensure that the debug email
 * address provided is valid if debugging is to be enabled.
 */
function hubspot_admin_settings_validate($form, &$form_state) {
  if ($form_state['values']['hubspot_debug_on'] &&
      !valid_email_address($form_state['values']['hubspot_debug_email'])) {
    form_set_error('hubspot_debug_email', t('You must provide a valid email address.'));
  }
}
