<?php

namespace MMAE\WPPlugin\Form\Fields;


class Radio extends Field {

	public function html(): void {
		$attr = $this->buildAttributes();
		$checked = checked($this->attributes['value']??false,$this->value,false);
		echo "<input $attr  name='$this->name' type='radio' $checked ";
	}
}