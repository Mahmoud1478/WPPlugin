<?php

namespace MMAE\WPPlugin\Form\Fields;


class Check extends Field {

	public function html(): void {
		$attr = $this->buildAttributes();
		$checked = checked($this->value,1,false);
		echo "<input $attr  name='$this->name' type='checkbox' $checked value='1'>";
	}
}