<?php
namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\InvalidFolderException;

/**
 * ILoader
 *
 * @author Jakub Konečný
 */
interface ILoader {
  /**
   * @return string
   */
  function getLang();
  
  /**
   * @param string $lang
   */
  function setLang($lang);
  
  /**
   * @return string
   */
  function getDefaultLang();
  
  /**
   * @param string $defaultLang
   */
  function setDefaultLang($defaultLang);
  
  /**
   * @return string[]
   */
  function getFolders();
  
  /**
   * @param string[] $folders
   * @throws InvalidFolderException
   */
  function setFolders(array $folders);
  
  /**
   * @return array
   */
  function getResources();
  
  /**
   * @return array
   */
  function getTexts();
  
  /**
   * @return string
   */
  function getResolverName();
}
?>