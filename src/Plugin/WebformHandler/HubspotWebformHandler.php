<?php

namespace Drupal\hubspot\Plugin\WebformHandler;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Url;
use Drupal\node\NodeStorageInterface;
use Drupal\webform\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform submission remote post handler.
 *
 * @WebformHandler(
 *   id = "hubspot_webform_handler",
 *   label = @Translation("HubSpot Webform Handler"),
 *   category = @Translation("External"),
 *   description = @Translation("Posts webform submissions to a Hubspot form."),
 *   cardinality = \Drupal\webform\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class HubspotWebformHandler extends WebformHandlerBase {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Stores the configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerInterface $logger, EntityTypeManagerInterface $entity_type_manager, ClientInterface $httpClient, NodeStorageInterface $node_storage, Connection $connection, ConfigFactoryInterface $config_factory, LoggerChannelFactory $loggerChannelFactory, MailManager $mailManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger, $entity_type_manager);
    $this->httpClient = $httpClient;
    $this->nodeStorage = $node_storage;
    $this->connection = $connection;
    $this->configFactory = $config_factory->getEditable('hubspot.settings');
    $this->loggerFactory = $loggerChannelFactory;
    $this->mailManager = $mailManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('webform.remote_post'),
      $container->get('entity_type.manager'),
      $container->get('http_client'),
      $container->get('entity.manager')->getStorage('node'),
      $container->get('database'),
      $container->get('config.factory'),
      $container->get('logger.factory'),
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    $operation = ($update) ? 'update' : 'insert';
    $this->remotePost($operation, $webform_submission);
  }

  /**
   * Execute a remote post.
   *
   * @param string $operation
   *   The type of webform submission operation to be posted. Can be 'insert',
   *   'update', or 'delete'.
   * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
   *   The webform submission to be posted.
   */
  protected function remotePost($operation, WebformSubmissionInterface $webform_submission) {
    $request_post_data = $this->getPostData($operation, $webform_submission);
    $entity_type = $request_post_data['entity_type'];
    if ($entity_type == 'node') {
      // Case 1: of node forms.
      $entity_id = $request_post_data['entity_id'];

      $node = $this->nodeStorage->load($entity_id);

      // Node form title of some webform type.
      $form_title = $node->getTitle();
      // Node id i.e entity id is mapped with hubspot form id in hubspot table.
      $id = $entity_id;

      $page_url = Url::fromUserInput($request_post_data['uri'], ['absolute' => TRUE])->toString();
    }
    else {
      // Case 2: Webform it self.
      // Webform id is mapped with hubspot form id in hubspot table.
      $id = $this->getWebform()->getOriginalId();

      // Webform title.
      $form_title = $this->getWebform()->get('title');
      $page_url = Url::fromUserInput('/form/' . $id, ['absolute' => TRUE])->toString();
    }
    $form_guid = $this->connection->select('hubspot', 'h')
      ->fields('h', ['hubspot_guid'])
      ->condition('id', $id)
      ->range(0, 1)
      ->execute()->fetchField();

    $portal_id = $this->configFactory->get('hubspot_portalid');

    $api = 'https://forms.hubspot.com/uploads/form/v2/' . $portal_id . '/' . $form_guid;

    $options = [
      'query' => $request_post_data,
    ];

    $url = Url::fromUri($api, $options)->toString();

    $cookie = \Drupal::request()->cookies->get('hubspotutk');

    try {

      $hs_context = [
        'hutk' => isset($cookie) ? $cookie : '',
        'ipAddress' => \Drupal::request()->getClientIp(),
        'pageName' => $form_title,
        'pageUrl' => $page_url,
      ];

      $fields = $request_post_data;
      $string = 'hs_context=' . Json::encode($hs_context) . '&' . Json::encode($fields);
      $request_options = [
        RequestOptions::HEADERS => ['Content-Type' => 'application/x-www-form-urlencoded'],
        RequestOptions::BODY => $string,
      ];
      $response = $this->httpClient->request('POST', $url, $request_options);

      // Debugging information.
      $hubspot_url = 'https://app.hubspot.com';
      $to = $this->configFactory->get('hubspot_debug_email');
      $default_language = \Drupal::languageManager()->getDefaultLanguage()->getId();
      $from = $this->configFactory->get('site_mail');
      $data = (string) $response->getBody();

      if ($response->getStatusCode() == '204') {
        $this->loggerFactory->get('hubspot')->notice('Webform "%form" results succesfully submitted to HubSpot. Response: @msg', [
          '@msg' => strip_tags($data),
          '%form' => $form_title,
        ]
        );
      }
      elseif (!empty($response['Error'])) {
        $this->loggerFactory->get('hubspot')->notice('HTTP error when submitting HubSpot data from Webform "%form": @error', [
          '@error' => $response['Error'],
          '%form' => $form_title,
        ]
        );

        if ($this->configFactory->get('hubspot_debug_on')) {
          $this->mailManager->mail('hubspot', 'http_error', $to, $default_language, [
            'errormsg' => $response['Error'],
            'hubspot_url' => $hubspot_url,
            'node_title' => $form_title,
          ], $from, TRUE);
        }
      }
      else {
        $this->loggerFactory->get('hubspot')->notice('HubSpot error when submitting Webform "%form": @error', [
          '@error' => $data,
          '%form' => $form_title,
        ]
        );

        if ($this->configFactory->get('hubspot_debug_on')) {
          $this->mailManager->mail('hubspot', 'hub_error', $to, $default_language, [
            'errormsg' => $data,
            'hubspot_url' => $hubspot_url,
            'node_title' => $form_title,
          ], $from);
        }
      }

    }
    catch (RequestException $e) {
      watchdog_exception('Hubspot', $e);
    }

  }

  /**
   * Get a webform submission's post data.
   *
   * @param string $operation
   *   The type of webform submission operation to be posted. Can be 'insert',
   *   'update', or 'delete'.
   * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
   *   The webform submission to be posted.
   *
   * @return array
   *   A webform submission converted to an associative array.
   */
  protected function getPostData($operation, WebformSubmissionInterface $webform_submission) {
    // Get submission and elements data.
    $data = $webform_submission->toArray(TRUE);

    // Flatten data.
    // Prioritizing elements before the submissions fields.
    $data = $data['data'] + $data;
    unset($data['data']);

    return $data;
  }

}
