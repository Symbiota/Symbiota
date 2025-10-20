<?php

/**
 * Class used to standarized the creation of Pagination Componenets
 */
class Paginator {
	public Int $activePage = 1;
	public Int $totalCount = 0;
	public Int $perPage = 100;
	public Int $pagesShown = 10;
	public String $pageRequestVar = 'page';
	public String $baseLink;
	public Array $queryParams;

	function __construct(Int $resultCount, Int $resultPerPage, String $pageRequestVar, String $baseLink, Array $queryParams = []) {
		$this->totalCount = $resultCount;
		$this->perPage = $resultPerPage;
		$this->pageRequestVar = $pageRequestVar;
		$this->activePage = array_key_exists($pageRequestVar, $_REQUEST)? intval(filter_var($_REQUEST[$pageRequestVar], FILTER_SANITIZE_NUMBER_INT)): 1;
		$this->baseLink = $baseLink;
		$this->queryParams = $queryParams;
	}

	public function renderPagination(): String {
		$lastPage = ceil($this->totalCount / $this->perPage);
		$startPage = $this->activePage <= floor($this->pagesShown/2)? 1: $this->activePage - floor($this->pagesShown / 2); 
		$maxActive = max(1, $lastPage - $this->pagesShown);

		if($this->activePage > $maxActive) {
			$startPage = $maxActive;
		}

		$lastShownPage = min($startPage + $this->pagesShown, $lastPage);

		$html = '';

		if($startPage != 1) {
			$html .= '<span class="pagination">'. $this->getNavigationLink(1, 'First').'</span>';
			$html .= '<span class="pagination">'. $this->getNavigationLink($this->activePage - 1, '<').'</span>';
		} 

		for($i = $startPage; $i <= $lastShownPage; $i++) {
			$html .= $this->pageLink($i);
		}

		if($lastShownPage != $lastPage) {

			$html .= '<span class="pagination">'. $this->getNavigationLink($this->activePage + 1, '>').'</span>';
			$html .= '<span class="pagination">'. $this->getNavigationLink($lastPage, 'Last').'</span>';
		} 


		return '<div style="display:flex; gap:0.2rem">' . $html . '<div style="flex-grow:1; display:flex;justify-content:end">Page ' . $this->summaryText() . '</div></div>';
	}	

	private function summaryText(): String {
		return $this->activePage . ', records ' . (( ($this->activePage - 1) * $this->perPage) + 1) . ' - ' . $this->perPage * $this->activePage . ' of ' . $this->totalCount;
	}

	private function pageLink(Int $page): String {
		return '<span class="pagination">' . 
			($page === $this->activePage? $page: $this->getNavigationLink($page)) .
		'</span>';
	}

	private function getNavigationLink(Int $page, ?String $text = null): String {
		return '<a href="' . htmlspecialchars($this->baseLink . '?' . http_build_query([...$this->queryParams, $this->pageRequestVar => $page])) . '">' . ($text ?? $page) . '</a>';
	}

	public static function getPageRequestVar(String $variableName): Int {
		if(($page = $_REQUEST[$variableName] ?? false) && is_numeric($page)) {
			return intval(filter_var($page, FILTER_SANITIZE_NUMBER_INT));
		} else {
			return 1;
		}
	}
}
