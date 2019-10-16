DynamicEntityType
=================

## Configuration

### Add DynamicEntityType form theme to Twig Configuration

```yaml
# Twig Configuration
twig:
    form_themes:
        # Add DynamicEntityType form theme (The same as DynamicEntityType)
        - '@NadiaForm/Form/dynamic_choice.html.twig'
```

### Add Javascript file to your Twig template

The Javascript file for DynamicEntityType is locate in `Resources/assets/js/form/dynamic-choice-type.js`.  
You can choose one of the methods below to import this Javascript file

#### Method#1 Using Encore or Webpack

```js
// Add this line to your Javascript file
// you can change "/path/to/your/vendor/nadialabs/nadia-form-bundle" for your need
import '/path/to/your/vendor/nadialabs/nadia-form-bundle/Resources/assets/js/form/dynamic-choice-type.js';
```

#### Method#2 Using `<script>` tag

```twig
{# Add this line to your Twig template file, you can change asset file path for your need #}
<script type="text/javascript" src="{{ asset('bundles/nadiaform/dist/dynamic-choice-type.js') }}"></script>
```

### Field Options

##### Required options

- `class`: The class of your entity (e.g. `Category::class`, `App\Entity\Category`). This should be a fully-qualified class name (e.g. App\Entity\Category).

##### Optional options
  
- `em`: If specified, this entity manager will be used to load the choices instead of the default entity manager.

##### Inherit options

See [DynamicChoiceType](./dynamic-choice-type.md)


## Example Usage

```php
// src/AppBundle/Controller/DynamicChoiceController.php
namespace AppBundle\Controller;

use App\Entity\Category;
use Nadia\Bundle\NadiaFormBundle\Form\Type\DynamicChoiceType;
use Nadia\Bundle\NadiaFormBundle\Form\Type\DynamicEntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DynamicEntityController extends Controller
{
    /**
     * A demo page with DynamicChoiceType and DynamicEntityType fields
     *
     * @Route("/dynamic-entity", name="demo-dynamic-entity")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $form = $this->createFormBuilder()
            ->add('choice', DynamicChoiceType::class, [
                'ajax_uri' => $this->generateUrl('demo-dynamic-choice-ajax1'),
                'target' => '#form_entity',
                'choices' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'foobar' => 'foobar',
                ],
            ])
            ->add('entity', DynamicEntityType::class, [
                'class' => Category::class,
            ])
            ->getForm()
        ;

        return $this->render('@App/DynamicEntity/index.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * An AJAX response that contains <option> tags
     *
     * @Route("/dynamic-choice/ajax1", name="demo-dynamic-choice-ajax1")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajax1()
    {
        // You can also retrieve dropdown list from database
        $choices = [
            'Develop' => 1,
            'PHP' => 2,
            'NadiaFormBundle' => 3,
        ];
        $html = '';
        foreach ($choices as $title => $value) {
            $html .= sprintf('<option value="%s">%s</option>', $value, $title);
        }

        return new Response($html);
    }
}
```

```twig
{# src/AppBundle/Resources/views/DynamicEntity/index.html.twig #}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>DynamicChoiceType Demo</title>
</head>
<body>
{{ form(form) }}

<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
<!-- Simplest way to include dynamic-choice-type.js -->
<script src="{{ asset('bundles/nadiaform/dist/dynamic-choice-type.js') }}"></script>
<!-- If you use Encore or other tools (e.g. webpack),
     import Resources/assets/js/form/dynamic-choice-type.js to your app.js -->
</body>
</html>
```
