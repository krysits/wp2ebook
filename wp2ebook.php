<?php
/**
 * @package wp2ebook
 * @version 0.1.0
 */
/*
Plugin Name: wp2ebook
Plugin URI: https://0k.lv/wp2ebook
Description: This plugin symbolizes innovation of a printed book that Gutenberg has made to world.
Author: Kristaps Ledins aka @krysits.COM
Version: 0.1.0
Author URI: https://0k.lv/krysits
*/
use Dompdf\Dompdf;

function wp2ebook() {
	require __DIR__."/vendor/autoload.php";
	
	$pousti = get_posts(['post_type' => 'post', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'ASC']) ;
	$total = count($pousti);
	$lastIndex = $total - 1;
	$aBook = '<html><style type="text/css">'.file_get_contents(__DIR__.'/ebook.css').'</style><body>';
	foreach($pousti as $index => $post ) {
		$aBook .= '<h2>'.
			$post->post_title .'</h2>'.
			$post->post_content .
			'<br><p class="r">'. $post->post_date .'</p>' .
			($lastIndex == $index ? '' : '<ins class="pb"/>');
	}
	$aBook .= '</body></html>';
	
	$dompdf = new DOMPDF;
	
	$dompdf->setPaper("A4");
	$dompdf->loadHtml($aBook);
	
	$dompdf->render();
	$pdf_gen = $dompdf->output();
	
	if(!file_put_contents(wp_upload_dir()['basedir'].date('/Y/m/').get_option('blogname').'.pdf', $pdf_gen)){
		echo '[ErrorEbookPdfRender]';
	}
}

add_action( 'admin_head', 'wp2ebook' );