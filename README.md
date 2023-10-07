> # WPPlugin
a library to centralize wordpress apis using "Object-oriented programming" with some Automation
*************************************
>[!WARNING]
> ## _THIS LIBRARY NOT PUBLISHED YET_
**********************************
## installation :
```shell 
    composer require mmae/upplugin
```
**************************************************
## Usage : 
 1 - simply by extending base plugin class "Plugin" then implement basic functions 
 * activate => called when plugin activated
 * uninstall => called when plugin uninstalled
 * deactivate => called when plugin deactivate
 * init => called when init hook called
 * admin_menu => called when admin_menu hook called
 * boot => called before anything even hooks registration

```php
<?php

namespace WordCount;
use MMAE\WPPlugin\Plugin;

class WordCount extends Plugin {

	protected function activate(): void {
	}

	protected function deactivate(): void {
	}

	protected static function uninstall(): void {
	}

	public function init(): void {
	
	}

	public function admin_menu(): void {
	}

	protected function boot(): void {
	}
}
```
2 - in your plugin main file set some configuration then call run function 
```php
    /*
 * Plugin Name:       Word Count
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Skeleton .
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Mahmoud Mostafa
 * Author URI:        https://mahmoud-mostafa.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       Base
 * Domain Path:       src/languages
 */


use WordCount\WordCount;

require_once 'vendor/autoload.php';
global $wpdb;

WordCount::setPluginFile(__FILE__);
WordCount::setPrefix('wc_');
WordCount::setDB($wpdb);
$wordCount = new WordCount();
$wordCount->run();

```
*************************
# Example
in this example this plugin just count post words and display it based on options \
so let's build option page \
but first let's build options form \
to keep our code clean and clear let's  separate our logic into services \
to build service class you must implement "Service" class like this
```php
<?php
namespace WordCount\Services;
use MMAE\WPPlugin\Plugin;
use MMAE\WPPlugin\Services\Service;
class AdminPageService implements Service {
	public function __invoke( Plugin $plugin ): void {
	}
}
```
then we can interact with plugin class now \
we can now use form interface ("like laravel facade") to build our form 
>[!NOTE] 
> supported fields \
> text \
> select \
> checkbox

```php
<?php

namespace WordCount\Services;

use MMAE\WPPlugin\Form\Form;
use MMAE\WPPlugin\Plugin;
use MMAE\WPPlugin\Services\Service;

class AdminPageService implements Service {
	public function __invoke( Plugin $plugin ): void {
		$prefix = $plugin::getPrefix();
		$plugin->addAction( 'admin_init', function () use ( $plugin, $prefix ) {
			$plugin->registerFormForPage(
				Form::section( $prefix . 'section_1', $plugin->setting_page_slug, 'Basics' )->setGroups(
					Form::group( $prefix . 'group_1' )->setFields(
						Form::select( $prefix . 'location', 'Display location', $plugin->getEscapedAttributeOption( 'location', '0' ), [
							'Begging of Post' => 0,
							'End of Post'     => 1,
						]),
						Form::text( $prefix . 'title', 'Headline', $plugin->getEscapedAttributeOption( 'title', 'title' ) ),
						Form::check( $prefix . 'activate_word_count', 'Word Count', $plugin->getEscapedAttributeOption( 'activate_word_count', '1' ) ),
						Form::check( $prefix . 'activate_characters_count', 'Characters Count', $plugin->getEscapedAttributeOption( 'activate_characters_count', '1' ) ),
						Form::check( $prefix . 'activate_read', 'Read Minutes', $plugin->getEscapedAttributeOption( 'activate_read', '1' ) ),
					),
				),
			);
		} );
	}

	function text( $input ) {
		return $input;
	}
}
```
then register this service like this

