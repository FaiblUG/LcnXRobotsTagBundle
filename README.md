LcnXRobotsTagBundle
==================

[![Build Status](https://travis-ci.org/FaiblUG/LcnXRobotsTagBundle.svg)](https://travis-ci.org/FaiblUG/LcnXRobotsTagBundle)

Easily manage [X-Robots-Tag http header](https://developers.google.com/webmasters/control-crawl-index/docs/robots_meta_tag) (noindex, nofollow) in Symfony2.
* lets you define a default value for the X-Robots-Tag response header
* lets you define the value for the X-Robots-Tag response header for requests that require certain user roles
* lets you manually control the value for the X-Robots-Tag response header


Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require locaine/lcn-x-robots-tag-bundle "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Lcn\XRobotsTagBundle\LcnXRobotsTagBundle(),
        );

        // ...
    }

    // ...
}
```


Step 3: Configure the Bundle
----------------------------
Then, configure the bundle in `app/config.yml` file of your project:

```yaml
# app/config.yml
parameters:
    lcn_x_robots_tag.rules.default: { noindex: false, nofollow: false }
    
...

lcn_x_robots_tag:
    enabled: true
    rules:
        default: %lcn_x_robots_tag.rules.default%
```

### Advanced configuration options

#### Explicitly control X-Robots-Tag header value in your controller

Imagine you have a product listing page where you want search engine crawlers to follow the links to the products but not to index the listing page itself (e.g. to avoid duplicate content):

```php
class ProductController

    public function indexAction(Request $request)
    {
        $this->get('lcn_x_robots_tag')->setNoindex(true)->setNofollow(false);
        
        ...
```

Calling `setNoindex` or `setNofollow` on the `XRobotsTag` service overrides all other rules defined in your `app/config.yml` file.


#### Do not index requests that require certain user roles

If you have user roles and access control rules defined in `app/security.yml` then you can easily tell search engine crawlers not to index those requests.
This is useful if your visitors "login" using a token (see [Api Key Authenticator](http://symfony.com/doc/current/cookbook/security/api_key_authentication.html#the-api-key-authenticator)) or when Http Basic Auth user credentials are provided in urls.
If you are sending 403/401 Status headers or if you are redirecting unauthenticated users to you login page, this might be less useful.

```yaml
# app/config.yml
lcn_x_robots_tag:
    enabled: true
    rules:
        user_roles: true
```

The above syntax is shorthand notation for:

```yaml
# app/config.yml
lcn_x_robots_tag:
    enabled: true
    rules:
        user_roles:
            *: { noindex: true, nofollow: true }
```

You can also apply the rules only for certain user roles:

```yaml
# app/config.yml
lcn_x_robots_tag:
    enabled: true
    rules:
        user_roles:
            ROLE_ADMIN: { noindex: true, nofollow: true }
            ROLE_EDITOR: { noindex: true, nofollow: true }
```



#### Do not index dev environment

Of course, your dev environment should not be publicly accessible, but if it is, you can at least avoid that it gets indexed:

```yaml
# app/config_dev.yml
imports:
    - { resource: config.yml }

parameters:
    lcn_x_robots_tag.rules.default: { noindex: true, nofollow: true }
```
