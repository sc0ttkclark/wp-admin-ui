<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class WP_Admin_UI_Export_PDF
 *
 * @package WP_Admin_UI
 */
class WP_Admin_UI_Export_PDF extends TCPDF {

	/** WP_Admin_UI $admin */
	var $admin = null;

	/**
	 * @param WP_Admin_UI $admin
	 * @param string      $orientation
	 * @param string      $unit
	 * @param string      $format
	 * @param bool        $unicode
	 * @param string      $encoding
	 * @param bool        $diskcache
	 * @param bool        $pdfa
	 *
	 * @return self
	 */
	public static function setupPDFClass( WP_Admin_UI $admin, $orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false ) {

		$obj = new self( $orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa );

		$obj->admin = $admin;

		return $obj;

	}

	/**
	 * This method is used to render the page header.
	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @public
	 */
	public function Header() {
		if ($this->header_xobjid === false) {
			// start a new XObject Template
			$this->header_xobjid = $this->startTemplate( $this->w, $this->tMargin );
			$headerfont          = $this->getHeaderFont();
			$headerdata          = $this->getHeaderData();
			$this->y             = $this->header_margin;
			if ( $this->rtl ) {
				$this->x = $this->w - $this->original_rMargin;
			} else {
				$this->x = $this->original_lMargin;
			}
			if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
				$imgtype = TCPDF_IMAGES::getImageFileType( K_PATH_IMAGES . $headerdata['logo'] );
				if (($imgtype == 'eps') OR ($imgtype == 'ai')) {
					$this->ImageEps( K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width'] );
				} elseif ($imgtype == 'svg') {
					$this->ImageSVG( K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width'] );
				} else {
					$this->Image( K_PATH_IMAGES . $headerdata['logo'], '', '', $headerdata['logo_width'] );
				}
				$imgy = $this->getImageRBY();
			} else {
				$imgy = $this->y;
			}

			$cell_height = $this->getCellHeight( $headerfont[2] / $this->k );

			// set starting margin for text data cell
			if ( $this->getRTL() ) {
				$header_x = $this->original_rMargin + ( $headerdata['logo_width'] * 1.1 );
			} else {
				$header_x = $this->original_lMargin + ( $headerdata['logo_width'] * 1.1 );
			}

			$cw = $this->w - $this->original_lMargin - $this->original_rMargin - ( $headerdata['logo_width'] * 1.1 );

			$this->SetTextColorArray( $this->header_text_color );

			// header title
			$this->SetFont( $headerfont[0], 'B', $headerfont[2] + 1 );
			$this->SetX( $header_x );
			$this->Cell( $cw, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0 );

			// header string
			$this->SetFont( $headerfont[0], 'i', $headerfont[2] );
			$this->SetX( $header_x );
			$this->MultiCell( $cw, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false );

			// print an ending header line
			$this->SetLineStyle( array(
				'width' => 0.85 / $this->k,
				'cap'   => 'butt',
				'join'  => 'miter',
				'dash'  => 0,
				'color' => $headerdata['line_color'],
			) );
			$this->SetY( ( 2.835 / $this->k ) + max( $imgy, $this->y ) );
			if ( $this->rtl ) {
				$this->SetX( $this->original_rMargin );
			} else {
				$this->SetX( $this->original_lMargin );
			}
			$this->Cell( ( $this->w - $this->original_lMargin - $this->original_rMargin ), 0, '', 'T', 0, 'C' );
			$this->endTemplate();
		}
		// print header template
		$x = 0;
		$dx = 0;
		if (!$this->header_xobj_autoreset AND $this->booklet AND (($this->page % 2) == 0)) {
			// adjust margins for booklet mode
			$dx = ( $this->original_lMargin - $this->original_rMargin );
		}
		if ( $this->rtl ) {
			$x = $this->w + $dx;
		} else {
			$x = 0 + $dx;
		}
		$this->printTemplate( $this->header_xobjid, $x, 0, 0, 0, '', '', false );
		if ( $this->header_xobj_autoreset ) {
			// reset header xobject template at each page
			$this->resetHeaderTemplate();
		}
	}

	/**
	 * This method is used to render the page footer.
	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @public
	 */
	public function Footer() {

		$cur_y = $this->y;
		$this->SetTextColorArray( $this->footer_text_color );

		//set style for cell border
		$line_width = ( 0.85 / $this->k );
		$this->SetLineStyle( array(
			'width' => $line_width,
			'cap'   => 'butt',
			'join'  => 'miter',
			'dash'  => 0,
			'color' => $this->footer_line_color,
		) );
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
			$style = array(
				'position' => $this->rtl?'R':'L',
				'align' => $this->rtl?'R':'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
		}
		$w_page = isset( $this->l['w_page'] ) ? $this->l['w_page'] . ' ' : '';
		if ( empty( $this->pagegroups ) ) {
			$pagenumtxt = $w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->SetY( $cur_y );

		// Added for page numbers and date
		$headerfont = $this->getHeaderFont();
		$this->SetFont( $headerfont[0], $headerfont[1], 8 );
		$this->Cell( 0, 0, get_date_from_gmt( date( 'Y-m-d H:i:s' ), 'm/d/Y H:i:s' ), 0, 0, 'R' );
		$this->SetX( $this->original_lMargin );
		$this->Cell( 0, 0, 'Page ' . $pagenumtxt, 'T', 1, 'L' );

	}

	/**
	 * @param WP_Admin_UI $admin
	 *
	 * @return string Report file name
	 */
	public static function CreateReport( $admin ) {

		// Default to portrait if not set
		$page_orientation = ! empty( $admin->page_orientation ) ? $admin->page_orientation : 'P';
		$pdf              = self::setupPDFClass( $admin, $page_orientation, PDF_UNIT, 'LETTER', true, 'UTF-8', false );

		// Display the current filter information in the header
		$filter_output = array();
		foreach ( $admin->filters as $filter ) {

			if ( ! isset( $admin->search_columns[ $filter ] ) ) {
				continue;
			}

			$filter_column = $admin->search_columns[ $filter ];
			$selected_id   = $admin->get_var( 'filter_' . $filter, $filter_column['filter_default'] );

			if ( 'related' === $filter_column['type'] ) {
				$filter_label = empty( $selected_id ) ? 'All' : $filter_column['related_lookup'][ $selected_id ];

				$filter_output[] = $filter_column['label'] . ': ' . $filter_label;
			} elseif ( in_array( $filter_column['type'], array( 'date', 'datetime' ), true ) ) {
				$start = $admin->get_var( 'filter_' . $filter . '_start', $filter_column['filter_default'] );
				$end   = $admin->get_var( 'filter_' . $filter . '_end', $filter_column['filter_ongoing_default'] );

				if ( ! empty( $start ) && ! empty( $end ) ) {
					$filter_output[] = $filter_column['label'] . ' from ' . $start . ' to ' . $end;
				}
			} elseif ( 'bool' === $filter_column['type'] ) {
				if ( '' === $selected_id ) {
					$filter_display_value = 'All';
				} else {
					$filter_display_value = ( $selected_id ) ? 'Yes' : 'No';
				}

				$filter_output[] = $filter_column['label'] . ': ' . $filter_display_value;
			}
		}

		$filter_output = implode( "\n", $filter_output );

		// set document information
		$pdf->SetTitle( $admin->item );
		$pdf->SetAuthor( 'Texas Star Party ' );

		// set default header data
		$pdf->setHeaderData( '', 0, $admin->item, $filter_output );

		// set header and footer fonts
		$pdf->setHeaderFont( array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
		$pdf->setFooterFont( array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

		// set margins
		$pdf->SetMargins( 2, PDF_MARGIN_TOP, 2 );
		$pdf->setHeaderMargin( PDF_MARGIN_HEADER );
		$pdf->setFooterMargin( PDF_MARGIN_FOOTER );

		// set auto page breaks
		$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );

		// set image scale factor
		$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

		// set font
		$pdf->SetFont( 'courier', '', 9 );

		// add a page
		$pdf->AddPage();

		// Allow any filter hooks to provide custom output for the report
		$output = apply_filters( 'exports_and_reports_pdf_content', '', $admin, $pdf );
		if ( empty( $output ) ) {

			// No filters provided output, use the default
			$output = self::content( $admin );
		}

		$pdf->writeHTML( $output );

		$export_file          = str_replace( '-', '_', sanitize_title( $admin->items ) ) . '_' . date_i18n( 'm-d-Y_h-i-sa' ) . '.pdf';
		$export_file_location = WP_ADMIN_UI_EXPORT_DIR . '/' . $export_file;

		$pdf->Output( $export_file_location, 'F' );

		return $export_file;
	}

	/**
	 * @param WP_Admin_UI $admin
	 *
	 * @return string
	 */
	protected static function content( $admin ) {

		ob_start();
		?>
		<table cellpadding="2" style="table-layout: fixed;">
			<thead>
			<tr>
				<?php foreach ( $admin->columns as $column => $attributes ) { ?>
					<?php if ( false === $attributes['display'] ) {
						continue;
					} ?>
					<?php $col_label = $attributes['label']; ?>
					<?php $col_width = ( ! empty( $attributes['width'] ) ) ? 'width="' . esc_attr( $attributes['width'] ) . '"' : ''; ?>
					<th <?php echo $col_width; ?>><strong><?php echo $col_label; ?></strong></th>
				<?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $admin->full_data as $row_data ) { ?>
				<tr>
					<?php foreach ( $admin->columns as $column => $attributes ) { ?>
						<?php if ( false === $attributes['display'] ) {
							continue;
						} ?>
						<?php if ( false !== $attributes['custom_display'] && function_exists( "{$attributes['custom_display']}" ) ) {
							$row_data[ $column ] = $attributes['custom_display']( $row_data[ $column ], $row_data, $column, $attributes, $admin );
						}
						$output    = self::format_field( $attributes['type'], $row_data[ $column ] );
						$col_width = ( ! empty( $attributes['width'] ) ) ? 'width="' . esc_attr( $attributes['width'] ) . '"' : ''; ?>
						<td <?php echo $col_width; ?> style="overflow: hidden;"><?php echo $output; ?></td>
					<?php } ?>
				</tr>
			<?php } ?>
			<?php if ( 0 < count( $admin->sum_data ) ) { ?>
				<tr>
					<?php foreach ( $admin->columns as $column => $attributes ) { ?>
						<td>
							<strong><?php echo( isset( $admin->sum_data[0][ $column ] ) ? esc_html( $admin->sum_data[0][ $column ] ) : '' ); ?></strong>
						</td>
					<?php } ?>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php
		return ob_get_clean();

	}

	/**
	 * @param $type
	 * @param $raw_data
	 *
	 * @return int|string
	 */
	public static function format_field( $type, $raw_data ) {

		if ( 'date' === $type ) {
			return date_i18n( 'Y/m/d', strtotime( $raw_data ) );
		}

		if ( 'time' === $type ) {
			return date_i18n( 'g:i:s A', strtotime( $raw_data ) );
		}

		if ( 'datetime' === $type ) {
			return date_i18n( 'Y/m/d g:i:s A', strtotime( $raw_data ) );
		}

		if ( 'bool' === $type ) {
			return ( 1 === (int) $raw_data ? 'Yes' : 'No' );
		}

		if ( 'number' === $type ) {
			return (int) $raw_data;
		}

		if ( 'decimal' === $type ) {
			return number_format( $raw_data, 2 );
		}

		return $raw_data;
	}
}
