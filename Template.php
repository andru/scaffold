<?php
/**
 * @todo document
 * @ingroup Skins
 */
class ScaffoldTemplate extends \Skinny\Template {

	protected $showLanguages = false;
	protected $languages = array();

	protected $showTagline = false;

	protected function initialize(){

		$this->addTemplatePath( dirname(__FILE__).'/templates' );

		if(isset($this->languages)){
			$names = Language::getLanguageNames();
			$languages = array();
			foreach($this->languages as $code){
				if(array_key_exists($code, $names)){
					$languages[$code] = $names[$code];
				}
			}
			$this->data['languages'] = $languages;
			$context = RequestContext::getMain();
			$active = $context->getLanguage();
			$this->data['active_language'] = array(
				'name'=>$active->fetchLanguageName( $active->getCode() ),
				'code'=>$active->getCode()
			);
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
		if($this->userMenuTitleAsUsername){
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
			'items' => $this->getToolbox(),
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
