<?php
namespace Scaffold\Layouts;

class Base extends \Skinny\Layout {

	protected $contentClass = '';

	protected $showNavBar = true;

	protected $showSharedSidebar = true;
	protected $sharedSidebarPosition = 'right';

	protected $showFancyToc = true;
	protected $fancyTocZone = 'shared-sidebar';

	protected $showSearch = true;
	protected $searchZone = 'navbar-center';

	protected $showStandardSidebar = false;
	protected $standardSidebarZone = 'classic-sidebar';

	protected $showToolbox = true;
	protected $toolboxZone = 'page menu';

	protected $showUserMenu = true;
	protected $userMenuTitleAsUsername = true;

	protected $showBreadcrumbs = true;
	protected $breadcrumbsZone = 'prepend:title';

	//map content_navigation array keys to glyphicon names
	public $key_to_icon = array(
		've-edit'    => 'edit',
		'form_edit'  => 'edit',
		'edit'       => 'edit',
		'history'    => 'time',
		'delete'     => 'remove',
		'move'       => 'arrow-right',
		'protect'    => 'lock',
		'watch'      => 'eye-open',
		'viewsource' => 'align-justify',
		'purge'      => 'refresh',
		'main'       => 'file',
		'talk'       => 'comment'
	);

	public function initialize(){

		if(!isset($this->languages) && isset(\Scaffold::$skinOptions['languages'])){
			$this->languages = \Scaffold::$skinOptions['languages'];
		}

		if(isset($this->languages)){
			$names = Language::getLanguageNames();
			$languages = array();
			foreach($this->languages as $code){
				if(array_key_exists($code, $names)){
					$languages[$code] = $names[$code];
				}
			}
			$this->data['languages'] = $languages;
			$context = \RequestContext::getMain();
			$active = $context->getLanguage();
			$this->data['active_language'] = array(
				'name'=>$active->fetchLanguageName( $active->getCode() ),
				'code'=>$active->getCode()
			);
		}

		/*
		In order to be highly configurable, this skin regularly renders a template to a zone,
		and then adds that zone to another zone.
		eg.

			$this->addTemplateTo('navbar-brand', 'navbar-brand'); //render the `navbar-brand.tpl.php` template to the `navbar-brand` zone
			$this->addZoneTo('navbar', 'navbar-brand'); //append the content of the `navbar-brand` zone to the `navbar` zone

		This makes it easier to override standard content. If you swap out a template, you can choose
		to skip a main zone (eg. navbar) and instead specifically insert the zones you want (eg. navbar-brand, navbar-menu)

		Incase you're looking at this skin as a Skinny reference, don't feel like you need to follow this pattern.
		Rendering a template directly to a zone (eg. this->addTemplateTo('navbar', 'navbar-brand')) is absolutely fine for most cases.

		This pattern of rendering to a specific zone and then appending that zone to another is a level of
		complexity only used in order to allow advanced customisation of this skin without having to create a sub-skin
		with custom templates.
		*/

		// append each menu to it's own zone
		$this->addHookTo('page-menu', 'pageMenu');
		$this->addHookTo('toolbox-menu', 'toolboxMenu');
		$this->addHookTo('user-menu', 'userMenu');
		// for convenience, add them all to a new wiki-menus zone
		$this->addZoneTo('wiki-menus', 'page-menu');
		$this->addZoneTo('wiki-menus', 'toolbox-menu');
		$this->addZoneTo('wiki-menus', 'user-menu');

		if($this->showNavBar){
			//add a top navigation bar
			$this->addTemplateTo('before:page', 'navbar');

			//the navbar toggler
			$this->addTemplateTo('navbar-toggler', 'navbar-toggler');
			//prepend it to the navbar zone
			$this->addZoneTo('prepend:navbar', 'navbar-toggler');

			//the wiki name ("brand" in bootstrap lingo)
			$this->addTemplateTo('navbar-brand', 'navbar-brand');
			//add it to the navbar zone
			$this->addZoneTo('navbar', 'navbar-brand');

			//process the MediaWiki:navbar message, which works more or less like MediaWiki:sidebar
			$items = $this->getTemplate()->processNavigationFromMessage('navbar');
			$this->addTemplateTo('navbar-menu', 'navbar-menu', array(
				'items'=>$items
			));
			//add it to the navbar zone
			$this->addZoneTo('navbar', 'navbar-menu');

			//render the navbar-right-menu.tpl.php template to the nav-bar-right-menu zone
			$this->addTemplateTo('navbar-right', 'navbar-right-menu');
			//append menus to the navbar-right-menu zone
			if(isset($this->languages)){
				$this->addHookTo('navbar-right-menu', 'languageMenu');
			}
			$this->addZoneTo('navbar-right-menu', 'wiki-menus');
			//append the navbar-right-menu zone to the navbar zone
			$this->addZoneTo('navbar', 'navbar-right');

			$this->addTemplateTo('navbar-search', 'navbar-search');

		}

		if($this->showSearch){
			$this->addTemplateTo('search', 'navbar-search');
			if( $this->searchZone==='navbar-center' ){
				$this->addTemplateTo('append:navbar', 'navbar-search');
			}
			else
			if( $this->searchZone==='navbar-right' ){
				//$this->addTemplateTo('append:navbar', 'navbar-search');
			}
		}

		$this->addTemplateTo('inline-search', 'inline-search', array(
			'label'=>$this->getMsg('search')->plain(),
			'search_button_label' => $this->getMsg('searcharticle')->plain(),
			'fulltext_button_label'	=> $this->getMsg('searchbutton')->plain()
		));


		if($this->showBreadcrumbs){
			$this->addZoneTo($this->breadcrumbsZone, 'breadcrumbs');
		}

		//allow for a full-width hero unit above the content
	  $this->addHookTo('before:lower-container', 'hero');


		//add a shared sidebar
		if($this->showSharedSidebar){
			$this->addTemplateTo('append:content-container', 'shared-sidebar' );
		}

		//add the usual mediawiki sidebar contewnt
		if($this->showStandardSidebar){

			if($this->standardSidebarZone==='navbar'){
				//append the template to #content-container
				$this->addTemplateTo('append:navbar', 'sidebar-in-navbar', array(
					'sections'=>$this->data['sidebar']
				));
			}else{

			}

		}


		if($this->showFancyToc){
			//add .has-toc class to #content-container if there is a toc on the page
			if(\Skinny::hasContent('toc')){
				$this->addHTMLTo('content-container.class', 'has-toc');
			}

			$this->addTemplateTo($this->fancyTocZone, 'fancy-toc');
		}


	}

