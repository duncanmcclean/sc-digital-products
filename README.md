# Digital Products
> Sell digital products with [Simple Commerce](https://github.com/doublethreedigital/simple-commerce)

This addon enables you to sell digital products with Simple Commerce, an e-commerce addon for Statamic.

This addon is free, however to use it in production, you'll need to purchase a Simple Commerce license from the [Statamic Marketplace](https://statamic.com/addons/double-three-digital/simple-commerce).

## Features
* Allows you to specifiy assets for the customer to download
* Generates license keys
* Emails customers with license keys and download links to purchased products

## Installation
1. Install with Composer - `composer require doublethreedigital/sc-digital-products`
2. Start using the addon!

## Usage
### Adding downloadable assets to products
Once installed, you'll see a `Digital Product` tab appear on the publish form for your product entries.

![Screenshot](https://raw.githubusercontent.com/doublethreedigital/sc-digital-products/master/publish-form.png)

In each of your digital products, you should enable the `Is Digital Product?` toggle and add the downloadable assets. These are the assets the customer will be able to access once they have purchased your product.

### Overriding the licence key generation logic
By default, we create a serial license key which you can give to your customers. However, you may want to customise where the code comes from or maybe you want to send it away to a third party service.

To do this, you can create your own license key repository which [implements the one provided by this addon](https://github.com/doublethreedigital/sc-digital-products/blob/master/src/Contracts/LicenseKeyRepository.php).

To register your repository, you'll need to bind it to our `LicenseKey` facade. You can do this in your `AppServiceProvider`.

```php
$this->app->bind('LicenseKey', App\Repositories\LicenseKeyRepository::class);
```

### Customising email views
If you wish to customise the email views, you can publish them with this command.

```
php artisan vendor:pulish --tag="sc-digital-products-views"
```

You'll then find the published views in your `resources/views/vendor/sc-digital-products` folder.

### License Verification
We've included a basic verification endpoint which you can use to check if a customer's license key is valid. Before you can use the endpoint, you'll need to [enable Statamic's REST API](https://statamic.dev/rest-api#enable-the-api).

Once enabled, you can simply make a POST request to `/api/sc-digital-downloads/verification` with a JSON body containing the license key you wish to verify.

```
{
    "license_key": "IpebSuXft9Koio5GgP7TSRdtl"
}
```

A valid response will look like this:

```
{
  "license_key": "IpebSuXft9Koio5GgP7TSRdtl",
  "valid": true
}
```

And an invalid one will be like this.

```
{
  "license_key": "IpebSuXft9Koio5GgP7TSRdtl",
  "valid": false
}
```

## Resources
* [Simple Commerce Documentation](https://doublethree.digital/simple-commerce/about)
* [Simple Commerce Issues](https://github.com/doublethreedigital/simple-commerce/issues)
* [Simple Commerce Discord](https://discord.gg/P3ACYf9)

<p>
<a href="https://statamic.com"><img src="https://img.shields.io/badge/Statamic-3.0+-FF269E?style=for-the-badge" alt="Compatible with Statamic v3"></a>
<a href="https://packagist.org/packages/doublethreedigital/sc-digital-products/stats"><img src="https://img.shields.io/packagist/v/doublethreedigital/sc-digital-products?style=for-the-badge" alt="Simple Commerce Digital Products on Packagist"></a>
</p>
