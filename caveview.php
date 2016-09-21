<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.caveview
 *
 * @copyright   Copyright (C) 2016 Angus Sawyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * CaveView plugin class.
 */
class PlgContentCaveView extends JPlugin
{

	/**
	 * Plugin that displays 3D cave surveys
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @return  boolean	True on success.
	 */

	public function onContentPrepare( $context, &$row, &$params, $page = 0 ) {

		// Don't run this plugin when the content is being indexed
		if ( $context == 'com_finder.indexer' ) {

			return true;

		}

		if ( is_object( $row ) ) {

			return $this->_addSurvey( $row->text, $params );

		}

		return $this->_addSurvey( $row, $params );
	}

	/**
	 * add survey
	 *
	 * @param   string  &$text    The content to be searched
	 * @param   mixed   &$params  Additional parameters.
	 *
	 * @return  boolean  True on success.
	 */

	protected function _addSurvey( &$text, &$params ) {

		if (JString::strpos( $text, '{caveview' ) !== false )	{

			$document = jFactory::getDocument();
	
			$document->addStyleSheet( '/media/plg_caveview/css/caveview.css' );
			$document->addScript( '/media/plg_caveview/js/CaveView.js' );

			$survey = "";

			preg_match( '/\{caveview(?:\|([a-z]+)="(.+)")*\}/', $text, $matches );

			for ( $i = 1 ; $i < count( $matches ) - 1 ; $i += 2 ) {

				switch ( $matches[ $i ] ) {

					case 'survey' :

						$survey = $matches[ $i + 1 ];
						break;

				}

			}

			$surveyPath = $this->params->get( 'Survey Path' ) . '/';
			$terrainPath = $this->params->get( 'Terrain Path' ) . '/';

			$document->addScriptDeclaration( "
				jQuery( function () {

					CV.setEnvironment( { terrainDirectory: '$terrainPath', surveyDirectory: '$surveyPath', home: '/media/plg_caveview/' } );

					CV.UI.init( 'scene' );
					CV.UI.loadCave( '$survey' );
				});
			");

			$text = preg_replace( '/\{caveview.*\}/', '<div id="scene" style="overflow: hidden;"></div>', $text );

			return true;

		}

		return true;

	}

}
