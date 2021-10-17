/**
 * External dependencies
 */
import isEmpty from 'lodash/isEmpty';

/**
 * Internal dependencies
 */
import { BLOCKS_WITH_AUTOPADDING } from './';
import ResponsiveTabsControl from '../../components/responsive-tabs-control';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { SelectControl, RangeControl, ToggleControl, PanelBody, Button, FocalPointPicker } = wp.components;

class BackgroundPanel extends Component {
	constructor() {
		super( ...arguments );
		this.setBackgroundPaddingTo = this.setBackgroundPaddingTo.bind( this );
		this.setBackgroundPaddingMobileTo = this.setBackgroundPaddingMobileTo.bind( this );
		this.onSelectRepeat = this.onSelectRepeat.bind( this );
	}

	setBackgroundPaddingTo( value ) {
		this.props.setAttributes( { backgroundPadding: value } );

		if ( this.props.attributes.backgroundPadding <= 0 ) {
			this.props.setAttributes( {
				backgroundRadius: 0,
			} );
		}
	}

	setBackgroundPaddingMobileTo( value ) {
		this.props.setAttributes( { backgroundPaddingMobile: value } );
	}

	onSelectRepeat( value ) {
		if ( value === 'no-repeat' ) {
			this.props.setAttributes( {
				backgroundRepeat: value,
				backgroundSize: 'cover',
			} );
		} else {
			this.props.setAttributes( {
				backgroundRepeat: value,
				backgroundSize: 'contain',
				focalPoint: undefined,
			} );
		}
	}

