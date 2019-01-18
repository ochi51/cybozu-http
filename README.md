Cybozu HTTP client for PHP[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/ochi51/cybozu-http/tree/master/LICENSE)
=======================

[![Circle CI](https://circleci.com/gh/ochi51/cybozu-http/tree/master.svg?style=svg)](https://circleci.com/gh/ochi51/cybozu-http/tree/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ochi51/cybozu-http/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ochi51/cybozu-http/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ochi51/cybozu-http/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ochi51/cybozu-http/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ochi51/cybozu-http/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ochi51/cybozu-http/build-status/master)

Cybozu HTTP is a PHP HTTP client library for cybozu.com API.

cybozu.com API Documentation
------------

[Japanese](https://cybozudev.zendesk.com/hc/ja)
[English](https://developer.kintone.io/hc/en-us)

Available API
------------

- kintone API
- User API

If you want to use Garoon API, please send Pull Request.

Requirements
------------

- PHP >=7.1
- Composer
- To use the PHP stream handler, `allow_url_fopen` must be enabled in your system's php.ini.
- To use the cURL handler, you must have a recent version of cURL >= 7.19.4 compiled with OpenSSL and zlib.

Installation
------------

The recommended way to install Cybozu HTTP is with [Composer](https://getcomposer.org/).
Composer is a dependency management tool for PHP that allows you to declare the dependencies your project needs and installs them into your project.

```{.bash}
    $ curl -sS https://getcomposer.org/installer | php
    $ mv composer.phar /usr/local/bin/composer
```

You can add Cybozu HTTP as a dependency using the composer

```{.bash}
    $ composer require ochi51/cybozu-http
```

Alternatively, you can specify Cybozu HTTP as a dependency in your project's existing composer.json file:

```{.json}
    {
       "require": {
          "ochi51/cybozu-http": "^1.4"
       }
    }
```

After installing, you need to require Composer's autoloader:

```{.php}
    require 'vendor/autoload.php';
```

Quick start
------------

```{.php}
    $api = new \CybozuHttp\Api\KintoneApi(new \CybozuHttp\Client([
        'domain' => 'cybozu.com',
        'subdomain' => 'your-subdomain',
        'login' => 'your-login-name',
        'password' => 'your-password',
    ]));
    
    // get record that kintone app id is 100 and record id is 1.
    $record = $api->record()->get(100, 1);
```

Usage
------------

@todo

Testing
------------

To run the tests, you need to following process.

- Prepare your kintone account.
    - Free trial is [here](https://www.cybozu.com/jp/service/com/trial/?fcode=F00000081)
- Create kintone space template. (Enable multiple thread)
- Create graph.
- Edit `parameters.yml`.

Run the following command from the project folder.

```{.bash}
    $ php ./bin/phpunit
```

TODO
------------

- Japanese documentation.

License
------------

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
