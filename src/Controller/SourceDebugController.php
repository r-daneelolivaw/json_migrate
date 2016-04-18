<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Controller\SourceDebugController.
 */
namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystem;
use Drupal\json_migrate\JSONReader;
use Drupal\json_migrate\Model\ContentType\ContentTypeMigration;
use Drupal\json_migrate\Model\Vocabulary\VocabularyMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContentTypeController.
 *
 * @package Drupal\json_migrate\Controller
 */
class SourceDebugController extends ControllerBase
{

  private $jsonReader;
  private $fileSystem;

  public function __construct(FileSystem $fileSystem,
                              JSONReader $jsonReader)
  {
    $this->fileSystem = $fileSystem;
    $this->jsonReader = $jsonReader;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('file_system'),
      $container->get('json_migrate.reader')
    );
  }

  /**
   * Reads a JSON source and prints entries via Kint.
   *
   * @return string
   */
  public function printDebug($type, $file_name, $length)
  {
    $path = '';
    // @todo improve via a factory / interface (avoid switch)
    switch($type){
      case 'content-type':
        $path = $this->fileSystem->realpath(ContentTypeMigration::JSON_PATH);
        break;
      case 'vocabulary':
        // @todo
        $path = $this->fileSystem->realpath(VocabularyMigration::JSON_PATH);
        break;
      default:
        drupal_set_message(t('Define a type of file: "content-type" or "vocabulary".'), 'error');
        break;
    }

    // @todo limit entries
    $json = $this->jsonReader->read($path, $file_name.'.txt');

    // @todo if module exists kint
    if(function_exists('kint')) {
      if(empty($json['errors'])){
        // limit debug entries amount
        $count = 0;
        $debug = [];
        // limit applies only to content type nodes
        // @todo terms views export provides a root node "terms"
        foreach($json['json'] as $entry) {
          if($count < (int) $length) {
            $debug[] = $entry;
          }else {
            break;
          }
          ++$count;
        }
        kint($debug);
      }else{
        drupal_set_message($this->t('Could not read the file.'), 'error');
        kint($json['errors']);
      }
    }else{
      drupal_set_message($this->t('Install the Kint module to view the results.'), 'error');
    }

    $build = array(
      '#type' => 'markup',
      // @todo format plural
      '#markup' => $this->t('Source debug info for the @name content type. Displaying @length entries.',
        array('@name' => $file_name, '@length' => $length)
      ),
    );
    return $build;
  }
}
