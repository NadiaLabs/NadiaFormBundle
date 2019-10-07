DynamicChoiceType
=================

#### Add DynamicChoiceType form theme to Twig Configuration

```yaml
# Twig Configuration
twig:
    form_themes:
        # Add DynamicChoiceType form theme
        - '@NadiaForm/Form/dynamic_choice.html.twig'
```

#### Example Controller and views

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
                'target' => '#form_choice2_list',
                'choices' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'foobar' => 'foobar',
                ],
            ])
            ->add('choice2', DynamicChoiceType::class, [
                'ajax_uri' => $this->generateUrl('demo-dynamic-choice-ajax2'),
                'target' => '#form_choice3_list',
                'build_ajax_data_callback' => "
                    function ($) {
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
