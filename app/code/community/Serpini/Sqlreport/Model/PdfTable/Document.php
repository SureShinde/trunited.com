<?php
class Serpini_Sqlreport_Model_PdfTable_Document extends Zend_Pdf{

	/*
	 * Margin (margin-top,margin-right,margin-bottom,margin-left)
	 */
	private $_margin=array(10,20,25,20);
	private $_footerYOffset=10; //y offset from margin-bottom --> page bottom
	private $_header;
	private $_footer;
	private $_filename="document.pdf";
	private $_path="/";
	private $_logo; 
	private $_title;
	private $_description;
	//TOP,RIGHT (no used),BOTTOM (no used),LEFT
	private $_description_margin=array(20,0,0,0);
	
	//TOP,RIGHT (no used),BOTTOM (no used),LEFT
	private $_title_margin=array(20,0,0,0);
	private $_footer_margin=array(0,0,0,0);
	private $_title_size=20;
	private $_title_fontName = Zend_Pdf_Font::FONT_HELVETICA;
	private $_title_color = "000000";
	
	public function __construct($filename,$path){
		$this->_filename=$filename;
		$this->_path=$path;
		parent::__construct();
	}
	
	/**
	 * Set Document Margin
	 *
	 * @param integer $value
	 * @param Serpini_Sqlreport_Model_PdfTable_Pdf $position
	 */
	public function setMargin($position,$value){
		$this->_margin[$position]=$value;
	}
	
	public function setTitleMargin($position,$value){
		$this->_title_margin[$position]=$value;
	}
	
	public function setDescriptionMargin($position,$value){
		$this->_description_margin[$position]=$value;
	}
	
	public function setFooterMargin($position,$value){
		$this->_footer_margin[$position]=$value;
	}

	/**
	 * Get Document Margins
	 *
	 * @return array(TOP,RIGHT,BOTTOM,LEFT)
	 */
	public function getMargins(){
		return $this->_margin;
	}

	/**
	 * Set Footer
	 *
	 * @param Serpini_Sqlreport_Model_PdfTable_Pdf_Table $table
	 */
	public function setFooter(Serpini_Sqlreport_Model_PdfTable_Table $table){
		$this->_footer=$table;
	}

	/**
	 * Set Header
	 *
	 * @param Serpini_Sqlreport_Model_PdfTable_Pdf_Table $table
	 */
	public function setHeader(Serpini_Sqlreport_Model_PdfTable_Table $table){
		$this->_header=$table;
	}
	
	
	/**
	 * Set Logo
	 */
	public function setLogo(Zend_Pdf_Resource_Image $image,$width=100,$height=100,$marginTop=0,$marginLeft=0){
		$logo=new Serpini_Sqlreport_Model_PdfTable_Logo($image);
		$logo->setHeightLimit($height);
		$logo->setWidthLimit($width);
		$logo->setMarginTop($marginTop);
		$logo->setMarginLeft($marginLeft);
		$this->_logo=$logo;
	}
	
	public function setTitle($title){
		$this->_title = $title;
	}
	
	public function getTitle(){
		return $this->_title;
	}
	
	public function setDescription($description){
		$this->_description = $description;
	}
	
	public function getDescription(){
		return $this->_description;
	}
	
	public function setTitleSize($size){
		$this->_title_size=$size;
	}
	
	public function setTitleFontName($font){
		$this->_title_fontName = $font;
	}
	
	public function setTitleColor($color){
		$this->_title_color = $color;
	}

	/**
	 * Create a new Page for this Document
	 * Sets all default values (margins,...)
	 * @param mixed $param
	 * @return Serpini_Sqlreport_Model_PdfTable_Page
	 */
	public function createPage($param=Zend_Pdf_Page::SIZE_A4){
		$page=new Serpini_Sqlreport_Model_PdfTable_Page($param);
		$page->setMargins($this->_margin);
		if($this->_header instanceof Serpini_Sqlreport_Model_PdfTable_Table){
			$page->setHeader($this->_header);
		}
		
		if($this->_logo instanceof Serpini_Sqlreport_Model_PdfTable_Logo){
			$page->setLogo($this->_logo);
		}
		 
		$page->setDocument($this);
		return $page;
	}

	/**
	 * Add Page to this Document
	 *
	 * @param Serpini_Sqlreport_Model_PdfTable_Page $page
	 */
	public function addPage(Serpini_Sqlreport_Model_PdfTable_Page $page){
		//add debug page
		//$page->setMargins($this->_margin);
		//$this->_debugTable($page);

		//add pages with new pages (page breaks)
		if($pages=$page->getPages()){
			foreach ($pages as $p){
				$p->setMargins($this->_margin);
				$this->pages[]=$p;
			}
		}
		else{
			$page->setMargins($this->_margin);
			$this->pages[]=$page;
		}
	}

