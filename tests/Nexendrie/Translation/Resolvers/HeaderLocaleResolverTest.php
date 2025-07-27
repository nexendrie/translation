<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nexendrie\Translation\Loader;
use Nexendrie\Translation\LoaderNotSetException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class HeaderLocaleResolverTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  protected function prepareRequest(string $language): Request {
    $headers = [
      "Accept-Language" => $language
    ];
    return new Request(new UrlScript(), null, null, null, $headers);
  }
  
  public function testResolve(): void {
    $resolver = new HeaderLocaleResolver();
    Assert::exception(function() use($resolver) {
      $resolver->resolve();
    }, LoaderNotSetException::class);
    /** @var Loader $loader */
    $loader = $this->getService(Loader::class);
    $resolver->setLoader($loader);
    Assert::null($resolver->resolve());
    $resolver = new HeaderLocaleResolver($this->prepareRequest("zh"));
    $resolver->setLoader($loader);
    Assert::null($resolver->resolve());
    $resolver = new HeaderLocaleResolver($this->prepareRequest("en"));
    $resolver->setLoader($loader);
    Assert::same("en", $resolver->resolve());
  }
}

$test = new HeaderLocaleResolverTest();
$test->run();
?>