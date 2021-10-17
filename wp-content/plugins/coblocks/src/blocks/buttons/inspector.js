/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, RangeControl, ToggleControl } = wp.components;

/**
 * Inspector controls
 */
class Inspector extends Component {
	render() {
		const {
			clientId,
			attributes,
			setAttributes,
		} = this.props;

		const {
			items,
			isStackedOnMobile,
		} = attributes;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={ __( 'Buttons Settings' ) }>
						<RangeControl
							label={ __( 'Buttons' ) }
							value={ items }
							onChange={ ( nextCount ) => {
								setAttributes( {
									items: parseInt( nextCount ),
								} );

								wp.data.dispatch( 'core/block-editor' ).selectBlock( clientId );
							} }
							min={ 1 }
							max={ 4 }
						/>
						<ToggleControl
							label={ __( 'Stack on mobile' ) }
							checked={ isStackedOnMobile }
							onChange={ () => setAttributes( {
								isStackedOnMobile: ! isStackedOnMobile,
							} ) }
							help={ !! isStackedOnMobile ? __( 'Stacking buttons on mobile.' ) : __( 'Toggle to stack buttons on mobile.' ) }
						/>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	}
}

export default Inspector;
