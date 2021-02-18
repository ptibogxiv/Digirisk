///* Copyright (C) 2021 SuperAdmin
// *
// * This program is free software: you can redistribute it and/or modify
// * it under the terms of the GNU General Public License as published by
// * the Free Software Foundation, either version 3 of the License, or
// * (at your option) any later version.
// *
// * This program is distributed in the hope that it will be useful,
// * but WITHOUT ANY WARRANTY; without even the implied warranty of
// * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// * GNU General Public License for more details.
// *
// * You should have received a copy of the GNU General Public License
// * along with this program.  If not, see <https://www.gnu.org/licenses/>.
// *
// * Library javascript to enable Browser notifications
// */
//
//if (!defined('NOREQUIREUSER'))  define('NOREQUIREUSER', '1');
//if (!defined('NOREQUIREDB'))    define('NOREQUIREDB', '1');
//if (!defined('NOREQUIRESOC'))   define('NOREQUIRESOC', '1');
//if (!defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN', '1');
//if (!defined('NOCSRFCHECK'))    define('NOCSRFCHECK', 1);
//if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1);
//if (!defined('NOLOGIN'))        define('NOLOGIN', 1);
//if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', 1);
//if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', 1);
//if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX', '1');
//
//
///**
// * \file    digiriskdolibarr/js/digiriskdolibarr.js.php
// * \ingroup digiriskdolibarr
// * \brief   JavaScript file for module DigiriskDolibarr.
// */
//
//// Load Dolibarr environment
//$res = 0;
//// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
//if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
//// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
//$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
//while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
//if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
//if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/../main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/../main.inc.php";
//// Try main.inc.php using relative path
//if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
//if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
//if (!$res) die("Include of main fails");
//
//// Define js type
//header('Content-Type: application/javascript');
//// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
//// You can use CTRL+F5 to refresh your browser cache.
//if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
//else header('Cache-Control: no-cache');
//

/* Javascript library of module DigiriskDolibarr */

'use strict';

/**
 * @namespace EO_Framework_Init
 *
 * @author Eoxia <dev@eoxia.com>
 * @copyright 2015-2018 Eoxia
 */

/*

 */

if ( ! window.eoxiaJS ) {

	/**
	 * [eoxiaJS description]
	 *
	 * @memberof EO_Framework_Init
	 *
	 * @type {Object}
	 */
	window.eoxiaJS = {};

	/**
	 * [scriptsLoaded description]
	 *
	 * @memberof EO_Framework_Init
	 *
	 * @type {Boolean}
	 */
	window.eoxiaJS.scriptsLoaded = false;
}

if ( ! window.eoxiaJS.scriptsLoaded ) {

	/**
	 * [description]
	 *
	 * @memberof EO_Framework_Init
	 *
	 * @returns {void} [description]
	 */
	window.eoxiaJS.init = function() {
		window.eoxiaJS.load_list_script();
	};

	/**
	 * [description]
	 *
	 * @memberof EO_Framework_Init
	 *
	 * @returns {void} [description]
	 */
	window.eoxiaJS.load_list_script = function() {
		if ( ! window.eoxiaJS.scriptsLoaded ) {
			var key = undefined, slug = undefined;
			for ( key in window.eoxiaJS ) {

				if ( window.eoxiaJS[key].init ) {
					window.eoxiaJS[key].init();
				}

				for ( slug in window.eoxiaJS[key] ) {

					if ( window.eoxiaJS[key] && window.eoxiaJS[key][slug] && window.eoxiaJS[key][slug].init ) {
						window.eoxiaJS[key][slug].init();
					}

				}
			}

			window.eoxiaJS.scriptsLoaded = true;
		}
	};

	/**
	 * [description]
	 *
	 * @memberof EO_Framework_Init
	 *
	 * @returns {void} [description]
	 */
	window.eoxiaJS.refresh = function() {
		var key = undefined;
		var slug = undefined;
		for ( key in window.eoxiaJS ) {
			if ( window.eoxiaJS[key].refresh ) {
				window.eoxiaJS[key].refresh();
			}

			for ( slug in window.eoxiaJS[key] ) {

				if ( window.eoxiaJS[key] && window.eoxiaJS[key][slug] && window.eoxiaJS[key][slug].refresh ) {
					window.eoxiaJS[key][slug].refresh();
				}
			}
		}
	};

	/**
	 * [description]
	 *
	 * @memberof EO_Framework_Init
	 *
	 * @param  {void} cbName [description]
	 * @param  {void} cbArgs [description]
	 * @returns {void}        [description]
	 */
	window.eoxiaJS.cb = function( cbName, cbArgs ) {
		var key = undefined;
		var slug = undefined;
		for ( key in window.eoxiaJS ) {

			for ( slug in window.eoxiaJS[key] ) {

				if ( window.eoxiaJS[key] && window.eoxiaJS[key][slug] && window.eoxiaJS[key][slug][cbName] ) {
					window.eoxiaJS[key][slug][cbName](cbArgs);
				}
			}
		}
	};

	jQuery( document ).ready( window.eoxiaJS.init );
}

