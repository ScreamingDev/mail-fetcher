# Mail Fetcher

> Fetching and showing outgoing mails

This shall be the most simple way for PHP to fetch
and show outgoing mails.

## Usage

1. Install package
   `composer require --dev screamingdev/mail-fetcher` .
2. Adapt PHP config
   `php -d sendmail_path="vendor/rmp-up/mail-fetcher/sendmail.php" ...` .
3. Read outgoing mails using the GUI
   `ln -sr vendor/screamingdev/mail-fetcher/mails.php document-root/mails.php`

Mails will now be stored in "vendor/screamingdev/mail-fetcher/var"
so by removing the package all mails will be gone too.
To have it more persistent or share mails among multiple projects
you could replace this dir by a symlink.

Watch out: This is very simple and basic PHP.
No OOP or fancy stuff needed here.
This is for devs only and won't become the next big mail client :P