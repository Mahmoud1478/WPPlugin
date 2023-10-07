<?php

namespace MMAE\WPPlugin\Form\Fields;


class Text extends Field {

	public function html(): void {
		$attr = $this->buildAttributes();
		echo "<input $attr  name='$this->name' type='text' value='$this->value'>";
	}
}