/**
 * Initialise l'objet "navigation" ainsi que la méthode "init" obligatoire pour la bibliothèque EoxiaJS.
 *
 * @since 6.0.0
 * @version 7.0.0
 */

window.eoxiaJS.navigation = {};

/**
 * La méthode appelée automatiquement par la bibliothèque EoxiaJS.
 *
 * @return {void}
 *
 * @since 6.0.0
 * @version 6.2.4
 */
window.eoxiaJS.navigation.init = function() {
	window.eoxiaJS.navigation.event();
};

/**
 * La méthode contenant tous les évènements pour la navigation.
 *
 * @since 6.0.0
 * @version 6.3.0
 *
 * @return {void}
 */
window.eoxiaJS.navigation.event = function() {
	//toggles
	jQuery( document ).on( 'click', '-wrap .navigation-container .unit-container .toggle-unit', window.eoxiaJS.navigation.switchToggle );
	jQuery( document ).on( 'click', '#newGroupment', window.eoxiaJS.navigation.switchToggle );
	jQuery( document ).on( 'click', '#newWorkunit', window.eoxiaJS.navigation.switchToggle );
	jQuery( document ).on( 'click', '-wrap .navigation-container .toolbar div', window.eoxiaJS.navigation.toggleAll );
	jQuery( document ).on( 'click', '#slider', window.eoxiaJS.navigation.setUnitActive );

	//menu button
	jQuery( document ).on( 'click', '#slider', window.eoxiaJS.redirect );
	jQuery( document ).on( 'click', '#newGroupment', window.eoxiaJS.redirect );
	jQuery( document ).on( 'click', '#newWorkunit', window.eoxiaJS.redirect );
	// tabs
	jQuery( document ).on( 'click', '#elementDocument', window.eoxiaJS.redirect );
	jQuery( document ).on( 'click', '#elementCard', window.eoxiaJS.redirect );
	jQuery( document ).on( 'click', '#elementAgenda', window.eoxiaJS.redirect );
	jQuery( document ).on( 'click', '#elementRisk', window.eoxiaJS.redirect );

	//modal
	jQuery( document ).on( 'click', '.modal-close', window.eoxiaJS.closeModal );
	jQuery( document ).on( 'click', '.modal-open', window.eoxiaJS.openModal );

	//action buttons
	jQuery( document ).on( 'click', '#actionButtonEdit', window.eoxiaJS.redirect );
	jQuery( document ).on( 'click', '#actionButtonCancelCreate', window.eoxiaJS.redirectAfterCancelCreate );


	//risks
	jQuery( document ).on( 'click', '.risk-edit', window.eoxiaJS.editRisk );
	jQuery( document ).on( 'click', '.risk-create', window.eoxiaJS.createRisk );
	jQuery( document ).on( 'click', '.risk-save', window.eoxiaJS.saveRisk );
	//dropdown cotation
	jQuery( document ).on( 'click', '.table.risk .dropdown-list li.dropdown-item:not(.open-popup), .wpeo-table.table-listing-risk .dropdown-list li.dropdown-item:not(.open-popup), .wpeo-table.table-risk .dropdown-list li.dropdown-item:not(.open-popup)', window.eoxiaJS.selectSeuil );

};


