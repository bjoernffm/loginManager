# LoginManager v0.7.1

## Why?
Because many password managing tools are unsafe! The project is related to a deep wish for security while managing passwords. LoginManager offers an own session management that stores the sessiondata in a secure way by encrypting it.

Your passwords are encrypted throught a wrapper that is using PHP Mcrypt functions. It's very secure and of course an alternative to all those tools like lastpass (i'm sure nobody knows really how the passwords are stored).

Sadly huge companies like Sony often save passwords in plain text till now - don't use those services if you don't have to.

## Installation
... is very simple! Just execute the install.php and you will be fine by just entering the mysql login data.

## Requirements
LoginManager depends on
* PHP 5.3 or higher
* PHP MySQLi extension
* PHP Mcrypt extension
* the new password_hash and password_verfy functions - see http://de2.php.net/manual/de/ref.password.php
