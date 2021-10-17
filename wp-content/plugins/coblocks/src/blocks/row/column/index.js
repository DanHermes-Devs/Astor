/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import Edit from './components/edit';
import icons from './../../../utils/icons';
import { BackgroundStyles, BackgroundAttributes, BackgroundClasses, BackgroundVideo } from '../../../components/background';
import DimensionsAttributes from '../../../components/dimensions-control/attributes';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { InnerBlocks, getColorClassName } = wp.blockEditor;

/**
 * Block constants
 */
const name = 'column';

const title = __( 'Column' );

const icon = icons.column;

const blockAttributes = {
	width: {
		type: 'string',
	},
	contentAlign: {
		type: 'string',
	},
	textColor: {
		type: 'string',
	},
	customTextColor: {
		type: 'string',
	},
	showInserter: {
		type: 'boolean',
		default: true,
	},
	...DimensionsAttributes,
	...BackgroundAttributes,
};

const settings = {

	title: title,

	description: __( 'An immediate child of a row.' ),

	attributes: blockAttributes,

	parent: [ 'coblocks/row' ],

	supports: {
		inserter: false,
	},

	edit: Edit,

	getEditWrapperProps( attributes ) {
		const { paddingSize } = attributes;

		// If the column block has children, return the following.
		if ( paddingSize !== 'advanced' && paddingSize === 'no' ) {
			return { 'data-background-dropzone': false };
		}
	},

	save( { attributes } ) {
		const {
			coblocks,
			textColor,
			customTextColor,
			marginSize,
			paddingSize,
			contentAlign,
		} = attributes;
		const textClass = getColorClassName( 'color', textColor );

		const classes = classnames( {
			[ `coblocks-column-${ coblocks.id }` ]: coblocks && ( typeof coblocks.id !== 'undefined' ),
		} );

		const innerClasses = classnames(
			'wp-block-coblocks-column__inner',
			...BackgroundClasses( attributes ), {
				'has-text-color': textColor || customTextColor,
				[ textClass ]: textClass,
				'has-padding': paddingSize && paddingSize !== 'no',
				'has-margin': marginSize && marginSize !== 'no',
				[ `has-${ paddingSize }-padding` ]: paddingSize && ( paddingSize !== 'advanced' ),
				[ `has-${ marginSize }-margin` ]: marginSize && ( marginSize !== 'advanced' ),
			} );

		const styles = {
			textAlign: contentAlign ? contentAlign : null,
		};

		const innerStyles = {
			...BackgroundStyles( attributes ),
			color: textClass ? undefined : customTextColor,
		};

		return (
			<div className={ classes } style={ styles } >
				<div className={ innerClasses } style={ innerStyles }>
					{ BackgroundVideo( attributes ) }
					<InnerBlocks.Content />
				</div>
			</div>
		);
	},

	deprecated: [ {
		attributes: {
			...blockAttributes,
		},
		save( { attributes } ) {
			const {
				id,
				coblocks,
				backgroundType,
				backgroundColor,
				backgroundImg,
				customBackgroundColor,
				textColor,
				customTextColor,
				marginSize,
				paddingSize,
				contentAlign,
			} = attributes;

			const textClass = getColorClassName( 'color', textColor );
			const backgroundClass = getColorClassName( 'background-color', backgroundColor );

			let classlist = {
				[ `coblocks-row--${ id }` ]: id,
				'has-text-color': textColor || customTextColor,
				[ textClass ]: textClass,
			};

			if ( coblocks && ( typeof coblocks.id !== 'undefined' ) ) {
				classlist = Object.assign( classlist, [ `coblocks-row-${ coblocks.id }` ] );
			}

			const classes = classnames( classlist );

			const innerClasses = classnames(
				'wp-block-coblocks-column__inner',
				...BackgroundClasses( attributes ), {
					'has-padding': paddingSize && paddingSize !== 'no',
					'has-margin': marginSize && marginSize !== 'no',
					[ `has-${ paddingSize }-padding` ]: paddingSize && ( paddingSize !== 'advanced' ),
					[ `has-${ marginSize }-margin` ]: marginSize && ( marginSize !== 'advanced' ),
				} );

			const styles = {
				color: textClass ? undefined : customTextColor,
				textAlign: contentAlign ? contentAlign : null,
			};

			const innerStyles = {
				backgroundColor: backgroundClass ? undefined : customBackgroundColor,
				backgroundImage: backgroundImg && backgroundType === 'image' ? `url(${ backgroundImg })` : undefined,
			};

			return (
				<div className={ classes } style={ styles } >
					{ BackgroundVideo( attributes ) }
					<div className={ innerClasses } style={ innerStyles }>
						<InnerBlocks.Content />
					</div>
				</div>
			);
		},
	},
	],
};

export { name, title, icon, settings };