/**
 * Clique sur une des cotations simples.
 *
 * @param  {ClickEvent} event L'état du clic.
 * @return {void}
 *
 * @since 6.0.0
 * @version 7.0.0
 */
window.eoxiaJS.selectSeuil = function( event ) {
	var element      = jQuery( this );
	var riskID       = element.data( 'id' );
	var seuil        = element.data( 'seuil' );
	var variableID   = element.data( 'variable-id' );
	var evaluationID = element.data( 'evaluation-id' );
	var evaluationMethod = element.data( 'evaluation-method' );

	jQuery( '.risk-row.edit[data-id="' + riskID + '"] .cotation-container .dropdown-toggle.cotation span' ).text( jQuery( this ).text() );
	jQuery( '.risk-row.edit[data-id="' + riskID + '"] .cotation-container .dropdown-toggle.cotation' ).attr( 'data-scale', seuil );

	if ( variableID && seuil ) {
		window.eoxiaJS.updateInputVariables( riskID, evaluationID, variableID, seuil, evaluationMethod );
	}
};

window.eoxiaJS.updateInputVariables = function( riskID, evaluationID, variableID, value, evaluationMethod, field ) {

	$('#cotationInput').attr('value', evaluationID)
	$('#cotationMethod').attr('value', evaluationMethod)

	$('#cotationSpan').text(evaluationID)
	let scale = 0

	// faire fonction scale
	switch (true) {
		case (evaluationID < 48):
			scale = 1
			break;
		case (evaluationID < 51):
			scale = 2
			break;
		case (evaluationID < 79):
			scale = 3
			break;
		case (evaluationID < 101):
			scale = 4
			break;
		case (evaluationID === 0):
			scale = 1
			break;
	}

	$('#cotationSpan').attr('data-scale', scale)

};
/**
 * Gestion du toggle dans la navigation.
 *
 * @param  {MouseEvent} event Les attributs lors du clic.
 * @return {void}
 */
window.eoxiaJS.navigation.switchToggle = function( event ) {
	event.preventDefault();

	var MENU = localStorage.menu
	if (MENU == null || MENU == '') {
		MENU = new Set()
	} else {
		MENU = JSON.parse(MENU)
		MENU = new Set(MENU)
	}

	if ( jQuery( this ).find( '.toggle-icon' ).hasClass( 'fa-chevron-down' ) ) {

		jQuery(this).find( '.toggle-icon' ).removeClass('fa-chevron-down').addClass('fa-chevron-right');
		var idUnToggled = jQuery(this).closest('.unit').attr('id').split('unit')[1]
		jQuery(this).closest('.unit').removeClass('toggled');

		MENU.delete(idUnToggled)
		localStorage.setItem('menu',  JSON.stringify(Array.from(MENU.keys())))

	} else {

		jQuery(this).find( '.toggle-icon' ).removeClass('fa-chevron-right').addClass('fa-chevron-down');
		jQuery(this).closest('.unit').addClass('toggled');

		var idToggled = jQuery(this).closest('.unit').attr('id').split('unit')[1]
		MENU.add(idToggled)
		localStorage.setItem('menu',  JSON.stringify(Array.from(MENU.keys())))
	}

};

/**
 * Déplies ou replies tous les éléments enfants
 *
 * @param  {MouseEvent} event Les attributs lors du clic
 * @return {void}
 */
window.eoxiaJS.navigation.toggleAll = function( event ) {
	event.preventDefault();

	if ( jQuery( this ).hasClass( 'toggle-plus' ) ) {

		jQuery( '-wrap .navigation-container .workunit-list .unit .toggle-icon').removeClass( 'fa-chevron-right').addClass( 'fa-chevron-down' );
		jQuery( '-wrap .navigation-container .workunit-list .unit' ).addClass( 'toggled' );

		// local storage add all
		let MENU = $( '-wrap .navigation-container .workunit-list .unit .title' ).get().map( v => v.attributes.value.value)
		localStorage.setItem('menu', JSON.stringify(Object.values(MENU)) )

	}

	if ( jQuery( this ).hasClass( 'toggle-minus' ) ) {
		jQuery( '-wrap .navigation-container .workunit-list .unit.toggled' ).removeClass( 'toggled' );
		jQuery( '-wrap .navigation-container .workunit-list .unit .toggle-icon').addClass( 'fa-chevron-right').removeClass( 'fa-chevron-down' );

		// local storage delete all
		let emptyMenu = new Set('0')
		localStorage.setItem('menu', JSON.stringify(Object.values(emptyMenu)) )

	}
};


