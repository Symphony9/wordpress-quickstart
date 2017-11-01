<?php
// Base config
//
add_action( 'admin_menu', 'form_entries_menu' );

function form_entries_menu() {
	add_menu_page( 'Form Entries', 'Form Entries', 'manage_options', 'form-entries', 'form_entries', 'dashicons-id', 30  );
	add_submenu_page( 'form-entries', 'Export CSV', 'Export CSV', 'manage_options', 'export-form-entries', 'export_csv_form_entries', 'dashicons-media-text' ); 
	export_csv_form_entries();
}

function form_entries() {
	global $wpdb;
	$entryLimit = 10;
	$entriesCount = $wpdb->get_var("SELECT COUNT(*) FROM `contact_form`");
	$pages = ceil($entriesCount / $entryLimit);
	$page = 1;
	if (isset($_GET['paged'])) {
		$page = $_GET['paged'];
	}
	$offset = ($page - 1) * $entryLimit;
	$entries = $wpdb->get_results("SELECT * FROM `contact_form` ORDER BY `date` DESC LIMIT {$offset},{$entryLimit}", OBJECT);

	// make pagination
	$page_links = paginate_links(
		array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __( '&laquo;', 'text-domain' ),
			'next_text' => __( '&raquo;', 'text-domain' ),
			'total' => $pages,
			'current' => $page
		)
	);
?>
	<style type="text/css">
		th {
			font-weight: bold;
			border-bottom: 3px solid #0073AA;
		}
	</style>
	<div class="wrap">
		<h1 class="wp-heading-inline">Form Entries </h1>
		<a class="page-title-action" href="/wp-admin/admin.php?page=export-form-entries">
			Export CSV
		</a>
		<?php
			if ($page_links) {
				echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0; float: left">' . $page_links . '</div></div>';
			}
		?>
		<table class="wp-list-table widefat fixed striped posts">
			<tr>
				<th>
					Date
				</th>
				<th>
					Firstname
				</th>
				<th>
					Lastname
				</th>
				<th>
					Email
				</th>
				<th>
					Phone
				</th>
				<th>
					Service
				</th>
				<th>
					Message
				</th>
				<th>
					Clinic
				</th>
			</tr>
			<?php foreach ($entries as $entry) : ?>
				<tr>
					<td>
						<?php echo $entry->date; ?>
					</td>
					<td>
						<?php echo $entry->firstname; ?>
					</td>
					<td>
						<?php echo $entry->lastname; ?>
					</td>
					<td>
						<?php echo $entry->email; ?>
					</td>
					<td>
						<?php echo $entry->phone; ?>
					</td>
					<td>
						<?php echo $entry->service; ?>
					</td>
					<td>
						<?php echo $entry->message; ?>
					</td>
					<td>
						<?php echo $entry->clinic_name; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
			if ($page_links) {
				echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0; float: left">' . $page_links . '</div></div>';
			}
		?>
	</div>
<?php
}

function export_csv_form_entries() {
	if ($_GET['page'] == 'export-form-entries') {
		global $wpdb;
		$entries = $wpdb->get_results("SELECT * FROM `contact_form` ORDER BY `date` DESC", OBJECT);

		$csv_fields=array();
		$csv_fields[] = 'ID';
		$csv_fields[] = 'Date';
		$csv_fields[] = 'Firstname';
		$csv_fields[] = 'Lastname';
		$csv_fields[] = 'Email';
		$csv_fields[] = 'Phone';
		$csv_fields[] = 'Message';
		$csv_fields[] = 'Service';
		$csv_fields[] = 'Clinic Name';
		
		$date = date('Y-m-d');

		$output_filename = 'form_entries_' . $date  . '.csv';
		$output_handle = @fopen( 'php://output', 'w' );
		 
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( 'Content-Disposition: attachment; filename=' . $output_filename );
		header( 'Expires: 0' );
		header( 'Pragma: public' );	

		// Insert header row
		fputcsv( $output_handle, $csv_fields );

		// Parse results to csv format
		foreach ($entries as $Result) {
			$leadArray = (array) $Result; // Cast the Object to an array
			// Add row to file
			fputcsv( $output_handle, $leadArray );
		}
		 
		// Close output file stream
		fclose( $output_handle ); 

		die();
	}
}