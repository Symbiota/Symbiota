<?php

/**
 * Class used to standarized the creation of Pagination Componenets
 */
class Paginator {
	public $pageRequestVar = 'page';
	public int $activePage = 1;
	public int $totalCount = 0;
	public int $perPage = 100;
	public int $pagesShown = 10;

	function __construct(int $resultCount, int $resultPerPage, string $pageRequestVar) {
		$this->activePage = array_key_exists($pageRequestVar, $_REQUEST)? intval(filter_var($_REQUEST[$pageRequestVar], FILTER_SANITIZE_NUMBER_INT)): 1;
		$this->totalCount = $resultCount;
		$this->perPage = $resultPerPage;
	}

	public function nextLink() {

	}

	public function renderPagination() {
		$lastPage = ceil($this->totalCount / $this->perPage);
		$startPage = $this->activePage <= floor($this->pagesShown/2)? 1: $this->activePage - floor($this->pagesShown / 2); 

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


		return '<div style="display:flex; gap:0.2rem">' . $html . '</div>';
	}

	private function btn() {
		return '<button>';
	}

	private function pageLink(int $page) {
		return '<span class="pagination">' . 
			($page === $this->activePage? $page: $this->getNavigationLink($page)) .
		'</span>';
	}

	private function getNavigationLink(int $page, $text = null) {
		return '<a href="tpeditor.php?tid=58358&mediaPage=' . $page . '&tabindex=1">' . ($text ?? $page) . '</a>';
	}
}

