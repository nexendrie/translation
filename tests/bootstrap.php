<?php
require __DIR__ . "/../vendor/autoload.php";
Testbench\Bootstrap::setup(__DIR__ . '/_temp', function (\Nette\Configurator $configurator) {
  $configurator->addParameters([
    "appDir" => __DIR__,
    "tempDir" => __DIR__ . "/_temp",
    "debugMode" => true,
  ]);
  $configurator->addConfig(__DIR__ . "/tests.neon");
});
?>