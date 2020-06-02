<?php

use PHPMailer\PHPMailer\PHPMailer;

class BilloPHPMailer extends PHPMailer
{
  private $_billoContent;

  public function __construct()
  {
    parent::__construct(true);
    $this->AllowEmpty = true;
  }

  public function setBilloContent($billoContent)
  {
    $this->_billoContent = $billoContent;
  }

  public function createBody() {
    return $this->_billoContent;
  }
}
