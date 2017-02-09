<?php
class CommerceExtensions_Orderimportexport_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() {
		// place this code inside a php file and call it f.e. "download.php"
		#$path = $_SERVER['DOCUMENT_ROOT']."/var/export/"; // change the path to fit your websites document structure
		$path = Mage::getBaseDir()."/var/export/"; // change the path to fit your websites document structure
		
		$fullPath = $path.$_GET['download_file'];
		
		if ($fd = fopen ($fullPath, "r")) {
			$fsize = filesize($fullPath);
			$path_parts = pathinfo($fullPath);
			$ext = strtolower($path_parts["extension"]);
			switch ($ext) {
				case "csv":
				header("Content-type: text/csv"); // add here more headers for diff. extensions
				header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
				break;
				default;
				header("Content-type: application/octet-stream");
				header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
			}
			header("Content-length: $fsize");
			header("Cache-control: private"); //use this to open files directly
			while(!feof($fd)) {
				$buffer = fread($fd, 2048);
				echo $buffer;
			}
		}
		fclose ($fd);
		exit;
	}
}