<?php
/**
 * Scaffold - A highly customizable MediaWiki theme built with Skinny and Bootstrap 3
 *
 * @Version 1.0.0
 * @Author Andru Vallance <andru@tinymighty.com>
 * @Copyright Andru Vallance, 2012
 * @License: GPLv2 (http://www.gnu.org/copyleft/gpl.html)
 */

$cd = dirname(__FILE__);

//when installed via Composer, this file is loaded too early to access
//wgStylePath, so we delay init intil the SetupAfterCache hook
//by which MediaWiki is properly initialized
$GLOBALS['wgHooks']['SetupAfterCache'][] = function(){
	$cd = dirname(__FILE__);

	$GLOBALS['egScaffoldBasePath'] = __DIR__;
	$GLOBALS['egScaffoldBaseURL'] = $GLOBALS['wgStylePath'].'/'.basename(__DIR__);
	$GLOBALS['egScaffoldLayouts'] = array(
		'base'=>'Scaffold\Layouts\Base',
		'default'=>'Scaffold\Layouts\DefaultLayout'
	);
	$GLOBALS['egScaffoldDefaultLayout'] = 'default';

	//load skin variants
	require( $cd . '/layouts/base/Base.php' );
	require( $cd . '/layouts/default/Default.php' );

	// Scaffold::init();

};