/**
 * Ajout la classe 'active' à l'élément.
 *
 * @param  {MouseEvent} event Les attributs lors du clic.
 * @return {void}
 */
window.eoxiaJS.navigation.setUnitActive = function( event ) {

	jQuery( '-wrap .navigation-container .unit.active' ).removeClass( 'active' );
	let id = $(this).attr('value')

	jQuery( this ).closest( '.unit' ).addClass( 'active' );
	jQuery( this ).closest( '.unit' ).attr( 'value', id );

};

window.eoxiaJS.redirect = function( event ) {

	var params = new window.URLSearchParams(window.location.search);
	var id = $(params.get('id'))

	//get ID from div selected in left menu
	history.pushState({ path:  document.URL}, '', this.href)
	//change URL without refresh

	//empty and fill object card
	$('#cardContent').empty()
	//$('#cardContent').attr('value', id)
	$('#cardContent').load( document.URL + ' #cardContent' , id);

	return false;
};

window.eoxiaJS.redirectAfterCancelCreate = function( event ) {

	var params = new window.URLSearchParams(window.location.search);
	let id = $(params.get('id'))

	//id of parent object if cancel create
	var parentID = document.URL.split("fk_parent=")[1]
	var URL = document.URL.split("?action")[0]
	if (parentID > 0) {
		//get ID from div selected in left menu
		history.pushState({ path:  document.URL}, '', URL  + '?id=' + parentID)
		//change URL without refresh
	} else {
		history.pushState({ path:  document.URL}, '', URL)
	}

	jQuery( '-wrap .navigation-container .unit.active' ).removeClass( 'active' );
	jQuery( `#scores[value="${parentID}"]` ).closest( '.unit' ).addClass( 'active' );
	jQuery( '#scores' ).closest( '.unit' ).attr( 'value', parentID );

	//empty and fill object card
	$('#cardContent').empty()
	$('#cardContent').attr('value', id)
	$('#cardContent').load( document.URL + ' #cardContent' , id);

	return false;

};

// Onglet risques

window.eoxiaJS.closeModal = function ( event ) {
	$('.modal-active').removeClass('modal-active')
}

window.eoxiaJS.openModal = function ( event ) {

	let idSelected = $(this).attr('value')

	$('.modal-active').removeClass('modal-active')
	console.log(this)
	if ($(this).hasClass('digirisk-evaluation')) {
		$('#digirisk_evaluation_modal'+idSelected).addClass('modal-active')
	}
	else {
		$('#cotation_modal'+idSelected).addClass('modal-active')

	}
}

window.eoxiaJS.createRisk = function ( event ) {

	var description = $('#riskComment').val()
	var descriptionPost = ''
	if (description !== '') {
		descriptionPost = '&riskComment=' + description
	}
	console.log($('#cotationMethod'))
	var method = $('#cotationMethod').val()
	var methodPost = ''
	if (method !== '') {
		methodPost = '&cotationMethod=' + method
	}

	var cotation = $('#cotationSpan').text()
	var cotationPost = ''
	if (cotation !== 0) {
		cotationPost = '&cotation=' + cotation
	}
	console.log(document.URL + '&action=add' + cotationPost + descriptionPost + methodPost )

	$('.main-table').load( document.URL + '&action=add' + cotationPost + descriptionPost + methodPost + ' .main-table')

}

window.eoxiaJS.editRisk = function ( event ) {

	let editedRiskId = $(this).attr('value')
	$('#risk_row_'+editedRiskId).empty()
	$('#risk_row_'+editedRiskId).load( document.URL + '&action=editRisk' + editedRiskId + ' #risk_row_'+editedRiskId+' > div')

}

