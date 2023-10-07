<?php

namespace MMAE\WPPlugin\Form;

use MMAE\WPPlugin\Form\Fields\Check;
use MMAE\WPPlugin\Form\Fields\Radio;
use MMAE\WPPlugin\Form\Fields\Select;
use MMAE\WPPlugin\Form\Fields\Text;

class Form {

	public static function section( string $name, string $page, ?string $title = null, callable|array $callback = null ): Section
	{
		return new Section( $name, $page, $title, $callback );
	}

	public static function group( string $name ): Group
	{
		return new Group( $name );
	}

	public static function text( string  $name, string $label, mixed $value,array $attributes = [] ): Text
	{
		return new Text($name,$label,$value,$attributes);
	}

	public static function check( string  $name, string $label, mixed $value,array $attributes = [] ): Check
	{
		return new Check($name,$label,$value,$attributes);
	}
	public static function radio( string  $name, string $label, mixed $value,array $attributes = [] ): Radio {
		return new Radio($name,$label,$value,$attributes);
	}

	public static function select( string  $name, string $label, mixed $value,array $optionList,array $attributes = [] ): Select
	{
		return new Select($name,$label,$value,$optionList,$attributes);
	}




}