<?php

class ListsConfigParser
{
  private $_config;

  public function __construct($listsConfigFilePath) {
    $config = parse_ini_file($listsConfigFilePath, true);
    $this->_config = [];
    foreach($config as $key => $value) {
      $tmp = explode('=', $key);

      $this->_config[trim($tmp[0])] = [
        'name' => trim($tmp[1]),
        'members' => $config[$key]
      ];
    }
  }

  public function hasList($list) {
    return in_array($list, array_keys($this->_config));
  }

  public function getListName($list) {
    return $this->_config[$list]['name'];
  }

  public function getLists() {
    return array_keys($this->_config);
  }
  
  public function isMember($listAdresses, $senderAddress)
  {
    return in_array($listAdresses, $senderAddress)
  }

  public function getMembers($listAddress)
  {
    if (!in_array($listAddress, $this->getLists())) {
      return [];
    }

    return $this->_config[$listAddress]['members'];
  }
}
