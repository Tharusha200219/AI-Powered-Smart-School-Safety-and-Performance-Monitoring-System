<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $title;
    public $name;
    public $type;
    public $attr;
    public $isRequired;
    public $placeholder;
    public $class;
    public $value;
    public $options;

    public function __construct(
        string $name,
        string $type = 'text',
        string $attr = '',
        bool $isRequired = false,
        ?string $placeholder = null,
        ?string $class = null,
        ?string $title = '',
        ?string $value = '',
        ?array $options = []
    ) {
        $this->title = $title ?? $placeholder;
        $this->name = $name;
        $this->type = $type;
        $this->attr = $attr;
        $this->isRequired = $isRequired;
        $this->placeholder = $placeholder;
        $this->class = $class;
        $this->value = $value;
        $this->options = $options;
    }

    public function render()
    {
        return view('components.input');
    }
}
