# EditorJS Data Converter

A package to convert JSON data back to HTML that Editor.js generates.

## Installation
You can install this package by running the following command in your project:
```bash
composer require motivo/editorjs-data-converter
```

incase you want to extend the base functionality you should run the following command:
```bash
php artisan vendor:publish --provider="\Motivo\EditorJsDataConverter\EditorJsServiceProvider"
```
If you don't specify the provider, you should publish the following tag: `editorjs-config`.

## How to use it

To convert the json string so it will return HTML you will need to do the following
```php
resolve(DataConverter::class)->init(json_decode($jsonContent))
``` 

## How to extend the usage of the package.

You should make a folder in your app directory named "Converters".
In that folder, you can add a class that implements the `Converter` interface.

The interface expects a `toHtml` function.

Here is a small example converter.

```php
<?php

namespace App\Converters;

use Illuminate\Support\Arr;
use Motivo\EditorJsDataConverter\Converters\Contracts\Converter;
use Motivo\EditorJsDataConverter\Traits\WithHtml;

class ParagraphConverter implements Converter
{
    use WithHtml;

    public function toHtml(array $itemData): string
    {
        return $this->html
            ->element('p')
            ->addClass('h3')
            ->html(Arr::get($itemData, 'text', ''));
    }
}
```

The above converter will overwrite the default `ParagraphConverter`.
If you use custom frontend plugins for Editor.js please make sure that the type of the plugin is also saved.

This is needed because we use the following naming conventions: `Type`Converter, to figure out what converters we need to use.


## Testing

```bash
make test
```

## License

The Mozilla Public License Version 2.0 (mpl-2.0). Please see [License File](LICENSE) for more information.
