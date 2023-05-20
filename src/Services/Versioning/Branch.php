<?php

namespace Dashifen\Theme2023\Services\Versioning;

class Branch extends \Dashifen\Git\Branch
{
  /**
   * isTypeUnknown
   *
   * Returns true if the type is "?" (i.e. it's not one of the known types)
   * and if the branch is not named "main."
   *
   * @return bool
   */
  public function isTypeUnknown(): bool
  {
    return parent::isTypeUnknown() && $this->description !== 'main';
  }
}