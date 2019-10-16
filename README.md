NadiaFormBundle
===============

A bundle that collects useful Symfony FormTypes.


## Installation and configuration

#### Install with [Composer](https://getcomposer.org/), run

```bash
$ composer require nadialabs/nadia-form-bundle
```

#### Add NadiaFormBundle to your application kernel

```php
// app/AppKernel.php
public function registerBundles()
{
    return [
        // ...
        new Nadia\Bundle\NadiaFormBundle\NadiaFormBundle(),
        // ...
    ];
}
```

## Form Types

- [DynamicChoiceType](./Resources/doc/dynamic-choice-type.md)
- [DynamicEntityType](./Resources/doc/dynamic-entity-type.md)
- ToggleType (for Bootstrap4)
- ImageFileType
