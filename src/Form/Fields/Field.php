<?php

namespace MMAE\WPPlugin\Form\Fields;

abstract class Field {
	protected array $options = [
		'sanitize_callback' => 'sanitize_text_field',
		'default' => null,
	];
	public function __construct(
		protected string  $name, private string $label, protected mixed $value,protected array $attributes = []
	){}
	abstract public function html();
	public function __invoke(string $page,string $section ,string $group): void {
		add_settings_field($this->name,$this->label,[$this,'html'], $page, $section);
		register_setting($group,$this->name,$this->options);
	}
	public function setDefault( mixed $value ): static {
		$this->options['default'] = $value;
		return $this;
	}
	public function setSanitizeCallback( callable|array $callback ): static {
		$this->options['sanitize_callback'] = $callback;
		return $this;
	}
	public function setOptions( array $options ): static {
		$this->options = $options;
		return $this;
	}
	protected function buildAttributes():string
	{
		$attributes = '';
		foreach ( $this->attributes as $name => $value ) {
			$attributes.=" $name='$value'";
		}
		return $attributes;
	}
}