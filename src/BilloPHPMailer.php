<?php

use PHPMailer\PHPMailer\PHPMailer;

class BilloPHPMailer extends PHPMailer
{
  private $_billoContent;
  private $_billoHeader;

  public function __construct()
  {
    parent::__construct(true);
    $this->AllowEmpty = true;
  }

  public function setBilloHeader($billoHeader)
  {
    $this->_billoHeader = $billoHeader;
  }

  public function setBilloContent($billoContent)
  {
    $this->_billoContent = $billoContent;
  }

  public function createBody() {
    return $this->_billoContent;
  }

  public function getMAilMIME() {
    return $this->_billoHeader;
  }

}
