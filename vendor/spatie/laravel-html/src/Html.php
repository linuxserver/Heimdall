<?php

namespace Spatie\Html;

use BackedEnum;
use DateTimeImmutable;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Spatie\Html\Elements\A;
use Spatie\Html\Elements\Button;
use Spatie\Html\Elements\Div;
use Spatie\Html\Elements\Element;
use Spatie\Html\Elements\Fieldset;
use Spatie\Html\Elements\File;
use Spatie\Html\Elements\Form;
use Spatie\Html\Elements\I;
use Spatie\Html\Elements\Img;
use Spatie\Html\Elements\Input;
use Spatie\Html\Elements\Label;
use Spatie\Html\Elements\Legend;
use Spatie\Html\Elements\Option;
use Spatie\Html\Elements\P;
use Spatie\Html\Elements\Select;
use Spatie\Html\Elements\Span;
use Spatie\Html\Elements\Textarea;
use UnitEnum;

class Html
{
    use Macroable;

    public const HTML_DATE_FORMAT = 'Y-m-d';
    public const HTML_TIME_FORMAT = 'H:i:s';

    /** @var \Illuminate\Http\Request */
    protected $request;

    /** @var \ArrayAccess|array */
    protected $model;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string|null $href
     * @param string|null $text
     *
     * @return \Spatie\Html\Elements\A
     */
    public function a($href = null, $contents = null)
    {
        return A::create()
            ->attributeIf($href, 'href', $href)
            ->html($contents);
    }

    /**
     * @param string|null $href
     * @param string|null $text
     *
     * @return \Spatie\Html\Elements\I
     */
    public function i($contents = null)
    {
        return I::create()
            ->html($contents);
    }

    /**
     * @param \Spatie\Html\HtmlElement|string|null $contents
     *
     * @return \Spatie\Html\Elements\P
     */
    public function p($contents = null)
    {
        return P::create()
            ->html($contents);
    }

    /**
     * @param string|null $type
     * @param string|null $text
     *
     * @return \Spatie\Html\Elements\Button
     */
    public function button($contents = null, $type = null, $name = null)
    {
        return Button::create()
            ->attributeIf($type, 'type', $type)
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->html($contents);
    }

