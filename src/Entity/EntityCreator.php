<?php
namespace Drupal\json_migrate\Entity;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Helpers that creates entities.
 *
 * Usage
 * $ec = new EntityCreator();
 * $term = $ec->createTerm('tags', 'test tag', 'test desc');
 * $file = $ec->createFileFromURI('public://logo.png');
 * $node = $ec->createNodeWithImage(array(), $file);
 */
class EntityCreator
{
  /**
   * Creates a term.
   * @param $vocabulary
   * @param $name
   * @param $description
   * @param string $language
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  public function createTerm($vocabulary, $name, $description, $language = 'en')
  {
    $term = Term::create([
      'vid' => $vocabulary,
      'langcode' => $language,
      'name' => $name,
      'description' => [
        'value' => '<p>'.$description.'</p>',
        'format' => 'full_html',
      ],
      'weight' => 0,
      //'parent' => array (0),
    ]);
    $term->save();
    //\Drupal::service('path.alias_storage')->save("/taxonomy/term/" . $term->id(), "/tags/my-tag", "en");
    return $term;
  }

  /**
   * Creates a term and its translations.
   * @param $vocabulary
   * @param $translations
   * @param string $source
   * @return array
   */
  public function createTranslatedTerm($vocabulary, $translations, $source = 'en')
  {
    $terms = array();
    foreach($translations as $language => $translation) {
      // @todo implement
    }
    return $terms;
  }

  /**
   * Creates a file from a URI
   * @param $uri
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  public function createFileFromURI($uri)
  {
    $file = File::create([
      'uid' => 1,
      'uri' => $uri,
      'status' => 1,
    ]);
    $file->save();
    return $file;
  }

  /**
   * Creates a node.
   * @param $properties
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  public function createNode($properties)
  {
    $node = Node::create([
      'type' => 'article',
      'langcode' => 'en',
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
      'uid' => 1,
      'title' => 'My title',
      //'field_tags' =>[2],
      'body' => [
        'summary' => '',
        'value' => 'My node',
        'format' => 'full_html',
      ],
    ]);
    $node->save();
    return $node;
  }

  /**
   * Creates a node and attach images.
   * @param $properties
   * @param $files
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  public function createNodeWithImages($properties, $files) {

    $nodeProperties = [
      'type' => 'article',
      'langcode' => 'en',
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
      'uid' => 1,
      'title' => 'My node with images',
      'field_tags' => [1,2,3],
      'body' => [
        'summary' => '',
        'value' => 'My node',
        'format' => 'full_html',
      ],
    ];

    foreach($files as $file) {
      $nodeProperties['field_image'][] = [
        'target_id' => $file->id(),
        'alt' => "alt",
      ];
    }

    $node = Node::create($nodeProperties);
    $node->save();
    // \Drupal::service('path.alias_storage')->save('/node/' . $node->id(), '/my-path', 'en');
    return $node;
  }

  /**
   * Creates a node with image field.
   */
  public function createNodeWithImage($properties, File $file)
  {
    $node = Node::create([
      'type' => 'article',
      'langcode' => 'en',
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
      'uid' => 1,
      'title' => 'My node with images',
      //'field_tags' =>[2],
      'body' => [
        'summary' => '',
        'value' => 'My node',
        'format' => 'full_html',
      ],
      'field_image' => [
        [
          'target_id' => $file->id(),
          'alt' => "alt",
        ],
      ],
    ]);
    $node->save();
    // \Drupal::service('path.alias_storage')->save('/node/' . $node->id(), '/my-path', 'en');
    return $node;
  }
}