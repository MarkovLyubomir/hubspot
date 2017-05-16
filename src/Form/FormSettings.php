<?php

namespace Drupal\hubspot\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FormSettings.
 *
 * @package Drupal\hubspot\Form
 */
class FormSettings extends FormBase {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * FormSettings constructor.
   */
  public function __construct(Client $client, EntityTypeManager $entityTypeManager, ModuleHandler $moduleHandler, Connection $database) {
    $this->httpClient = $client;
    $this->entityTypeManager = $entityTypeManager;
    $this->moduleHandler = $moduleHandler;
    $this->database = $this->database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hubspot_form_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $form = [];

    $hubspot_forms = _hubspot_get_forms();

    if (isset($hubspot_forms['error'])) {
      $form['webforms']['#description'] = $hubspot_forms['error'];
    }
    else {
      if (empty($hubspot_forms['value'])) {
        $form['webforms']['#description'] = $this->t('No HubSpot forms found. You will need to create a form on HubSpot before you can configure it here.');
      }
      else {
        $hubspot_form_options = ["--donotmap--" => "Do Not Map"];
        $hubspot_field_options = [];
        foreach ($hubspot_forms['value'] as $hubspot_form) {
          $hubspot_form_options[$hubspot_form['guid']] = $hubspot_form['name'];
          $hubspot_field_options[$hubspot_form['guid']]['fields']['--donotmap--'] = "Do Not Map";

          foreach ($hubspot_form['fields'] as $hubspot_field) {
            $hubspot_field_options[$hubspot_form['guid']]['fields'][$hubspot_field['name']] = ($hubspot_field['label'] ? $hubspot_field['label'] : $hubspot_field['name']) . ' (' . $hubspot_field['fieldType'] . ')';
          }
        }

        $nid = $node;
        $form['nid'] = [
          '#type' => 'hidden',
          '#value' => $nid,
        ];

        $form['hubspot_form'] = [
          '#title' => $this->t('HubSpot form'),
          '#type' => 'select',
          '#options' => $hubspot_form_options,
          '#default_value' => _hubspot_default_value($nid),
        ];

        foreach ($hubspot_form_options as $key => $value) {
          if ($key != '--donotmap--') {
            $form[$key] = [
              '#title' => $this->t('Field mappings for @field', [
                '@field' => $value,
              ]),
              '#type' => 'details',
              '#tree' => TRUE,
              '#states' => [
                'visible' => [
                  ':input[name="hubspot_form"]' => [
                    'value' => $key,
                  ],
                ],
              ],
            ];

            $webform = $this->entityTypeManager->getStorage('webform')->load('test_1');
            $webform = $webform->getElementsDecoded();

            foreach ($webform as $form_key => $component) {
              if ($component['#type'] !== 'markup') {
                $form[$key][$form_key] = [
                  '#title' => $component['#title'] . ' (' . $component['#type'] . ')',
                  '#type' => 'select',
                  '#options' => $hubspot_field_options[$key]['fields'],
                  '#default_value' => _hubspot_default_value($nid, $key, $form_key),
                ];
              }
            }
          }
        }
      }
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => ('Save Configuration'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->database->delete('hubspot')->condition('id', $form_state->getValue(['nid']))->execute();

    if ($form_state->getValue(['hubspot_form']) != '--donotmap--') {
      foreach ($form_state->getValue([$form_state->getValue('hubspot_form')]) as $webform_field => $hubspot_field) {
        $fields = [
          'id' => $form_state->getValue(['nid']),
          'hubspot_guid' => $form_state->getValue(['hubspot_form']),
          'webform_field' => $webform_field,
          'hubspot_field' => $hubspot_field,
        ];
        $this->database->insert('hubspot')->fields($fields)->execute();
      }
    }
    drupal_set_message($this->t('The configuration options have been saved.'));
  }

}
