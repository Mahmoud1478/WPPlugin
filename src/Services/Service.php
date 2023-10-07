<?php

namespace MMAE\WPPlugin\Services;

use MMAE\WPPlugin\Plugin;

interface Service {
	public function __invoke( Plugin $plugin ): void ;
}