	render() {
		const {
			attributes,
			backgroundColor,
			hasGalleryControls,
			hasOverlay,
			setAttributes,
		} = this.props;

		const {
			align,
			backgroundImg,
			backgroundOverlay,
			backgroundPadding,
			backgroundPaddingMobile,
			backgroundPosition,
			backgroundRadius,
			backgroundRepeat,
			backgroundSize,
			backgroundType = 'image',
			focalPoint,
			hasParallax,
			videoLoop,
			videoMuted,
		} = attributes;

		const backgroundPositionOptions = [
			{ value: 'top left', label: __( 'Top Left' ) },
			{ value: 'top center', label: __( 'Top Center' ) },
			{ value: 'top right', label: __( 'Top Right' ) },
			{ value: 'center left', label: __( 'Center Left' ) },
			{ value: 'center center', label: __( 'Center Center' ) },
			{ value: 'center right', label: __( 'Center Right' ) },
			{ value: 'bottom left', label: __( 'Bottom Left' ) },
			{ value: 'bottom center', label: __( 'Bottom Center' ) },
			{ value: 'bottom right', label: __( 'Bottom Right' ) },
		];

		const backgroundRepeatOptions = [
			{ value: 'no-repeat', label: __( 'No Repeat' ) },
			{ value: 'repeat', label: __( 'Repeat' ) },
			{ value: 'repeat-x', label: __( 'Repeat Horizontally' ) },
			{ value: 'repeat-y', label: __( 'Repeat Vertically' ) },
		];

		const backgroundSizeOptions = [
			{ value: 'auto', label: __( 'Auto' ) },
			{ value: 'cover', label: __( 'Cover' ) },
			{ value: 'contain', label: __( 'Contain' ) },
		];

		const backgroundSizeDefault = 'cover';

		return (
			<Fragment>
				{ backgroundImg && (
					<PanelBody
						title={ __( 'Background Settings' ) }
						initialOpen={ false }
						className="components-panel__body--coblocks-background-panel"
					>
						{ backgroundType === 'image' && (
							<ToggleControl
								label={ __( 'Fixed Background' ) }
								checked={ !! hasParallax }
								onChange={ () => setAttributes( { hasParallax: ! hasParallax } ) }
							/>
						) }
						{ ! hasParallax && FocalPointPicker && backgroundType === 'image' && backgroundRepeat !== 'repeat' && (
							<FocalPointPicker
								label={ __( 'Focal Point' ) }
								url={ backgroundImg }
								value={ focalPoint }
								onChange={ ( value ) => setAttributes( { focalPoint: value } ) }
								className="components-focal-point-picker--coblocks"
							/>
						) }
						{ hasOverlay && (
							<RangeControl
								label={ __( 'Background Opacity' ) }
								value={ backgroundOverlay }
								onChange={ ( nextBackgroundOverlay ) => setAttributes( { backgroundOverlay: nextBackgroundOverlay } ) }
								min={ 0 }
								max={ 100 }
								step={ 10 }
							/>
						) }
						{ hasGalleryControls && (
							<Fragment>
								<ResponsiveTabsControl { ...this.props }
									label={ __( 'Padding' ) }
									value={ backgroundPadding }
									valueMobile={ backgroundPaddingMobile }
									onChange={ this.setBackgroundPaddingTo }
									onChangeMobile={ this.setBackgroundPaddingMobileTo }
									min={ 5 }
									max={ 100 }
								/>
								{ ( ( ! isEmpty( backgroundImg ) || ! isEmpty( backgroundColor.color ) ) && backgroundPadding > 0 ) && align !== 'full' &&
									<RangeControl
										label={ __( 'Rounded Corners' ) }
										value={ backgroundRadius }
										onChange={ ( nextBackgroundRadius ) => setAttributes( { backgroundRadius: nextBackgroundRadius } ) }
										min={ 0 }
										max={ 20 }
										step={ 1 }
									/>
								}
							</Fragment>
						) }
						{ backgroundType === 'image' && (
							<SelectControl
								label={ __( 'Repeat' ) }
								className="components-background-display-select--coblocks"
								value={ backgroundRepeat ? backgroundRepeat : 'no-repeat' }
								options={ backgroundRepeatOptions }
								onChange={ ( nextbackgroundRepeat ) => this.onSelectRepeat( nextbackgroundRepeat ) }
							/>
						) }
						{ ! FocalPointPicker && backgroundType === 'image' && (
							<SelectControl
								label={ __( 'Position' ) }
								value={ backgroundPosition ? backgroundPosition : 'center center' }
								options={ backgroundPositionOptions }
								onChange={ ( nextbackgroundPosition ) => setAttributes( { backgroundPosition: nextbackgroundPosition } ) }
							/>
						) }
						{ backgroundRepeat === 'no-repeat' && backgroundType === 'image' && (
							<SelectControl
								label={ __( 'Display' ) }
								value={ backgroundSize ? backgroundSize : backgroundSizeDefault }
								options={ backgroundSizeOptions }
								onChange={ ( nextbackgroundSize ) => setAttributes( { backgroundSize: nextbackgroundSize } ) }
							/>
						) }
						{ backgroundType === 'video' && (
							<ToggleControl
								label={ __( 'Mute Video' ) }
								help={ videoMuted ? __( 'Muting the background video.' ) : __( 'Toggle to mute the video.' ) }
								checked={ !! videoMuted }
								onChange={ () => setAttributes( { videoMuted: ! videoMuted } ) }
							/>
						) }
						{ backgroundType === 'video' && (
							<ToggleControl
								label={ __( 'Loop Video' ) }
								help={ videoLoop ? __( 'Looping the background video.' ) : __( 'Toggle to loop the video.' ) }
								checked={ !! videoLoop }
								onChange={ () => setAttributes( { videoLoop: ! videoLoop } ) }
							/>
						) }
						<Button
							className="components-button--coblocks-remove-background-image"
							type="button"
							isDefault
							label={ __( 'Remove background' ) }
							onClick={ () => {
								setAttributes( {
									backgroundImg: '',
									backgroundOverlay: 0,
									backgroundRepeat: 'no-repeat',
									backgroundPosition: '',
									backgroundSize: 'cover',
									hasParallax: false,
									backgroundPadding: 0,
									backgroundPaddingMobile: 0,
								} );

								// Remove padding when background image is removed.
								if ( BLOCKS_WITH_AUTOPADDING.includes( this.props.name ) ) {
									if ( attributes.paddingSize ) {
										setAttributes( { paddingSize: 'no' } );
									}
								}
							} }
						>
							{ __( 'Remove ' + backgroundType ) }
						</Button>
					</PanelBody>
				) }
			</Fragment>
		);
	}
}

export default BackgroundPanel;
