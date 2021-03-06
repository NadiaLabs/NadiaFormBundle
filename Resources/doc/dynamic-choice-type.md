DynamicChoiceType
=================

## Configuration

### Add DynamicChoiceType form theme to Twig Configuration

```yaml
# Twig Configuration
twig:
    form_themes:
        # Add DynamicChoiceType form theme
        - '@NadiaForm/Form/dynamic_choice.html.twig'
```

### Add Javascript file to your Twig template

The Javascript file for DynamicChoiceType is locate in `Resources/assets/js/form/dynamic-choice-type.js`.  
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

##### Optional options

- `ajax_uri`: The AJAX uri for fetching new HTML for the target DynamicChoiceType widget. (default value is empty string `''`)  
  If you don't need to update target DynamicChoiceType widget, keep it as default.  
- `target`: The target DynamicChoiceType widget CSS selector. (default value is empty string `''`)  
  If you don't need to update target DynamicChoiceType widget, keep it as default.  
  For examples: `#company_department_id`, `#market_category_id`.
- `ajax_method`: The AJAX request method, `GET` or `POST`. (default value is `GET`)
- `ajax_extra_settings`: The extra AJAX request settings. (default value is `new \stdClass()`)  
  **DynamicChoiceType** generates `url`, `method`, `data`, and `success` settings by default, you can overwrite them or add other settings with `ajax_extra_settings` option. Read more at [jQuery.ajax](https://api.jquery.com/jquery.ajax/).
- `build_ajax_uri_callback`: A javascript function that returns AJAX request uri. (default value is empty string `''`)  
  The function interface: `function ($, ajaxUri) {}`, it takes two parameters:
  
  - `$`: The jQuery object
  - `ajaxUri`: An uri that comes from `ajax_uri` option.
    
  The `this` in the function is the DOM element that contains whole DynamicChoiceType widget (see [`Resources/views/form/dynamic_choice.html.twig`](../views/form/dynamic_choice.html.twig)).  
  Take default function as example:  
  
  ```js
  function defaultCallback($, ajaxUri) {
      return ajaxUri;
  }
  ```

- `build_ajax_data_callback`: A javascript function that returns AJAX request data. (default value is empty string `''`)  
  The function interface: `function ($) {}`, it takes only one parameter:
  
  - `$`: The jQuery object.
    
  The `this` in the function is the DOM element that contains whole DynamicChoiceType widget (see [`Resources/views/form/dynamic_choice.html.twig`](../views/form/dynamic_choice.html.twig)).  
  Take default function as example:  

  ```js
  function defaultCallback($) {
      let $node = $(this);
      let key = $node.data('default-ajax-data-key');
      let data = {};

      data[key] = $node.find('input[type="hidden"]').val();

      return data;
  }
  ```

- `default_ajax_data_key`: A default query parameter name for the AJAX request uri. (default value is `q`)  
  `build_ajax_data_callback` will fetch the parameter values.  
  If you have over two parameters, this option will be ignored, and you need write your own `build_ajax_data_callback`.  
  For example, if the AJAX request is `/article/list?q=category_php`. Set `q` as `default_ajax_data_key` option, and set `/article/list` as `ajax_uri`.
- `render_html_callback`: A javascript function that updates the target select element. (default value is empty string `''`)  
  The function interface: `function ($target, ajaxResponse, $) {}`, it takes three parameters:  
  
  - `$target`: The target jQuery object, the DOM element that contains whole target DynamicChoiceType widget.
  - `ajaxResponse`: The response string/object from AJAX request.
  - `$`: The jQuery object
    
  The `this` in the function is the DOM element that contains whole DynamicChoiceType widget (see [`Resources/views/form/dynamic_choice.html.twig`](../views/form/dynamic_choice.html.twig)).  
  Take default function as example:  
  ```js
  function defaultCallback($target, ajaxResponse, $) {
      let $targetList = $target.find('select:first');
      let $targetValue = $target.find('input[type="hidden"]');
      let targetValue = $targetValue.val();
      let hasSelectedValue = false;
      let $html = $('<div>'+ajaxResponse+'</div>');
      let $newList = $html.find('select:first');
      let $options;

      if ($newList.length) {
          $options = $newList.find('option');
      } else {
          $options = $html.find('option');
      }

      $targetList.empty();

      $options.each(function (i, option) {
          let $option = $(option);

          if ($option.val() === targetValue) {
              hasSelectedValue = true;
          }

          $targetList.append($option);
      });

      if (hasSelectedValue) {
          $targetList.val(targetValue);
      } else {
          $targetList.find('option:first').prop('selected', true);
      }

      $targetList.trigger('change');
  }
  ```

- `auto_call_ajax_onload`: When the HTML of this form type is rendered, check this option to call AJAX callback or not (see `ajax_uri`).  
  Call AJAX callback when this option is `true`. Do nothing when this option is `false`. (default value is `true`)

You can setup the select element with choice type options:

- `choice_constraints`: Setup constraints for the select element.
- `choices`: The same as [ChoiceType::choices](https://symfony.com/doc/current/reference/forms/types/choice.html#choices)
- `choice_attr`: The same as [ChoiceType::choice_attr](https://symfony.com/doc/current/reference/forms/types/choice.html#choice_attr)
- `choice_label`: The same as [ChoiceType::choice_label](https://symfony.com/doc/current/reference/forms/types/choice.html#choice_label)
- `choice_loader`: The same as [ChoiceType::choice_loader](https://symfony.com/doc/current/reference/forms/types/choice.html#choice_loader)
- `choice_name`: The same as [ChoiceType::choice_name](https://symfony.com/doc/current/reference/forms/types/choice.html#choice_name)
- `choice_translation_domain`: The same as [ChoiceType::choice_translation_domain](https://symfony.com/doc/current/reference/forms/types/choice.html#choice_translation_domain)
- `choice_value`: The same as [ChoiceType::choice_value](https://symfony.com/doc/current/reference/forms/types/choice.html#choice_value)
- `expanded`: The same as [ChoiceType::expanded](https://symfony.com/doc/current/reference/forms/types/choice.html#expanded)
- `group_by`: The same as [ChoiceType::group_by](https://symfony.com/doc/current/reference/forms/types/choice.html#group_by)
- `multiple`: The same as [ChoiceType::multiple](https://symfony.com/doc/current/reference/forms/types/choice.html#multiple)
- `placeholder`: The same as [ChoiceType::placeholder](https://symfony.com/doc/current/reference/forms/types/choice.html#placeholder)
- `preferred_choices`: The same as [ChoiceType::preferred_choices](https://symfony.com/doc/current/reference/forms/types/choice.html#preferred_choices)


## Example Usage

```php
// src/AppBundle/Controller/DynamicChoiceController.php
namespace AppBundle\Controller;

use Nadia\Bundle\NadiaFormBundle\Form\Type\DynamicChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DynamicChoiceController extends Controller
{
    /**
     * A demo page with three DynamicChoiceType fields
     *
     * @Route("/dynamic-choice", name="demo-dynamic-choice")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $form = $this->createFormBuilder()
            ->add('choice', DynamicChoiceType::class, [
                'ajax_uri' => $this->generateUrl('demo-dynamic-choice-ajax1'),
                'target' => '#form_choice2',
                'choices' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'foobar' => 'foobar',
                ],
            ])
            ->add('choice2', DynamicChoiceType::class, [
                'ajax_uri' => $this->generateUrl('demo-dynamic-choice-ajax2'),
                'target' => '#form_choice3',
                'build_ajax_data_callback' => "
                    function myCallback ($) {
                        return {
                            var1: $('#form_choice_value').val(),
                            var2: $('#form_choice2_value').val()
                        };
                    }
                ",
            ])
            ->add('choice3', DynamicChoiceType::class)
            ->getForm()
        ;

        return $this->render('@App/DynamicChoice/index.html.twig', ['form' => $form->createView()]);
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
        $choices = [
            'ajax1-foo' => 'ajax1-foo',
            'ajax1-bar' => 'ajax1-bar',
            'ajax1-foobar' => 'ajax1-foobar',
        ];
        $html = '';
        foreach ($choices as $title => $value) {
            $html .= sprintf('<option value="%s">%s</option>', $value, $title);
        }

        return new Response($html);
    }

    /**
     * An AJAX response that contains <select> tags (rending by Form component)
     *
     * @Route("/dynamic-choice/ajax2", name="demo-dynamic-choice-ajax2")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajax2()
    {
        $form = $this->createFormBuilder()
            ->add('choice', DynamicChoiceType::class, [
                'choices' => [
                    'ajax2-foo' => 'ajax2-foo',
                    'ajax2-bar' => 'ajax2-bar',
                    'ajax2-foobar' => 'ajax2-foobar',
                ],
            ])
            ->getForm()
        ;

        return $this->render('@App/DynamicChoice/select.html.twig', ['form' => $form->createView()]);
    }
}
```

```twig
{# src/AppBundle/Resources/views/DynamicChoice/index.html.twig #}

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

```twig
{# src/AppBundle/Resources/views/DynamicChoice/select.html.twig #}

{{ form_widget(form) }}
```
