/**
 * WordPress dependencies
 */
const { Component, Fragment } = wp.element;
const { BlockControls, AlignmentToolbar } = wp.blockEditor;

class Controls extends Component {
	render() {
		const {
			attributes,
			setAttributes,
		} = this.props;

		const { textAlign } = attributes;

		return (
			<Fragment>
				<BlockControls>
					<AlignmentToolbar
						value={ textAlign }
						onChange={ ( nextTextAlign ) => setAttributes( { textAlign: nextTextAlign } ) }
					/>
				</BlockControls>
			</Fragment>
		);
	}
}

export default Controls;