window.eoxiaJS.saveRisk = function ( event ) {

	let editedRiskId = $(this).attr('value')

	var description = $('#riskComment'+editedRiskId).val()
	var descriptionPost = ''
	if (description !== '') {
		descriptionPost = '&riskComment=' + description
	}

	var cotation = $('#cotation'+editedRiskId).val()
	var cotationPost = ''
	if (cotation !== 0) {
		cotationPost = '&cotation=' + cotation
	}
	$('#risk_row_'+editedRiskId).empty()
	$('#risk_row_'+editedRiskId).load( document.URL + '&action=saveRisk' + editedRiskId + cotationPost + descriptionPost + ' #risk_row_'+editedRiskId+' > div', function() {
		$.getScript('digiriskdolibarr.js.php')
	});
}

// A mettre dans un fichier dropdown
/**
 * [dropdown description]
 *
 * @memberof EO_Framework_Dropdown
 *
 * @type {Object}
 */
window.eoxiaJS.dropdown = {};

/**
 * [description]
 *
 * @memberof EO_Framework_Dropdown
 *
 * @returns {void} [description]
 */
window.eoxiaJS.dropdown.init = function() {
	window.eoxiaJS.dropdown.event();
};

/**
 * [description]
 *
 * @memberof EO_Framework_Dropdown
 *
 * @returns {void} [description]
 */
window.eoxiaJS.dropdown.event = function() {
	jQuery( document ).on( 'keyup', window.eoxiaJS.dropdown.keyup );
	jQuery( document ).on( 'click', '.wpeo-dropdown:not(.dropdown-active) .dropdown-toggle:not(.disabled)', window.eoxiaJS.dropdown.open );
	jQuery( document ).on( 'click', '.wpeo-dropdown.dropdown-active .dropdown-content', function(e) { e.stopPropagation() } );
	jQuery( document ).on( 'click', '.wpeo-dropdown.dropdown-active:not(.dropdown-force-display) .dropdown-content .dropdown-item', window.eoxiaJS.dropdown.close  );
	jQuery( document ).on( 'click', '.wpeo-dropdown.dropdown-active', function ( e ) { window.eoxiaJS.dropdown.close( e ); e.stopPropagation(); } );
	jQuery( document ).on( 'click', 'body', window.eoxiaJS.dropdown.close );
};

/**
 * [description]
 *
 * @memberof EO_Framework_Dropdown
 *
 * @param  {void} event [description]
 * @returns {void}       [description]
 */
window.eoxiaJS.dropdown.keyup = function( event ) {
	if ( 27 === event.keyCode ) {
		window.eoxiaJS.dropdown.close();
	}
};

/**
 * [description]
 *
 * @memberof EO_Framework_Dropdown
 *
 * @param  {void} event [description]
 * @returns {void}       [description]
 */
window.eoxiaJS.dropdown.open = function( event ) {
	var triggeredElement = jQuery( this );
	var angleElement = triggeredElement.find('[data-fa-i2svg]');
	var callbackData = {};
	var key = undefined;

	window.eoxiaJS.dropdown.close( event, jQuery( this ) );

	if ( triggeredElement.attr( 'data-action' ) ) {
		window.eoxiaJS.loader.display( triggeredElement );

		triggeredElement.get_data( function( data ) {
			for ( key in callbackData ) {
				if ( ! data[key] ) {
					data[key] = callbackData[key];
				}
			}

			window.eoxiaJS.request.send( triggeredElement, data, function( element, response ) {
				triggeredElement.closest( '.wpeo-dropdown' ).find( '.dropdown-content' ).html( response.data.view );

				triggeredElement.closest( '.wpeo-dropdown' ).addClass( 'dropdown-active' );

				/* Toggle Button Icon */
				if ( angleElement ) {
					window.eoxiaJS.dropdown.toggleAngleClass( angleElement );
				}
			} );
		} );
	} else {
		triggeredElement.closest( '.wpeo-dropdown' ).addClass( 'dropdown-active' );

		/* Toggle Button Icon */
		if ( angleElement ) {
			window.eoxiaJS.dropdown.toggleAngleClass( angleElement );
		}
	}

	event.stopPropagation();
};

