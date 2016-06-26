<?php

class Scaffold{

	/**
	 * Scaffold is a static singleton, it cannot be instantiated.
	 */
	private function __construct(){}

	public static $skinOptions = array();

	public static $baseURL = '';

	public static $initHandlers = array();
	public static $readyHandlers = array();

	/**
	 * Set up the various hooks needed
	 *
	 * Handler for Hook: BeforeInit
	 */
	public static function init(){

		self::$baseURL = $GLOBALS['egScaffoldBaseURL'];

		foreach(self::$initHandlers as $fn){
			$fn();
		}

		self::initSkin();
		self::initLayouts();

		//$wgHooks['ResourceLoaderRegisterModules'][] = 'Scaffold::registerResources';
		foreach(self::$readyHandlers as $fn){
			$fn();
		}

		return true;
	}

	public static function onInit( $fn ){
		array_push(self::$initHandlers, $fn);
	}

	public static function onReady( $fn ){
		array_push(self::$readyHandlers, $fn);
	}

	/**
	 * Init the skin. Give it a chance to define resources etc
	 */
	public static function initSkin(  ){
		SkinScaffold::init();
	}

	/**
	 * Initialize layouts from $egScaffoldLayouts config
	 */
	public static function initLayouts( ){
		foreach( $GLOBALS['egScaffoldLayouts'] as $name => $className ){
			self::addLayout( $name, $className );
		}
		$layout = isset($GLOBALS['egScaffoldDefaultLayout'])
			? $GLOBALS['egScaffoldDefaultLayout']
			: 'default';
		Skinny::setLayout($layout);
	}



	/**
	 * Convenience methods for SkinScaffold
	 */
	// public static function setOptions($options, $reset=false){
	// 	if($reset===true){
	// 		self::$skinOptions = array();
	// 	}
	// 	self::$skinOptions = \Skinny::mergeOptionsArrays( self::$skinOptions, $options );
	// }
	public static function addLayout( $name, $className ){
		SkinScaffold::addLayout( $name, $className );
	}
	// public static function extendLayout( $extend, $name, $config ){
	// 	SkinScaffold::extendLayout( $extend, $name, $config );
	// }
	// public static function setLayoutOptions( $layout_name, $options ){
	// 	SkinScaffold::setLayoutOptions( $layout_name, $options );
	// }
	// public static function setLayoutTemplateOptions( $layout_name, $options ){
	// 	SkinScaffold::setLayoutTemplateOptions( $layout_name, $options );
	// }
	public static function addModules( $modules, $auto=false ){
		SkinScaffold::addModules( $modules, $auto );
	}
	public static function loadModules( $modules ){
		SkinScaffold::autoloadModules( $modules );
	}
	public static function addModulesToLayout( $layout, $modules ){
		SkinScaffold::addModulesToLayout( $layout, $modules );
	}
	public static function addTemplatePath( $path ){
		SkinScaffold::addTemplatePath( $path );
	}



}
