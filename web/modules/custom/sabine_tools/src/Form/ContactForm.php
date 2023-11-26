<?php

namespace Drupal\sabine_tools\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a contact form.
 */
class ContactForm extends FormBase {

  /**
   * Drupal\Core\Mail\MailManagerInterface definition.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $pluginManagerMail;

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Drupal\Core\Logger\LoggerChannelInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $loggerChannelDefault;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->pluginManagerMail = $container->get('plugin.manager.mail');
    $instance->loggerFactory = $container->get('logger.factory');
    $instance->loggerChannelDefault = $container->get('logger.channel.default');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contact_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Naam'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
      '#required' => TRUE,
      '#prefix' => '<div class="col-12">',
      '#suffix' => '</div>',
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mailadres'),
      '#weight' => '0',
      '#required' => TRUE,
      '#prefix' => '<div class="col-6 col-12-mobile">',
      '#suffix' => '</div>',
    ];
    $form['telephone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Telefoonnummer'),
      '#weight' => '0',
      '#required' => TRUE,
      '#prefix' => '<div class="col-6 col-12-mobile">',
      '#suffix' => '</div>',
    ];
    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Onderwerp'),
      '#weight' => '0',
      '#required' => TRUE,
      '#prefix' => '<div class="col-12">',
      '#suffix' => '</div>',
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Vraag of opmerking'),
      '#weight' => '0',
      '#required' => TRUE,
      '#prefix' => '<div class="col-12">',
      '#suffix' => '</div>',
    ];
    $form['tools'] = [
      '#type' => 'textfield',
      '#title' => t('Noem drie letters uit de naam van de site'),
      '#size' => 24,
      '#description' => t('Ik stel deze vraag om te controleren dat dit formulier niet door een robot wordt ingevuld'),
      '#prefix' => '<div class="col-12">',
      '#suffix' => '</div>',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Versturen'),
      '#prefix' => '<div class="col-12 centered">',
      '#suffix' => '</div>',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $tools_string = strtolower($form_state->getValue('tools'));
    $letters = str_split($tools_string);
    $sabine_letters = str_split('sabine.nl');
    $result = array_intersect($letters, $sabine_letters);
    $result = count($result);
    if ($result < 3) {
      $form_state->setError($form, t('Het antwoord is niet juist. Om te controleren of u geen spamrobot bent, vraag ik om 3 letters uit de naam van de site in te voeren. Als het niet lukt, stuur dan een email naar bdoesborg@gamil.com'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $telephone = $form_state->getValue('telephone');
    $name = $form_state->getValue('name');
    $message = $form_state->getValue('message');
    $subject = $form_state->getValue('subject');
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'sabine_tools';
    $key = 'sabine_tools_mail';
    $to = "bdoesborg@gmail.com";
    $params['message'] = "Onderwerp: $subject \n\nNaam: $name \n\nTelefoon: $telephone\n\nBericht: $message";
    $params['subject'] = $subject;
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = TRUE;
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== TRUE) {
      $this->messenger()
        ->addError(t('There was a problem sending your message and it was not sent.'));
    }
    else {
      $this->messenger()->addStatus(t('Your message has been sent.'));
    }
  }

}