/**
 * [description]
 *
 * @memberof EO_Framework_Dropdown
 *
 * @param  {void} event [description]
 * @returns {void}       [description]
 */
window.eoxiaJS.dropdown.close = function( event ) {
	var _element = jQuery( this );
	jQuery( '.wpeo-dropdown.dropdown-active:not(.no-close)' ).each( function() {
		var toggle = jQuery( this );
		var triggerObj = {
			close: true
		};

		_element.trigger( 'dropdown-before-close', [ toggle, _element, triggerObj ] );

		if ( triggerObj.close ) {
			toggle.removeClass( 'dropdown-active' );

			/* Toggle Button Icon */
			var angleElement = jQuery( this ).find('.dropdown-toggle').find('[data-fa-i2svg]');
			if ( angleElement ) {
				window.eoxiaJS.dropdown.toggleAngleClass( angleElement );
			}
		} else {
			return;
		}
	});
};

/**
 * [description]
 *
 * @memberof EO_Framework_Dropdown
 *
 * @param  {void} button [description]
 * @returns {void}        [description]
 */
window.eoxiaJS.dropdown.toggleAngleClass = function( button ) {
	if ( button.hasClass('fa-caret-down') || button.hasClass('fa-caret-up') ) {
		button.toggleClass('fa-caret-down').toggleClass('fa-caret-up');
	}
	else if ( button.hasClass('fa-caret-circle-down') || button.hasClass('fa-caret-circle-up') ) {
		button.toggleClass('fa-caret-circle-down').toggleClass('fa-caret-circle-up');
	}
	else if ( button.hasClass('fa-angle-down') || button.hasClass('fa-angle-up') ) {
		button.toggleClass('fa-angle-down').toggleClass('fa-angle-up');
	}
	else if ( button.hasClass('fa-chevron-circle-down') || button.hasClass('fa-chevron-circle-up') ) {
		button.toggleClass('fa-chevron-circle-down').toggleClass('fa-chevron-circle-up');
	}
}

/**
 *
 *
 * Méthode Evarisk Cotation
 *
 */


/**
 * Initialise l'objet "evaluationMethodEvarisk" ainsi que la méthode "init" obligatoire pour la bibliothèque EoxiaJS.
 *
 * @since 1.0
 * @version 6.2.6.0
 */
window.eoxiaJS.evaluationMethodEvarisk = {};

window.eoxiaJS.evaluationMethodEvarisk.init = function() {
	window.eoxiaJS.evaluationMethodEvarisk.event();
};

window.eoxiaJS.evaluationMethodEvarisk.event = function() {
	jQuery( document ).on( 'click', '.wpeo-modal.evaluation-method .wpeo-table.evaluation-method .table-cell.can-select', window.eoxiaJS.evaluationMethodEvarisk.selectSeuil );
	jQuery( document ).on( 'click', '.wpeo-modal.evaluation-method .wpeo-button.button-main', window.eoxiaJS.evaluationMethodEvarisk.save );
	jQuery( document ).on( 'click', '.wpeo-modal.evaluation-method .wpeo-button.button-secondary', window.eoxiaJS.evaluationMethodEvarisk.close_modal );
};

window.eoxiaJS.evaluationMethodEvarisk.selectSeuil = function( event ) {
	jQuery( this ).closest( '.table-row' ).find( '.active' ).removeClass( 'active' );
	jQuery( this ).addClass( 'active' );

	var element      = jQuery( this );
	var riskID       = element.data( 'id' );
	var seuil        = element.data( 'seuil' );
	var variableID   = element.data( 'variable-id' );
	var evaluationID = element.data( 'evaluation-id' );

	window.eoxiaJS.updateInputVariables( riskID, evaluationID, variableID, seuil, jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' textarea' ) );

	var data = {
		action: 'get_scale',
		method_evaluation_id: evaluationID,
		variables: jQuery( '.wpeo-modal.modal-risk-' + riskID + ' textarea' ).val()
	};

	var currentVal    = JSON.parse(jQuery( '.wpeo-modal.modal-risk-' + riskID + ' textarea' ).val());
	var canGetDetails = true;
	for (var key in currentVal) {
		if (currentVal[key] == '') {
			canGetDetails = false;
		}
	}

	if ( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .table-cell.active' ).length == 5 ) {
		if ( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).length ) {
			window.eoxiaJS.loader.display( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ) );
			jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).addClass( 'disabled' );
		}
		jQuery.post( window.ajaxurl, data, function( response ) {
			if ( response.data.details ) {
				if ( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).length ) {
					window.eoxiaJS.loader.remove( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ) );
					jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).removeClass( 'disabled' );
				}
				jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .cotation' ).attr( 'data-scale', response.data.details.scale );
				jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .cotation span' ).text( response.data.details.equivalence );
				jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .wpeo-button.button-disable' ).removeClass( 'button-disable' ).addClass( 'button-main' );
			}
		} );
	}
};


