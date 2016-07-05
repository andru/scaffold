<?php
if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

class SkinScaffold extends \Skinny\Skin {

	public static $modules = array();

	public $skinname = 'scaffold';
	public $stylename = 'Scaffold';
	public $template = 'ScaffoldTemplate';
	public $useHeadElement = true;


	// public static function init(){
	// 	// self::loadModules('skins.scaffold.bootstrap.js');
	// 	self::loadModules('skins.scaffold.js');
	// }
	//

	public function initPage( OutputPage $out ) {
		parent::initPage( $out );

		$baseURL = $GLOBALS['egScaffoldBaseURL'];

		//add the css modules separately to prevent a FOUC
		$out->addModuleStyles( 'skins.scaffold.bootstrap.css' );
		$out->addModuleStyles( 'skins.scaffold');
		$out->addModuleStyles( 'skins.scaffold.fontawesome' );

		//js items will be appended after page load
		$out->addModules( 'skins.scaffold.bootstrap.js' );
		$out->addModules( 'skins.scaffold.js' );


		//since we're using theb mediawiki generated head element, we have to add the viewport meta tag
		//so the layout scaled properly to mobile devices
		// $out->addMeta( 'viewport', 'width=device-width');//,initial-width=1,maximum-width=1' );

		/* Until ResourceLoader can correctly parse multiple urls in a single font-family
		webfont files have to be defined in the head to prevent it screwing things up */

		$out->addInlineStyle("@font-face {
		  font-family: 'Glyphicons Halflings';
		  src: url('$baseURL/resources/bootstrap-3.0.3/fonts/glyphicons-halflings-regular.eot');
		  src: url('$baseURL/resources/bootstrap-3.0.3/fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'), url('$baseURL/resources/bootstrap-3.0.3/fonts/glyphicons-halflings-regular.woff') format('woff'), url('$baseURL/resources/bootstrap-3.0.3/fonts/glyphicons-halflings-regular.ttf') format('truetype'), url('$baseURL/resources/bootstrap-3.0.3/fonts/glyphicons-halflings-regular.svg#glyphicons-halflingsregular') format('svg');
		}");
		$out->addInlineStyle("@font-face {
		  font-family: 'FontAwesome';
		  src: url('$baseURL/resources/font-awesome-4.0.3/fonts/fontawesome-webfont.eot?v=4.0.3');
		  src: url('$baseURL/resources/font-awesome-4.0.3/fonts/fontawesome-webfont.eot?#iefix&v=4.0.3') format('embedded-opentype'), url('$baseURL/resources/font-awesome-4.0.3/fonts/fontawesome-webfont.woff?v=4.0.3') format('woff'), url('$baseURL/resources/font-awesome-4.0.3/fonts/fontawesome-webfont.ttf?v=4.0.3') format('truetype'), url('$baseURL/resources/font-awesome-4.0.3/fonts/fontawesome-webfont.svg?v=4.0.3#fontawesomeregular') format('svg');
		  font-weight: normal;
		  font-style: normal;
		}");

		$out->addHeadItem('meta-viewport', '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">');

	}


}
