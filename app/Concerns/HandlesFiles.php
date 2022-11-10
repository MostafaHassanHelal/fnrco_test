<?php

namespace App\Concerns;

trait HandlesFiles
{
  private function getDefaultUrl()
  {
    $disk = config("filesystems.default");
    $url = config("filesystems.disks." . $disk)['url'] . "/";
    return $url;
  }
}