window.eoxiaJS.evaluationMethodEvarisk.save = function( event ) {
	var riskID       = jQuery( this ).data( 'id' );
	var evaluationID = jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .digi-method-evaluation-id' ).val();
	var value        = jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' textarea' ).val();

	jQuery( '.risk-row.edit[data-id="' + riskID + '"] textarea[name="evaluation_variables"]' ).val( value );
	jQuery( '.risk-row.edit[data-id="' + riskID + '"] input[name="evaluation_method_id"]' ).val( evaluationID );

	// On met à jour l'affichage de la cotation.
	jQuery( '.risk-row.edit[data-id="' + riskID + '"] .cotation:first' ).attr( 'data-scale', jQuery( '.wpeo-modal.modal-risk-' + riskID + ' .cotation' ).attr( 'data-scale' ) );
	jQuery( '.risk-row.edit[data-id="' + riskID + '"] .cotation:first span' ).text( jQuery( '.wpeo-modal.modal-risk-' + riskID + ' .cotation span' ).text() );

	window.eoxiaJS.evaluationMethodEvarisk.close_modal( undefined, riskID );
};

window.eoxiaJS.evaluationMethodEvarisk.close_modal = function( event, riskID ) {
	if ( ! riskID ) {
		riskID = jQuery( this ).data( 'id' );
	}

	jQuery( '.wpeo-modal.modal-active .modal-close' ).click();
};

window.eoxiaJS.evaluationMethodEvarisk.fillVariables = function( element ) {
	element.attr( 'data-variables', element.closest( 'td' ).find( 'textarea[name="evaluation_variables"]' ).val() );
}

/**
 * Initialise l'objet "evaluationMethodEvarisk" ainsi que la méthode "init" obligatoire pour la bibliothèque EoxiaJS.
 *
 * @since 1.0
 * @version 6.2.6.0
 */
window.eoxiaJS.evaluationMethodEvarisk = {};

window.eoxiaJS.evaluationMethodEvarisk.init = function() {
	window.eoxiaJS.evaluationMethodEvarisk.event();
};

window.eoxiaJS.evaluationMethodEvarisk.event = function() {
	jQuery( document ).on( 'click', '.wpeo-modal.evaluation-method .wpeo-table.evaluation-method .table-cell.can-select', window.eoxiaJS.evaluationMethodEvarisk.selectSeuil );
	jQuery( document ).on( 'click', '.wpeo-modal.evaluation-method .wpeo-button.button-main', window.eoxiaJS.evaluationMethodEvarisk.save );
	jQuery( document ).on( 'click', '.wpeo-modal.evaluation-method .wpeo-button.button-secondary', window.eoxiaJS.evaluationMethodEvarisk.close_modal );
};

