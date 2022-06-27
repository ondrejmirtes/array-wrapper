# Array wrapper #

When the `array` structure needs to be more like an `object` for a while.

## Requirements ##

 - [PHP](https://php.net/) >= 8.1

## Installation ##

The best way to install [**zeleznypa/array-wrapper**](https://github.com/zeleznypa/array-wrapper) is using [Composer](https://getcomposer.org/):

```bash
composer require zeleznypa/array-wrapper
```

### Example: usage of ArrayWrapper ###

```php
$array = ['key' => 'value'];
$arrayWrapper = ArrayWrapper::create($array);

// Check array key exists
isset($arrayWrapper['key']);
isset($arrayWrapper->key);
$arrayWrapper->isKey();
$arrayWrapper->hasKey();
$arrayWrapper->offsetExists('key');

// Get array value by key
echo $arrayWrapper['key'];
echo $arrayWrapper->key;
echo $arrayWrapper->getKey();
echo $arrayWrapper->offsetGet('key');

// Set array value by key
$arrayWrapper['key'] = 'value';
$arrayWrapper->key = 'value';
$arrayWrapper->setKey('value');
$arrayWrapper->offsetSet('key', 'value');

// Unset array value by key
unset($arrayWrapper['key']);
unset($arrayWrapper->key);
$arrayWrapper->unsetKey();
$arrayWrapper->offsetUnset('key');
```