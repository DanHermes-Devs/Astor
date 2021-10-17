/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import icons from './icons';
import './styles/editor.scss';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { withInstanceId } = wp.compose;
const { Component, Fragment } = wp.element;
const { BaseControl, TabPanel } = wp.components;

class ResponsiveBaseControl extends Component {
	constructor() {
		super( ...arguments );

		this.saveMeta = this.saveMeta.bind( this );
	}

	saveMeta() {
		const meta = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' );
		const block = wp.data.select( 'core/block-editor' ).getBlock( this.props.clientId );
		let dimensions = {};

		if ( typeof this.props.attributes.coblocks !== 'undefined' && typeof this.props.attributes.coblocks.id !== 'undefined' ) {
			const id = this.props.name.split( '/' ).join( '-' ) + '-' + this.props.attributes.coblocks.id;
			const height = {
				height: block.attributes[ this.props.type ],
				heightTablet: block.attributes[ this.props.type + 'Tablet' ],
				heightMobile: block.attributes[ this.props.type + 'Mobile' ],
			};

			if ( typeof meta._coblocks_responsive_height === 'undefined' || ( typeof meta._coblocks_responsive_height !== 'undefined' && meta._coblocks_responsive_height === '' ) ) {
				dimensions = {};
			} else {
				dimensions = JSON.parse( meta._coblocks_responsive_height );
			}

			if ( typeof dimensions[ id ] === 'undefined' ) {
				dimensions[ id ] = {};
				dimensions[ id ][ this.props.type ] = {};
			} else {
				if ( typeof dimensions[ id ][ this.props.type ] === 'undefined' ) {
					dimensions[ id ][ this.props.type ] = {};
				}
			}

			dimensions[ id ][ this.props.type ] = height;

			// Save values to metadata.
			wp.data.dispatch( 'core/editor' ).editPost( {
				meta: {
					_coblocks_responsive_height: JSON.stringify( dimensions ),
				},
			} );
		}
	}

	render() {
		const {
			label = __( 'Height' ),
			height,
			heightTablet,
			heightMobile,
			onChange,
			onChangeTablet,
			onChangeMobile,
			min = 10,
			max = 1000,
			step = 1,
		} = this.props;

		const onSelect = ( tabName ) => {
			let selected = 'desktop';

			switch ( tabName ) {
				case 'desktop':
					selected = 'tablet';
					break;
				case 'tablet':
					selected = 'mobile';
					break;
				case 'mobile':
					selected = 'desktop';
					break;
				default:
					break;
			}

			//Reset z-index
			const buttons = document.getElementsByClassName( `components-coblocks-dimensions-control__mobile-controls-item--${ this.props.type }` );

			for ( let i = 0; i < buttons.length; i++ ) {
				buttons[ i ].style.display = 'none';
			}
			if ( tabName === 'default' ) {
				const button = document.getElementsByClassName( `components-coblocks-dimensions-control__mobile-controls-item-${ this.props.type }--tablet` );
				button[ 0 ].click();
			} else {
				const button = document.getElementsByClassName( `components-coblocks-dimensions-control__mobile-controls-item-${ this.props.type }--${ selected }` );
				button[ 0 ].style.display = 'block';
			}
		};

		const classes = classnames(
			'components-base-control',
			'components-coblocks-dimensions-control',
			'components-coblocks-responsive-base-control', {
			}
		);

		return (
			<Fragment>
				<div className={ classes }>
					<Fragment>
						<span className="components-base-control__label">{ label }</span>
						<TabPanel
							className="components-coblocks-dimensions-control__mobile-controls"
							activeClass="is-active"
							initialTabName="default"
							onSelect={ onSelect }
							tabs={ [
								{
									name: 'default',
									title: icons.desktopChrome,
									className: `components-coblocks-dimensions-control__mobile-controls-item components-coblocks-dimensions-control__mobile-controls-item--${ this.props.type } components-button components-coblocks-dimensions-control__mobile-controls-item--default components-coblocks-dimensions-control__mobile-controls-item-${ this.props.type }--default`,
								},
								{
									name: 'desktop',
									title: icons.mobile,
									className: `components-coblocks-dimensions-control__mobile-controls-item components-coblocks-dimensions-control__mobile-controls-item--${ this.props.type } components-button components-coblocks-dimensions-control__mobile-controls-item--desktop components-coblocks-dimensions-control__mobile-controls-item-${ this.props.type }--desktop`,
								},
								{
									name: 'tablet',
									title: icons.desktopChrome,
									className: `components-coblocks-dimensions-control__mobile-controls-item components-coblocks-dimensions-control__mobile-controls-item--${ this.props.type } components-button components-coblocks-dimensions-control__mobile-controls-item--tablet components-coblocks-dimensions-control__mobile-controls-item-${ this.props.type }--tablet`,
								},
								{
									name: 'mobile',
									title: icons.tablet,
									className: `components-coblocks-dimensions-control__mobile-controls-item components-coblocks-dimensions-control__mobile-controls-item--${ this.props.type } components-button components-coblocks-dimensions-control__mobile-controls-item--mobile components-coblocks-dimensions-control__mobile-controls-item-${ this.props.type }--mobile`,
								},
							] }>
							{
								( tab ) => {
									if ( 'mobile' === tab.name ) {
										return (
											<Fragment>
												<div className="components-coblocks-dimensions-control__inputs component-coblocks-is-mobile">
													<BaseControl>
														<input
															type="number"
															onChange={ ( newValue ) => {
																onChangeMobile( newValue );
																this.saveMeta();
															} }
															value={ heightMobile ? heightMobile : '' }
															min={ min }
															step={ step }
															max={ max }
														/>
													</BaseControl>
												</div>
											</Fragment>
										);
									} else if ( 'tablet' === tab.name ) {
										return (
											<Fragment>
												<div className="components-coblocks-dimensions-control__inputs component-coblocks-is-tablet">
													<BaseControl>
														<input
															type="number"
															onChange={ ( newValue ) =>{
																onChangeTablet( newValue );
																this.saveMeta();
															} }
															value={ heightTablet ? heightTablet : '' }
															min={ min }
															step={ step }
															max={ max }
														/>
													</BaseControl>
												</div>
											</Fragment>
										);
									}
									return (
										<Fragment>
											<div className="components-coblocks-dimensions-control__inputs component-coblocks-is-desktop">
												<BaseControl>
													<input
														type="number"
														onChange={ ( newValue ) => {
															onChange( newValue );
															this.saveMeta();
														} }
														value={ height ? height : '' }
														min={ min }
														step={ step }
														max={ max }
													/>
												</BaseControl>
											</div>
										</Fragment>
									);
								}
							}
						</TabPanel>
					</Fragment>
				</div>
			</Fragment>
		);
	}
}

export default withInstanceId( ResponsiveBaseControl );
