<?php

namespace Dashifen\Theme2023\Services\Versioning;

use Dashifen\Composer\BumperException;

class Bumper extends \Dashifen\Composer\Bumper
{
  /**
   * getGitBranchObjectName
   *
   * We override the default branch object name with the one that we've
   * constructed that allows us to bump within the main branch if we want to.
   *
   * @return string
   */
  public function getGitBranchObjectName(): string
  {
    return Branch::class;
  }
  
  /**
   * calculateNextVersion
   *
   * Uses the name of the current branch to determine the next version number
   * after the current one.
   *
   * @return void
   * @throws BumperException
   */
  protected function calculateNextVersion(): void
  {
    parent::calculateNextVersion();
    
    // while we're building this theme, we might want to bump the version and
    // tags while simply messing around in the main branch.  if that's the
    // case, our parent's method will set the build for the next version to
    // something greater than zero.  so, if that's true and if our major
    // version number is zero, we just increment the minor version number and
    // reset the build.
    
    if ($this->next->getBuild() > 0 && $this->next->getMajor() === 0) {
      $this->next->setMinor($this->next->getMinor() + 1);
      $this->next->setBuild(0);
      
    }
  }
  
  
}