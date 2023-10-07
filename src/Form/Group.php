<?php

namespace MMAE\WPPlugin\Form;

use MMAE\WPPlugin\Form\Fields\Field;

class Group {

	private array $fields = [];

	public static function make( string $name ): Group {
		return new self($name);
	}

	public function __construct(private string $name)
	{}
	public function __invoke(string $page, string$section): void {
		foreach ( $this->fields as $field ) {
			$field($page,$section,$this->name,);
		}
	}
	public function setField(Field $group): static {
		$this->fields[] = $group;
		return $this;
	}
	public function setFields(Field ...$groups): static {
		$this->fields = $groups;
		return $this;
	}
	public function mergeFields(Field ...$groups): static {
		$this->fields += $groups;
		return $this;
	}

}