	/**
	 * (renders) and Saves the Document to the specified File
	 *
	 */
	public function save($filename, $updateOnly = false){
		//add header/footer to each page
		$i=1;
		foreach ($this->pages as $page) {
			$this->_drawLogo($page,$i);
			$this->_drawTitle($page,$i);
			$this->_drawFooter($page,$i);
			if($i==1) {
				$this->_drawHeader($page,$i);
			}
			
			$i++;
		}

		parent::save("{$this->_path}/{$this->_filename}");
	}

	private function _drawFooter(Serpini_Sqlreport_Model_PdfTable_Page $page,$currentPage){
		if(!$this->_footer) return;
		if ($page instanceof Serpini_Sqlreport_Model_PdfTable_Page) {

			//set table width
			$currFooter = clone $this->_footer;
			//check for special place holders
			$rows=$currFooter->getRows();
			foreach ($rows as $key=>$row) {
				$row->setWidth($page->getWidth()-$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::LEFT] - $this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::RIGHT]);
				$cols=$row->getColumns();
				$num=0;
				foreach ($cols as $col) {
					if($col->hasText()){
						$num+=$col->replaceText('@@CURRENT_PAGE',$currentPage);
						$num+=$col->replaceText('@@TOTAL_PAGES',count($this->pages));
					}
				}

				if($num>0){
					$row->setColumns($cols);
					$currFooter->replaceRow($row,$key);
				}

			}

			//add table
			$page->addTable($currFooter,
					$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::LEFT],
					($page->getHeight()-$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::BOTTOM]-$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP]+$this->getFooterHeight(false)-$this->_footer_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::BOTTOM]-15),
					false
					);
		}

	}

	private function _drawHeader(Serpini_Sqlreport_Model_PdfTable_Page $page,$currentPage){
		if(!$this->_header) return;
		if ($page instanceof Serpini_Sqlreport_Model_PdfTable_Page) {
			//set table width
			$currHeader = clone $this->_header;
			//check for special place holders
			$rows=$currHeader->getRows();
			foreach ($rows as $key=>$row) {
				$row->setWidth($page->getWidth()-$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::LEFT] - $this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::RIGHT]);
				$cols=$row->getColumns();
				$num=0;
				foreach ($cols as $col) {
					if($col->hasText()){
						$num+=$col->replaceText('@@CURRENT_PAGE',$currentPage);
						$num+=$col->replaceText('@@TOTAL_PAGES',count($this->pages));
					}
				}

				if($num>0){
					$row->setColumns($cols);
					$currHeader->replaceRow($row,$key);
				}

			}
			$altoLogo=0;
			if($this->_logo!=null) $altoLogo = $this->_logo->getHeight()+$this->_logo->getMarginTop();
			$altoTitle = $this->getTitleHeight();
			$offSet = max($altoLogo,$altoTitle) + $this->_description_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP];
			
			$page->addTable($currHeader,
					$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::LEFT],
					$offSet+$this->_title_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP],
					false
					);
		}
	}
	
	private function _drawLogo(Serpini_Sqlreport_Model_PdfTable_Page $page,$currentPage){
		if(!$this->_logo) return;
		$page->drawImage($this->_logo->getImage(), $this->_logo->getMarginLeft()+$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::LEFT],$this->_logo->getMarginTop()+$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP],$this->_logo->getWidth(),$this->_logo->getHeight(),false);
	}
	
	private function _drawTitle(Serpini_Sqlreport_Model_PdfTable_Page $page,$currentPage){
		$font = $this->_title_fontName;
		$page->setFont($font, $this->_title_size);
		$page->setFillColor(new Zend_Pdf_Color_Html($this->_title_color));
		$sepLogo = 0;
		if($this->_logo!=null) $sepLogo=$this->_logo->getMarginLeft()+$this->_logo->getWidth()+$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::LEFT];
		//detectar los saltos de l�nea
		$y=$this->_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP]+$this->_title_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP];
		foreach (explode("\n", $this->getTitle()) as $i => $line) {
			$y+=$this->_title_size;
			$page->drawText($line, $sepLogo+$this->_title_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::LEFT], $y,'UTF-8',false);
		}
		//$page->drawText($this->getTitle(), $sepLogo+20, $this->_title_marginTop,"",false);
	}
	
	public function getTitleHeight(){
		$height = $this->_title_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP];
		foreach (explode("\n", $this->getTitle()) as $i => $line) {
			$height+=$this->_title_size;
		}
		return $height;
	}
	
	// Calcula el alto del footer de la p�gina
	public function getFooterHeight($addMargins=true){
		
		if(!$this->_footer) return 0;
		$height=($addMargins?$this->_footer_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::TOP]+$this->_footer_margin[Serpini_Sqlreport_Model_PdfTable_Pdf::BOTTOM]:0);
		$rows=$this->_footer->getRows();
		foreach ($rows as $key=>$row) {
			$height+=$row->getFontSize();
		}
		return $height;
	}

}

?>