window.eoxiaJS.evaluationMethodEvarisk.selectSeuil = function( event ) {
	jQuery( this ).closest( '.table-row' ).find( '.active' ).removeClass( 'active' );
	jQuery( this ).addClass( 'active' );

	var element      = jQuery( this );
	var riskID       = element.data( 'id' );
	var seuil        = element.data( 'seuil' );
	var variableID   = element.data( 'variable-id' );
	var evaluationID = element.data( 'evaluation-id' );

	window.eoxiaJS.evaluationMethodEvarisk.updateInputVariables( riskID, evaluationID, variableID, seuil, jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' textarea' ) );

	var data = {
		action: 'get_scale',
		method_evaluation_id: evaluationID,
		variables: jQuery( '.wpeo-modal.modal-risk-' + riskID + ' textarea' ).val()
	};

	var currentVal    = JSON.parse(jQuery( '.wpeo-modal.modal-risk-' + riskID + ' textarea' ).val());
	var canGetDetails = true;
	for (var key in currentVal) {
		if (currentVal[key] == '') {
			canGetDetails = false;
		}
	}
	console.log(riskID)
	if ( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .table-cell.active' ).length == 5 ) {
		if ( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).length ) {
			//window.eoxiaJS.loader.display( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ) );
			jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).addClass( 'disabled' );
		}
		//jQuery.post( window.ajaxurl, data, function( response ) {
		//	if ( response.data.details ) {
		//		if ( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).length ) {
		//			window.eoxiaJS.loader.remove( jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ) );
		//			jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .button-main' ).removeClass( 'disabled' );
		//		}
		//		jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .cotation' ).attr( 'data-scale', response.data.details.scale );
		//		jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .cotation span' ).text( response.data.details.equivalence );
		//		jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .wpeo-button.button-disable' ).removeClass( 'button-disable' ).addClass( 'button-main' );
		//	}
		//} );
	}
};
window.eoxiaJS.evaluationMethodEvarisk.updateInputVariables = function( riskID, evaluationID, variableID, value, field ) {
	var updateEvaluationID = false;

	console.log('riskID= ' + riskID)
	console.log('evaluationID= ' + evaluationID)
	console.log('variableID= ' + variableID)
	console.log('value= ' + value)
	console.log('field= ' + field)
	//field = jQuery( '.risk-row.edit[data-id="' + riskID + '"] textarea[name="evaluation_variables"]' );
	console.log(field)

	var currentVal = JSON.parse(field.val());
	//field.text(value)
	currentVal[variableID] = value;

	field.val( JSON.stringify( currentVal ) );
	console.log(Object.keys(currentVal).length)
	if ( updateEvaluationID ) {
		jQuery( '.risk-row.edit[data-id="' + riskID + '"] input[name="evaluation_method_id"]' ).val( evaluationID );
	}
	// Rend le bouton "active".
	if (Object.keys(currentVal).length === 5) {
	let cotationBeforeAdapt = currentVal[0] * currentVal[1] * currentVal[2] * currentVal[3] * currentVal[4]
	console.log(cotationBeforeAdapt)
			jQuery( '.wpeo-button.cotation-save.button-disable' ).removeClass( 'button-disable' );
	}
};

window.eoxiaJS.evaluationMethodEvarisk.save = function( event ) {
	var riskID       = jQuery( this ).data( 'id' );
	var evaluationID = jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' .digi-method-evaluation-id' ).val();
	var value        = jQuery( '.wpeo-modal.modal-active.modal-risk-' + riskID + ' textarea' ).val();

	jQuery( '.risk-row.edit[data-id="' + riskID + '"] textarea[name="evaluation_variables"]' ).val( value );
	jQuery( '.risk-row.edit[data-id="' + riskID + '"] input[name="evaluation_method_id"]' ).val( evaluationID );

	// On met à jour l'affichage de la cotation.
	jQuery( '.risk-row.edit[data-id="' + riskID + '"] .cotation:first' ).attr( 'data-scale', jQuery( '.wpeo-modal.modal-risk-' + riskID + ' .cotation' ).attr( 'data-scale' ) );
	jQuery( '.risk-row.edit[data-id="' + riskID + '"] .cotation:first span' ).text( jQuery( '.wpeo-modal.modal-risk-' + riskID + ' .cotation span' ).text() );

	window.eoxiaJS.evaluationMethodEvarisk.close_modal( undefined, riskID );
};

window.eoxiaJS.evaluationMethodEvarisk.close_modal = function( event, riskID ) {
	if ( ! riskID ) {
		riskID = jQuery( this ).data( 'id' );
	}

	jQuery( '.wpeo-modal.modal-active .modal-close' ).click();
};

window.eoxiaJS.evaluationMethodEvarisk.fillVariables = function( element ) {
	element.attr( 'data-variables', element.closest( 'td' ).find( 'textarea[name="evaluation_variables"]' ).val() );
}