	protected function hero(){
		$content = '';
		//Skinny can be used to content from the article into the
		if( \Skinny::hasContent('hero') ){
			$content = \Skinny::getContent('hero');
			return $this->renderTemplate('hero');
		}
	}


	/**
	 * Primary Navigation menus...
	 */

	protected function languageMenu(){
		$languages =  isset( $this->data['languages'] ) ?  $this->data['languages'] : false;
		$uls = isset( $this->data['uls'] ) ? $this->data['uls'] : false;
		if($uls || $languages){
			return $this->renderTemplate('language-selection', array(
				'languages' => $languages,
				'active' => $this->data['active_language'],
				'uls' => $uls
			));
		}
		return '';
	}

	protected function pageMenu(){
		return $this->renderTemplate('page-menu', array(
			'title'=>$this->getMsg( 'actions' )->plain(),
			'namespaces'=>$this->data['content_navigation']['namespaces'],
			'views'=>$this->data['content_navigation']['views'],
			'actions'=>$this->data['content_navigation']['actions'],
			'variants'=>$this->data['content_navigation']['variants']
		));
	}

	protected function userMenu(){
		$user = $this->getSkin()->getUser();
		if($this->userMenuTitleAsUsername===true){
			if($user->isLoggedIn()){
				$title = $user->getName();
			}else{
				//ideally this should display 'account' or something, but for now we'll leave it as the default
				$title = $this->getMsg('personaltools')->plain();
			}

		}else{
			$title = $this->getMsg('personaltools')->plain();
		}
		return $this->renderTemplate('user-menu', array(
			'items' => $this->data['personal_urls'],
			'title' => $title
		));
	}

	protected function toolboxMenu(){
		return $this->renderTemplate('toolbox-menu', array(
			'items' => $this->getTemplate()->getToolbox(),
			'title' => $this->getMsg('toolbox')->plain()
		));
	}

	/* Override SkinTemplate::makeListItem in order to inject icons */
	/*public function makeListItem( $key, $attributes, $options=array() ){
		if( isset($options['icon-class']) ){
			$class_name = $options['icon-class'];
			unset( $options['icon-class'] );
			$options['text-wrapper'] = array('tag' => 'span');
			$item = parent::makeListItem($key, $attributes, $options);
			//echo 'WOAH'.$item.'WOAH';
			$item = str_replace('<span', '<i class="'.$class_name.'"></i> <span', $item);
			return $item;
		}else{
			return parent::makeListItem($key, $attributes, $options);
		}
	}*/


	/*************************************************************************************************/

	/* A hacky way to move to Universal Language Selector out of the personal_urls
	as soon as it's added... */
	public function set($prop, $val){
		parent::set($prop, $val);

		//if Universal Language Selector is installed
		//pull it out of the user menu
		if($prop === 'personal_urls'){
			if( isset($this->data['personal_urls']['uls']) ){
				$this->data['uls'] = $this->data['personal_urls']['uls'];
				unset($this->data['personal_urls']['uls']);
			}
		}
	}

} // end of class
