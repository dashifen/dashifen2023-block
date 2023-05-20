<?php

namespace Dashifen;

use Dashifen\Composer\BumperException;
use Dashifen\Theme2023\Services\Versioning\Bumper;

if (PHP_SAPI === 'cli') {
  if (!class_exists(Bumper::class)) {
    require 'vendor/autoload.php';
  }
  
  try {
    $bumper = new Bumper();
    $runSimulation = Bumper::flagExists('simulate');
    
    // if we are running a simulation, then we don't want to commit.  but, if
    // the no-commit flag exists, we also don't want to commit.  the following
    // assignment checks those conditions, and we can pass its results over to
    // the bump method of our Bumper object.  notice that this means the
    // default behavior is to update the local repo after changing the
    // files.
    
    $doCommit = !$runSimulation && !Bumper::flagExists('no-commit');
    $bumper->bump($runSimulation, $doCommit);
  } catch (BumperException $e) {
    echo 'Cannot bump because ' . lcfirst($e->getMessage());
  }
}