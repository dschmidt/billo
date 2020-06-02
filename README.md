# Billo Mailing Lists

This is a simple project for hosting static mailing lists on regular webhosting accounts.

It works by fetching unread messages from a Mailbox and resending to users on mailing lists matching the `to` header.

Mailing lists are configured in a config file that looks like this:

```ini
[test-list@domain.tld = Some List]
user@some-domain.tld = "Foo"
someone@other-domain.de = "Bar"

[another-list@domain.tld = Another List]
user@some-domain.tld = "Foo"
whoever@other-domain.de = "Bar"
```

This defines two mailing lists with two members each.
The mailing list addresses need to be configured to end up in the same mailbox.

## Retrieving and sending emails

Mail servers need to be configured in `config.php` in the project root folder:

```php
<?php
use Laminas\Mail\Storage\Imap;
use PHPMailer\PHPMailer\SMTP;

$GLOBALS['mailStorage'] = new Imap([
  'host'     => '...',
  'user'     => '...',
  'password' => '...',
]);

$GLOBALS['mailSender'] = new BilloPHPMailer();
// $mailSender->SMTPDebug = SMTP::DEBUG_SERVER;                   // Enable verbose debug output
$mailSender->isSMTP();                                         // Send using SMTP
$mailSender->Host       = '....';                              // Set the SMTP server to send through
$mailSender->SMTPAuth   = true;                                // Enable SMTP authentication
$mailSender->Username   = '...';                               // SMTP username
$mailSender->Password   = '...';                               // SMTP password
$mailSender->SMTPSecure = BilloPHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
$mailSender->Port       = 587;                                 // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

```

For retrieving mails from the mailbox you need to provide a `Laminas Mail` Storage implementation, e.g. Imap.
For sending mails you need to provide an instance of the Billo `PHPMailer` subclass. You can configure it just like the upstream version.

# Installation

Apart from creating the config files, you need to checkout deps with `composer install`.

Create a cronjob that fetches and sends mails as often as you want. Be careful not to start two parallel runs.

## TODO

1. Better error handling and proper error messages
2. Throttling
3. Lock file for avoiding race conditions in parallel runs
4. Improve documentation
5. Improve usage() message
6. Set useful mailing list headers
7. Set return-path

## Name

`Billo` is a German slang word for `billig` and can probably be translated as `cheap` or `shoddy`. It seems to fit the kinda hack-ish approach of this project fairly well :)
