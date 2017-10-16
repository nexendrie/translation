<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert,
    Nette\Http\Request,
    Nette\Http\UrlScript,
    Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Translation\LoaderNotSetException;

require __DIR__ . "/../../../bootstrap.php";

final class HeaderLocaleResolverTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  protected function prepareRequest(string $language): Request {
    $headers = [
      "Accept-Language" => $language
    ];
    return new Request(new UrlScript, NULL, NULL, NULL, NULL, $headers);
  }
  
  public function testResolve() {
    $resolver = new HeaderLocaleResolver();
    Assert::exception(function() use($resolver) {
      $resolver->resolve();
    }, LoaderNotSetException::class);
    /** @var ILoader $loader */
    $loader = $this->getService(ILoader::class);
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