```php
<?php

namespace WordCount;

use WordCount\Services\AdminPageService;
use MMAE\WPPlugin\Plugin;
use WordCount\Services\WordCountService;

class WordCount extends Plugin {

	public string $setting_page_slug = 'word-count-plugin-setting';

	public array $services = [
		AdminPageService::class,
	];
	protected function activate(): void {
		flush_rewrite_rules();
	}

	protected function deactivate(): void {
		flush_rewrite_rules();
	}

	protected static function uninstall(): void {
		flush_rewrite_rules();
	}

	public function init(): void {}

	public function admin_menu(): void {
	}

	protected function services(): array {
		return $this->services;
	}
	protected function boot(): void {
	}
}
```
now let's create our view \
in path 'src/src/templates/pages/options.php'

```php
<style>
    .headding{
        /*padding: 10px;*/
    }
    .headding h1{
        font-size: 35px;
    }
    form{
        padding: 0 10px;
    }
</style>
<div class="wrap">
    <section class="headding">
        <h1>
            Word Count Plugin Options Page
        </h1>
    </section>
    <form action="options.php" method="post">
	    <?php settings_fields($group); ?>
	    <?php do_settings_sections($page); ?>
	    <?php submit_button(); ?>

    </form>
</div>

```
now let's register the page \
in admin_menu function
```php
<?php

namespace WordCount;

use WordCount\Services\AdminPageService;
use MMAE\WPPlugin\Plugin;
use WordCount\Services\WordCountService;

class WordCount extends Plugin {

	public string $setting_page_slug = 'word-count-plugin-setting';

	public array $services = [
		AdminPageService::class,
	];
	protected function activate(): void {
		flush_rewrite_rules();
	}

	protected function deactivate(): void {
		flush_rewrite_rules();
	}

	protected static function uninstall(): void {
		flush_rewrite_rules();
	}

	public function init(): void {}

	public function admin_menu(): void {

		$this->addSettingPage(
			'Word Count Setting', 'Word Count',
			'manage_options', $this->setting_page_slug,
			fn() => $this->loadView( 'src/templates/pages/options',[
				'page' => $this->setting_page_slug,
				'group' => static::getPrefix() .'group_1',
			])
		);
	}

	protected function services(): array {
		return $this->services;
	}
	protected function boot(): void {
	}
}
```
we have it folks and it works
![options page](/docs/options.png)
![options page](/docs/options2.png)

now let's make the actual service
make new service class lets call it "WordCountService"
```php
<?php

namespace WordCount\Services;

use MMAE\WPPlugin\Plugin;
use MMAE\WPPlugin\Services\Service;

class WordCountService implements Service {
	public function __invoke( Plugin $plugin ): void {
	}
}
```
then implement out logic 
```php
<?php
namespace WordCount\Services;
use MMAE\WPPlugin\Plugin;
use MMAE\WPPlugin\Services\Service;

class WordCountService implements Service {
	public function __invoke( Plugin $plugin ): void {
		$plugin->addFilter( 'the_content', function ( $content ) use ( $plugin ) {
			if ( ! is_main_query() or ! is_single() ) {
				return $content;
			}
			if ( $plugin->getOption( 'location' ) ) {

				return $content . $this->genHTML($plugin , strip_tags($content));
			}

			return $this->genHTML($plugin , strip_tags($content)) . $content;
		} );
	}

	function genHTML( Plugin $plugin , $content): string {
		$title = $plugin->getEscapedAttributeOption('title');
		$count = str_word_count($content);
		$html = "<h3>$title</h3><p>";
		if ($plugin->getOption('activate_word_count')){
			$html.= "This Post Has $count word. <br>";
		}
		if ($plugin->getOption('activate_characters_count')){
			$html.= "This Post Has ".strlen($content)." characters. <br>";
		}
		if ($plugin->getOption('activate_read')){
			$html.= "This Post read time ". round($count/225) ." minnute <br>";
		}

		$html.= "</p>";
		return $html;
	}

}
```
then register it in our plugin

```php
use WordCount\Services\WordCountService;
    protected function boot(): void {
		if (
			$this->getOption('activate_word_count') or
			$this->getOption($this->getOption('activate_characters_count')) or
			$this->getOption('activate_read')
		){
			$this->services[] = WordCountService::class;
		}
	}
```
### and it works
![post with plugin](/docs/post.png)