<?php

namespace MMAE\WPPlugin\Form\Fields;


class Select extends Field {

	public function __construct( string $name, string $label, mixed $value , protected array $optionsList , array $attributes=[]) {
		parent::__construct( $name, $label, $value ,$attributes);
	}

	public function html(): void {
		$attr = $this->buildAttributes();
		?>
		<select <?php echo $attr ?>  name='<?php echo $this->name?>'>
			<?php foreach ( $this->optionsList as $text => $value ): ?>
				<option value='<?php echo $value ?>' <?php selected($this->value,$value) ?> ><?php echo $text ?></option>
			<?php endforeach; ?>
		</select>
	<?php }

}