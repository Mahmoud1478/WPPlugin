<?php

namespace MMAE\WPPlugin\Form;

class Section {

	public static function make(string $name , string $page, ?string $title = null , callable|array $callback = null): Section {
		return new self($name,$page,$title ,$callback);
	}
	private array $groups = [];
	public function __construct(
		private string $name ,
		private string $page,
		private ?string $title = null ,
		private mixed $callback = null
	) {}
	public function setGroup(Group $group): static {
		$this->$group[] = $group;
		return $this;
	}

	public function setGroups(Group ...$groups): static {
		$this->groups = $groups;
		return $this;
	}
	public function mergeGroups(Group ...$groups): static {
		$this->groups += $groups;
		return $this;
	}

	public function __invoke(): void {
		add_settings_section($this->name,$this->title,$this->callback,$this->page);
		foreach ( $this->groups as $group ) {
			$group($this->page,$this->name);
		}
	}

}