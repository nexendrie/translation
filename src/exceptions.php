<?php
namespace Nexendrie\Translation;

class InvalidStateException extends \RuntimeException {
  
}

class InvalidFolderException extends \RuntimeException {
  
}

class FolderNotSetException extends InvalidStateException {
  
}

class InvalidLocaleResolverException extends \RuntimeException {
  
}

class InvalidLoaderException extends \RuntimeException {
  
}

class LoaderNotSetException extends InvalidStateException {

}
?>