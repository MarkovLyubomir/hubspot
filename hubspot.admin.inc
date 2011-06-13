<?php
function hubspot_admin_settings()
{
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
    
    return system_settings_form($form);
}
?>
