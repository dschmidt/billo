<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../config.php');

use Laminas\Mail\Storage;

function messageAddFlag($mailStorage, $id, $message, $flag) {
  $flags = $message->getFlags();
  if(in_array($flag, $flags)) {
    return;
  }
  $flags[] = Storage::FLAG_SEEN;
  $mailStorage->setFlags($id, $flags);
}

function usage() {
  return 'php src/billo.php --config listConfig.ini';
}

function distributeMessage($listsConfig, $mailSender, $id, $message) {
  // TODO: handle CC
  foreach($message->getHeader('to')->getAddressList() as $list) {
    if(!$listsConfig->hasList($list->getEmail())) {
      continue;
    }

    echo PHP_EOL . "Distribute message: " . $message->subject . " to " . $listsConfig->getListName($list->getEmail()) . " <" . $list->getEmail() . ">" . PHP_EOL ;

    // Set our mailing list as sender
    $mailSender->Sender = $list->getEmail();
    $mailSender->Subject =
      '['.$listsConfig->getListName($list->getEmail()).'] '.$message->subject;


    // Copy over content
    $content = $GLOBALS['mailStorage']->getRawContent($id);
    $mailSender->setBilloContent($content);
    $mailSender->ContentType = $message->ContentType;

    foreach($listsConfig->getMembers($list->getEmail()) as $toEmail => $toName) {
      // Add original from addresses: yes, there can be multiple.
      foreach($message->getHeader('from')->getAddressList() as $from) {
        $mailSender->setFrom($from->getEmail(), $from->getName());
      }

      try {
        $mailSender->addAddress($toEmail, $toName);
      } catch (Exception $e) {
        echo 'Invalid address skipped: ' . $toEmail . PHP_EOL;
        continue;
      }

      try {
        echo "Sending " .  $mailSender->Subject . " to ";
        foreach($mailSender->getToAddresses() as $to) {
          echo $to[1] . " <" . $to[0] . ">";
        }
        echo PHP_EOL;

        $mailSender->send();
      } catch (Exception $e) {
        echo 'Mailer Error (' . $toEmail . ') ' . $mailSender->ErrorInfo . PHP_EOL;
        // Reset the connection to abort sending this message
        // The loop will continue trying to send to the rest of the list
        $mailSender->getSMTPInstance()->reset();
      }

      $mailSender->clearAddresses();
    }
  }
}

function main() {
  $options = getopt('', ['config:']);

  $listsConfigFile = null;
  if (in_array('config', array_keys($options))) {
    $listsConfigFile = $options['config'];
  } else {
    echo usage();
    die(1);
  }

  $listsConfig = new ListsConfigParser($listsConfigFile);

  echo "Configured mailing lists" . PHP_EOL;
  foreach($listsConfig->getLists() as $list) {
    echo $listsConfig->getListName($list) . ": " . $list . PHP_EOL;
  }
  echo PHP_EOL . PHP_EOL;

  foreach ($GLOBALS['mailStorage'] as $id => $message) {
    if ($message->hasFlag(Storage::FLAG_SEEN)) {
      continue;
    } else {
      messageAddFlag($GLOBALS['mailStorage'], $id, $message, Storage::FLAG_SEEN);
    }

    distributeMessage($listsConfig, $GLOBALS['mailSender'], $id, $message);
  }
}

main();