    /**
     * @param \Illuminate\Support\Collection|iterable|string $classes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function class($classes): Htmlable
    {
        if ($classes instanceof Collection) {
            $classes = $classes->toArray();
        }

        $attributes = new Attributes();
        $attributes->addClass($classes);

        return new HtmlString(
            $attributes->render()
        );
    }

    /**
     * @param string|null $name
     * @param bool $checked
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function checkbox($name = null, $checked = null, $value = '1')
    {
        return $this->input('checkbox', $name, $value)
            ->attributeIf(! is_null($value), 'value', $value)
            ->attributeIf((bool) $this->old($name, $checked), 'checked');
    }

    /**
     * @param \Spatie\Html\HtmlElement|string|iterable|int|float|null $contents
     *
     * @return \Spatie\Html\Elements\Div
     */
    public function div($contents = null)
    {
        return Div::create()->children($contents);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function email($name = null, $value = null)
    {
        return $this->input('email', $name, $value);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function search($name = null, $value = null)
    {
        return $this->input('search', $name, $value);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param bool $format
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function date($name = '', $value = null, $format = true)
    {
        $element = $this->input('date', $name, $value);

        if (! $format || empty($element->getAttribute('value'))) {
            return $element;
        }

        return $element->value($this->formatDateTime($element->getAttribute('value'), self::HTML_DATE_FORMAT));
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param bool $format
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function datetime($name = '', $value = null, $format = true)
    {
        $element = $this->input('datetime-local', $name, $value);

        if (! $format || empty($element->getAttribute('value'))) {
            return $element;
        }

        return $element->value($this->formatDateTime(
            $element->getAttribute('value'),
            self::HTML_DATE_FORMAT.'\T'.self::HTML_TIME_FORMAT
        ));
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param string|null $min
     * @param string|null $max
     * @param string|null $step
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function range($name = '', $value = null, $min = null, $max = null, $step = null)
    {
        return $this->input('range', $name, $value)
            ->attributeIfNotNull($min, 'min', $min)
            ->attributeIfNotNull($max, 'max', $max)
            ->attributeIfNotNull($step, 'step', $step);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param bool $format
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function time($name = '', $value = null, $format = true)
    {
        $element = $this->input('time', $name, $value);

        if (! $format || empty($element->getAttribute('value'))) {
            return $element;
        }

        return $element->value($this->formatDateTime($element->getAttribute('value'), self::HTML_TIME_FORMAT));
    }

    /**
     * @param string $tag
     *
     * @return \Spatie\Html\Elements\Element
     */
    public function element($tag)
    {
        return Element::withTag($tag);
    }

    /**
     * @param string|null $type
     * @param string|null $name
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function input($type = null, $name = null, $value = null)
    {
        $hasValue = ! is_null($value) || ($type !== 'password' && ! is_null($this->old($name, $value)));

        return Input::create()
            ->attributeIf($type, 'type', $type)
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name))
            ->attributeIf($hasValue, 'value', $this->old($name, $value));
    }

    /**
     * @param \Spatie\Html\HtmlElement|string|null $legend
     *
     * @return \Spatie\Html\Elements\Fieldset
     */
    public function fieldset($legend = null)
    {
        return $legend
            ? Fieldset::create()->legend($legend)
            : Fieldset::create();
    }

    /**
     * @param string $method
     * @param string|null $action
     *
     * @return \Spatie\Html\Elements\Form
     */
    public function form($method = 'POST', $action = null)
    {
        $method = strtoupper($method);
        $form = Form::create();

        // If Laravel needs to spoof the form's method, we'll append a hidden
        // field containing the actual method
        if (in_array($method, ['DELETE', 'PATCH', 'PUT'])) {
            $form = $form->addChild($this->hidden('_method')->value($method));
        }

        // On any other method that get, the form needs a CSRF token
        if ($method !== 'GET') {
            $form = $form->addChild($this->token());
        }

        return $form
            ->method($method === 'GET' ? 'GET' : 'POST')
            ->attributeIf($action, 'action', $action);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function hidden($name = null, $value = null)
    {
        return $this->input('hidden', $name, $value);
    }

    /**
     * @param string|null $src
     * @param string|null $alt
     *
     * @return \Spatie\Html\Elements\Img
     */
    public function img($src = null, $alt = null)
    {
        return Img::create()
            ->attributeIf($src, 'src', $src)
            ->attributeIf($alt, 'alt', $alt);
    }

    /**
     * @param \Spatie\Html\HtmlElement|iterable|string|null $contents
     * @param string|null $for
     *
     * @return \Spatie\Html\Elements\Label
     */
    public function label($contents = null, $for = null)
    {
        return Label::create()
            ->attributeIf($for, 'for', $this->fieldName($for))
            ->children($contents);
    }

    /**
     * @param \Spatie\Html\HtmlElement|string|null $contents
     *
     * @return \Spatie\Html\Elements\Legend
     */
    public function legend($contents = null)
    {
        return Legend::create()->html($contents);
    }

    /**
     * @param string $email
     * @param string|null $text
     *
     * @return \Spatie\Html\Elements\A
     */
    public function mailto($email, $text = null)
    {
        return $this->a('mailto:'.$email, $text ?: $email);
    }

    /**
     * @param string|null $name
     * @param iterable $options
     * @param string|iterable|null $value
     *
     * @return \Spatie\Html\Elements\Select
     */
    public function multiselect($name = null, $options = [], $value = null)
    {
        return Select::create()
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name))
            ->options($options)
            ->value($name ? $this->old($name, $value) : $value)
            ->multiple();
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param string|null $min
     * @param string|null $max
     * @param string|null $step
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function number($name = null, $value = null, $min = null, $max = null, $step = null)
    {
        return $this->input('number', $name, $value)
            ->attributeIfNotNull($min, 'min', $min)
            ->attributeIfNotNull($max, 'max', $max)
            ->attributeIfNotNull($step, 'step', $step);
    }

    /**
     * @param string|null $text
     * @param string|null $value
     * @param bool $selected
     *
     * @return \Spatie\Html\Elements\Option
     */
    public function option($text = null, $value = null, $selected = false)
    {
        return Option::create()
            ->text($text)
            ->value($value)
            ->selectedIf($selected);
    }

    /**
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function password($name = null)
    {
        return $this->input('password', $name);
    }

    /**
     * @param string|null $name
     * @param bool $checked
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function radio($name = null, $checked = null, $value = null)
    {
        return $this->input('radio', $name, $value)
            ->attributeIf($name, 'id', $value === null ? $name : ($name.'_'.Str::slug($value)))
            ->attributeIf(! is_null($value), 'value', $value)
            ->attributeIf((! is_null($value) && $this->old($name) === $value) || $checked, 'checked');
    }

    /**
     * @param string|null $name
     * @param iterable $options
     * @param string|iterable|null $value
     *
     * @return \Spatie\Html\Elements\Select
     */
    public function select($name = null, $options = [], $value = null)
    {
        return Select::create()
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name))
            ->options($options)
            ->value($name ? $this->old($name, $value) : $value);
    }

    /**
     * @param \Spatie\Html\HtmlElement|string|null $contents
     *
     * @return \Spatie\Html\Elements\Span
     */
    public function span($contents = null)
    {
        return Span::create()->children($contents);
    }

    /**
     * @param string|null $text
     *
     * @return \Spatie\Html\Elements\Button
     */
    public function submit($text = null)
    {
        return $this->button($text, 'submit');
    }

    /**
     * @param string|null $text
     *
     * @return \Spatie\Html\Elements\Button
     */
    public function reset($text = null)
    {
        return $this->button($text, 'reset');
    }

    /**
     * @param string $number
     * @param string|null $text
     *
     * @return \Spatie\Html\Elements\A
     */
    public function tel($number, $text = null)
    {
        return $this->a('tel:'.$number, $text ?: $number);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Input
     */
    public function text($name = null, $value = null)
    {
        return $this->input('text', $name, $value);
    }

    /**
     * @param string|null $name
     *
     * @return \Spatie\Html\Elements\File
     */
    public function file($name = null)
    {
        return File::create()
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name));
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return \Spatie\Html\Elements\Textarea
     */
    public function textarea($name = null, $value = null)
    {
        return Textarea::create()
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name))
            ->value($this->old($name, $value));
    }

    /**
     * @return \Spatie\Html\Elements\Input
     */
    public function token()
    {
        return $this
            ->hidden()
            ->name('_token')
            ->value($this->request->session()->token());
    }

    /**
     * @param \ArrayAccess|array $model
     *
     * @return $this
     */
    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param \ArrayAccess|array $model
     * @param string|null $method
     * @param string|null $action
     *
     * @return \Spatie\Html\Elements\Form
     */
    public function modelForm($model, $method = 'POST', $action = null): Form
    {
        $this->model($model);

        return $this->form($method, $action);
    }

    /**
     * @return $this
     */
    public function endModel()
    {
        $this->model = null;

        return $this;
    }

    /**
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function closeModelForm(): Htmlable
    {
        $this->endModel();

        return $this->form()->close();
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return mixed
     */
    protected function old($name, $value = null)
    {
        if (empty($name)) {
            return $value;
        }

        // Convert array format (sth[1]) to dot notation (sth.1)
        $name = preg_replace('/\[(.+)\]/U', '.$1', $name);

        // If there's no default value provided, the html builder currently
        // has a model assigned and there aren't old input items,
        // try to retrieve a value from the model.
        if (is_null($value) && $this->model && empty($this->request->old())) {
            $value = ($value = data_get($this->model, $name)) instanceof UnitEnum
                ? $this->getEnumValue($value)
                : $value;
        }

        return $this->request->old($name, $value);
    }

    /**
     * Retrieve the value from the current session or assigned model. This is
     * a public alias for `old`.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return mixed
     */
    public function value($name, $default = null)
    {
        return $this->old($name, $default);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function fieldName($name)
    {
        return $name;
    }

    protected function ensureModelIsAvailable()
    {
        if (empty($this->model)) {
            throw new Exception('Method requires a model to be set on the html builder');
        }
    }

    /**
     * @param string $value
     * @param string $format DateTime formatting string supported by date_format()
     * @return string
     */
    protected function formatDateTime($value, $format)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            $date = new DateTimeImmutable($value);

            return $date->format($format);
        } catch (Exception $e) {
            return $value;
        }
    }

    /**
     * Get the value from the given enum.
     *
     * @param  \UnitEnum|\BackedEnum  $value
     * @return string|int
     */
    protected function getEnumValue($value)
    {
        return $value instanceof BackedEnum
            ? $value->value
            : $value->name;
    }
}
