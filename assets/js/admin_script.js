( function( $ ){
	const { __, _x, _n, _nx, sprintf } = wp.i18n

	$('#wpwrap').addClass('wpwrap--has-mywp-admin');

	function save_settings( form ){
		var form_data = process_form_data( form )
		enable_loading( form )
		disable_form( form )
		$.ajax( {
			url         : mywp_custom_login.save_settings_api_url,
			beforeSend  : function( xhr ){ xhr.setRequestHeader( 'X-WP-Nonce', mywp_custom_login.nonce ) },
			method      : 'POST',
			data        : form_data,
			processData : false,
			contentType : false,
			cache       : false,
			success     : function ( response ){
				console.log( response )
				disable_loading( form )
				enable_form( form )
				display_notice( response.code, response.message )
			},
			error       : function ( response ) {
				console.log( response )
				disable_loading( form )
				enable_form( form )
				display_notice( response.responseJSON.code, response.responseJSON.message, true )
			}
		} )
	}

	function process_form_data( form ){
		var form_data = new FormData( form );
		$( form ).find( 'input[type=checkbox]' ).each( function(){
			form_data.append( $( this ).attr( 'name' ), ( $( this ).is( ':checked' ) ) ? 'true' : 'false' )
		});
		return form_data
	}

	function display_notice( code, message, is_dismissible = false ){
		var notice  = $( '<div class="notice"></div>' );
		var message = $( '<p><strong>'+message+'</strong></p>' );
		var close   = $( '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' );
		notice.append( message )
		notice.addClass( ( 'success' === code ) ? 'notice-success' : 'notice-error' )
		if( is_dismissible ){
			notice.addClass( 'is-dismissible' )
			notice.append( close )
			close.on( 'click', function(){ notice.remove() } )
		}else{
			setTimeout( function(){ notice.remove() }, 5555 )
		}
		$( '#wpbody' ).prepend( notice )
	}

	/** disable_form
	 *  disable all form input and buttons
	 */
	function disable_form( form ){
		$( form ).find( 'button,input,textarea,select' ).prop( 'disabled', true )
	}

	/** enable_form
	 *  enable all form input and buttons
	 */
	function enable_form( form ){
		$( form ).find( 'button,input,textarea,select' ).prop( 'disabled', false )
	}

	/** enable_loading
	 *  display loading box
	 */
	function enable_loading( form ){
		$( form ).addClass( 'loading' )
	}

	/** disable_loading
	 *  hide loading box
	 */
	function disable_loading( form ){
		$( form ).removeClass( 'loading' )
	}

	//init input
	console.log( $( '.media-field' ) )
	$( '.media-field' ).each( function( node ){
		var input = $( this ).children( 'input' )
		var wpMedia = wp.media( {
			title: __( 'Choose Image', 'mywp-custom-login' ),
			button: { text: __( 'Select Image', 'mywp-custom-login' ) },
			multiple: false,
		} ).on( 'select', function (){
			input.val( wpMedia.state().get( "selection" ).first().toJSON().url )
			input.trigger( 'change' )
		});

		$( this ).children( 'button.upload-button' ).on( 'click', function (e) {
			wpMedia.open();
		} );

		$( this ).children( 'button.clear-button' ).on( 'click', function (e) {
			input.val( '' )
			input.trigger( 'change' )
		} );
	} );

	$('.color-picker-field').wpColorPicker();

	//trigger submit
	$( '.submit-form' ).on( 'click', function( e ){
		$( this ).parents( 'form' ).submit()
	} )
	$( 'form.mywp-custom-login__settings_form' ).on( 'submit', function( e ){
		e.preventDefault()
		save_settings( this )
	} )

} )( jQuery );