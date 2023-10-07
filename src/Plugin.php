<?php

namespace MMAE\WPPlugin;

use JetBrains\PhpStorm\NoReturn;
use MMAE\WPPlugin\Form\Section;
use MMAE\WPPlugin\Http\HttpClient;
use MMAE\WPPlugin\Services\Service;
use wpdb;

abstract class Plugin {
	private static HttpClient $http;
	private static string $prefix = '';
	private static string $file = '';
	private static string $plugin_dir = '';
	private static string $plugin_url = '';
	private static string $id = '';
	private static string $assets_folder = 'assets';
	private static wpdb $db;

	public static function Http(): HttpClient {
		if ( ! isset( self::$http ) ) {
			static::$http = new HttpClient();
		}

		return static::$http;
	}

	public static function setPluginFile( string $file ): void {
		static::$file = $file;
		static::setPluginDir( static::$file );
		self::set_id( plugin_basename( static::$file ) );
	}

	public static function getAssetsFolder(): string {
		return self::$assets_folder;
	}

	public static function get_db(): wpdb {
		return self::$db;
	}

	public static function setDB( wpdb $wpdb ): void {
		self::$db = $wpdb;
	}

	public static function get_id(): string {
		return self::$id;
	}

	private static function set_id( string $id ): void {
		self::$id = $id;
	}

	private static function setAssetsFolder( string $assets_folder ): void {
		self::$assets_folder = $assets_folder;
	}

	public static function getPluginUrl(): string {
		return self::$plugin_url;
	}

	private static function setPluginUrl( string $plugin_url ): void {
		self::$plugin_url = $plugin_url;
	}

	/**
	 * @throws \Exception
	 */
	public function run(): void {
		$this->boot();
		register_activation_hook( self::$plugin_dir, [ $this, 'activate' ] );
		register_deactivation_hook( self::$plugin_dir, [ $this, 'deactivate' ] );
		register_uninstall_hook( self::$plugin_dir, [ $this, 'uninstall' ] );
		$this->addAction( 'init', 'init' );
		$this->addAction( 'admin_menu', 'admin_menu' );
		$this->addFilter( 'plugin_action_links_' . self::$id, 'links' );
		$this->__register_servers();
	}

	protected function services(): array {
		return [];
	}

	/**
	 * @throws \Exception
	 */
	private function __register_servers(): void {
		foreach ( $this->services() as $service ) {
			$this->call( $service );
		}
	}

	/**
	 * @throws \Exception
	 */
	private function call( callable|string $fun ): void {
		if ( is_callable( $fun ) ) {
			$fun( $this );
			return;
		}
		if ( class_exists( $fun ) ) {
			$class = new $fun();
			if ( ! $class instanceof Service ) {
				throw new \Exception( sprintf( 'Target class %s must implements %s class', $fun, Service::class ), 500 );
			}
			$class( $this );

			return;
		}
		$this->{$fun}();
	}

	public function links( array $links ): array {
		return $links;
	}

	abstract protected function boot(): void;

	abstract protected function activate(): void;

	abstract protected function deactivate(): void;

	abstract protected static function uninstall(): void;

	abstract public function init(): void;

	abstract protected function admin_menu(): void;


	public function addAction( string $name, callable|string|array $callback, int $priority = 10, ...$arg ): void {
		add_action( $name, $this->resolveCallback($callback), $priority, $arg );
	}

	public function addFilter( string $name, callable|string|array $callback, int $priority = 10, ...$arg ): void {
		add_filter( $name, $this->resolveCallback($callback), $priority, $arg );
	}

	public static function getPluginDir(): string {
		return self::$plugin_dir;
	}

	public static function setPluginDir( string $plugin_dir ): void {
		self::$plugin_dir = plugin_dir_path( $plugin_dir );
	}

	public function getOption( string $name, mixed $default = null ): mixed {
		return get_option( static::$prefix . $name, $default );
	}

	public function getEscapedAttributeOption( string $name, mixed $default = null ): mixed {
		return esc_attr( $this->getOption( $name, $default ) );
	}

	public function getEscapedHTMLOption( string $name, mixed $default = null ): mixed {
		return esc_html( $this->getOption( $name, $default ) );
	}

	public function setOption( string $name, mixed $value, string|bool $autoload = 'yes' ): bool {
		return add_option( static::$prefix . $name, $value, autoload: $autoload );
	}

	public static function getPrefix(): string {
		return self::$prefix;
	}


	public function registerFormForPage( Section ...$form_sections ): void {
		foreach ( $form_sections as $section ) {
			$section();
		}
	}

	public static function setPrefix( string $prefix ): void {
		self::$prefix = $prefix;
	}

	public function plugin_url( ?string $path ): string {
		return plugins_url( $path );
	}

//	public function load( string $file, array $data = [] ): string {
//		ob_start();
//		extract($data);
//		include self::$plugin_dir . $file . '.php';
//		$tmp = ob_get_clean();
//		ob_flush();
//		return $tmp;
//	}
	public function loadView( string $file, array $data = [] ): void {
		extract( $data );
		require_once self::$plugin_dir . $file . '.php';
	}

	public function addSettingPage(
		string $page_title,
		string $menu_title,
		string $permission,
		string $slug,
		callable|string|array $callback = '',
		?int $position = null
	): bool|string {
		return add_options_page( $page_title, $menu_title, $permission, $slug, $this->resolveCallback( $callback ), $position );
	}


	public function addMenuPage(
		string $page_title,
		string $menu_title,
		string $permission,
		string $slug,
		callable|string|array $callback = '',
		?string $icon = null,
		?int $position = null
	): string {
		return add_menu_page( $page_title, $menu_title, $permission, $slug, $this->resolveCallback( $callback ), $icon, $position );
	}

	public function resolveCallback( callable|string|array $callback ): callable|array {
		if ( is_callable( $callback ) or gettype( $callback ) == 'array' ) {
			return $callback;
		}
		return [ $this, $callback ];
	}

	#[NoReturn]
	public function dd( ...$prams ): void {
		foreach ( $prams as $pram ) {
			echo '<pre style="background-color:#18171B;color: #1299DA; padding:10px" >';
			if ( gettype( $pram ) == 'string' ) {
				echo $pram;
			} else {
				print_r( $pram );
			}
			echo '</pre>';
		}

//		require_once __DIR__.'/Views/dd.php' ;
		die( 500 );
	}
}