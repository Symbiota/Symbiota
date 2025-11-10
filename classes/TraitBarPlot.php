<?php

/*
 * A class for bi- or uni-variate bar plots.
 *
 *  Christopher D. Tyrrell, 2021
 *
 * This class is for the plot view and should be used in conjunction with the
 * TraitPlotManager.php controller class.
 *
 * Default plot size is 200 x 400 px, but svg is scalable to any size by setting
 * viewport (width, height attributes) to desired sizes, and setting the viewbox
 * to match the specified plot size (e.g., 0 0 400 400). Line widths are
 * controlled using css classes (e.g., BarPlotAxisLine).
*/

class BarPlot {

  private $PlotClass;
  private $PlotId;
  private $PlotWidth = 400;
  private $PlotHeight = 200;
  private $PlotOrigin;
  private $PlotPadding = 4;       // the distance between the axis and its label, in pixels
  private $PlotMargin = 16;       // space for label text, in pixels (browser font default = 16px)
  private $AxisRotation = 0;      // 0 = vertical bars, 1 = horizontal bars
  private $AxisLength = array();
  private $AxisLabels = array();
  private $AxisNumber;        
  private $TickNumber = 2;        // 1 = the outer edge, >1 = outer + inner, 0 = spokes only
  private $TickScale;
  private $DataValues = array();  // Future: consider making this into a 2D array holding data series
  public $ShowScale = 1;          // 1 = scale values shown, 0 = scale values hidden


  ## METHODS ##

  public function __construct($className = 'BarPlot', $id = ''){
    $this->PlotClass = $className;
    $this->PlotId = $id;
    $this->resetPlotDimensionValues();
	}

	public function __destruct(){
	}


  ### Public Methods ###

  public function setAxisNumber($n) {
    if(is_numeric($n) && $n >= 0) {
      $this->AxisNumber = $n;
      //$this->setRadInterval();
    } else {
      trigger_error("plots cannot have negative number of axes;", E_USER_NOTICE);
    }
  }

  public function setAxisRotation($r) {
    if(is_numeric($r)) {
      $this->AxisRotation = $r;
    }
  }

  public function setTickNumber($n) {
    // max out ticks at the number of pixels in the axis since they're not visible anyway.
    if(is_numeric($n)) {
      if($n > $this->AxisLength) { $n = $this->AxisLength; }
      $this->TickNumber = $n;
    }
  }

  public function setAxisLabels($l) {
    if(is_array($l)) {
      // if(count($l) != $this->AxisNumber) {
      //   trigger_error("number of labels does not match axis number;", E_USER_WARNING);
      // }
      $this->AxisLabels = $l;
    }
  }

  public function setPlotMargin($m) {
    if(is_numeric($m)) {
      $this->PlotMargin = $m;
    }
  }

  public function setDataValues($d) { //check for numeric
     if(is_array($d)) {
       $this->DataValues = $d;
       $this->setTickScale();
       return 1;
     } else {
       return 0;
     }
  }

  public function setPlotDimensions($h, $w = -1) {
    if($w < 0) { $w = $h; }
    if(is_numeric($w) && is_numeric($h)) {
      $this->PlotHeight = $h;
      $this->PlotWidth = $w;
      $this->resetPlotDimensionValues();
    }
  }

  public function getPlotWidth() {
    return $this->PlotWidth;
  }

  public function getPlotHeight() {
    return $this->PlotHeight;
  }

  public function getNumDataValues() {
    return array_sum($this->DataValues);
  }

  public function display() {
    if($this->ShowScale) {
       return $this->axisSVG() . ' ' . $this->tickSVG() . ' ' . $this->scaleSVG() . ' ' . $this->axisLabelSVG() . ' ' . $this->barsSVG();
     } else {
       return $this->axisSVG() . ' ' . $this->tickSVG() . ' ' . $this->axisLabelSVG() . ' ' . $this->barsSVG();
    }
  }


  ### Private Methods ###
  private function resetPlotDimensionValues() {
    $this->PlotOrigin = array('x' => $this->PlotMargin + $this->PlotPadding, 'y' => $this->PlotHeight  - $this->PlotPadding - $this->PlotMargin);
    $this->AxisLength = array('x' => $this->PlotWidth - $this->PlotPadding - $this->PlotMargin, 'y' => $this->PlotHeight - $this->PlotPadding - $this->PlotMargin);
  }

  private function setTickScale() {
    if(empty($this->DataValues)) {
      $this->TickScale = 0;
      return;
    } else {
      if(isset($this->TickNumber) && $this->TickNumber) {
        $s1 = max($this->DataValues) / $this->TickNumber;
        $s2 = 10 ** (round(log10($s1), 0) - 1);
        if($s2 == 0) {
          $s3 = 1;
        } else {
          $s3 = ceil($s1 / $s2) * $s2;
        }
        if(is_nan($s3)) {
          $this->TickScale = 0;
        } else {
          $this->TickScale = $s3;
        }
      }
    }
  }

  #### Graphic Functions ####
  private function tickSVG() {   // tick lines
    $svgStr = '';
    if(isset($this->ShowScale) && $this->ShowScale) {
      if( (isset($this->TickNumber) && $this->TickNumber) OR (isset($this->AxisNumber) && $this->AxisNumber) ) {
        //$barPlotId = uniqid("BarPlotGrid-", true);
        $tickXInterval = round($this->AxisLength['x'] / $this->AxisNumber, 1);
        $tickYInterval = round($this->AxisLength['y'] / $this->TickNumber, 1);

        $svgStr .= '<rect ';
        $svgStr .= 'x="' .$this->PlotOrigin['x']. '" ';
        $svgStr .= 'y="0" ';
        $svgStr .= 'width="' .$this->AxisLength['x']. '" ';
        $svgStr .= 'height="' .$this->AxisLength['y']. '" ';
        $svgStr .= 'class="' .$this->PlotClass. 'Background" />' .PHP_EOL;

        $y1 = $this->PlotOrigin['y'];
        $y2 = 0;
        for ($xpos = $this->PlotOrigin['x']; $xpos <= $this->AxisLength['x']; $xpos = $xpos + $tickXInterval) {
          $svgStr .= '<line ';
          $svgStr .= 'x1="' .$xpos. '" ';
          $svgStr .= 'y1="' .$y1. '" ';
          $svgStr .= 'x2="' .$xpos. '" ';
          $svgStr .= 'y2="' .$y2. '" ';
          $svgStr .= 'class="' . $this->PlotClass . 'XTickLine" />' .PHP_EOL;
        }

        $x1 = $this->PlotOrigin['x'];
        $x2 = $this->PlotWidth;
        for ($ypos = $this->PlotOrigin['y']; $ypos >= 0; $ypos = $ypos - $tickYInterval) {
          $svgStr .= '<line ';
          $svgStr .= 'x1="' .$x1. '" ';
          $svgStr .= 'y1="' .$ypos. '" ';
          $svgStr .= 'x2="' .$x2. '" ';
          $svgStr .= 'y2="' .$ypos. '" ';
          $svgStr .= 'class="' . $this->PlotClass . 'YTickLine" />' .PHP_EOL;
        }
      }
    }
    return $svgStr;
  }

  private function axisLabelSVG() {   // x-axis text/label
    $svgStr = '';
    $degRotation = 0;
    $ypos = $this->PlotOrigin['y'] + $this->PlotMargin;
    $xinterval = round($this->AxisLength['x'] / $this->AxisNumber, 1);
    $xpos = $this->PlotOrigin['x'] + ($xinterval / 2);
    for($i = 0; $i < $this->AxisNumber; $i++) {
      if(isset($this->AxisLabels[$i])) { $label = $this->AxisLabels[$i]; } else { $label = $i + 1; }
      //$x2 = round($this->PlotCenter['x'] + ($this->AxisLength + $this->PlotPadding) * cos($radPos), 0);
      //$y2 = round($this->PlotCenter['y'] - ($this->AxisLength + $this->PlotPadding) * sin($radPos), 0);
      $svgStr .= '<text transform="translate(' . $xpos . ',' . $ypos . ')';
      $svgStr .= 'rotate(' . $degRotation . ')" ';
      $svgStr .= 'class="' . $this->PlotClass . 'XLabelText">';
      $svgStr .= $label . '</text>' . PHP_EOL;
      $xpos += $xinterval;
    }
    return $svgStr;
  }

  private function scaleSVG() {   // y-axis text/label
    $svgStr = '';
    if(!isset($this->TickScale)) {
      return $svgStr;
    }
    if(isset($this->TickNumber) && $this->TickNumber) {
      $tickInterval = round($this->AxisLength['y'] / $this->TickNumber, 1);
      for($i = 0; $i < $this->TickNumber; $i++) {
        $yval = $tickInterval * $i;
        $svgStr .= '<text x="' .($this->PlotOrigin['x'] - $this->PlotPadding). '" ';
        $svgStr .= 'y="'.($this->PlotOrigin['y'] - $yval).'" ';//
        $svgStr .= 'class="' . $this->PlotClass . 'YLabelText">';
        $svgStr .= $this->TickScale * $i. '</text>' .PHP_EOL;
      }
    }
    return $svgStr;
  }

  private function axisSVG() {   // axes lines
    $svgStr = '';

    // for($i = 0; $i < $this->AxisNumber; $i++) {
    //   $x2 = round($this->PlotCenter['x'] + $this->AxisLength * cos($radPos), 0);
    //   $y2 = round($this->PlotCenter['y'] - $this->AxisLength * sin($radPos), 0);
    //   $svgStr .= '<line x1="' . $this->PlotCenter['x'] . '" y1="' . $this->PlotCenter['y'] . '" x2="' . $x2 . '" y2="' . $y2 . '" class="' . $this->PlotClass . 'AxisLine" />' . PHP_EOL;
    //   $radPos -= $this->RadInterval;
    // }
    return $svgStr;
  }

  private function barsSVG() {   // data graphics
    $svgStr = '';
    $traitMax = max($this->DataValues);
    $xinterval = $this->AxisLength['x'] / count($this->DataValues);
    $xstart = $this->PlotOrigin['x'];
    $barWidthScaleFactor = 0.9;
    if($traitMax > 0) {
      foreach($this->DataValues as $k => $d) {
        $scaledTraitValue = round($d * $this->AxisLength['y'] / $traitMax, 1);
        $svgStr .= '<rect x="' .($xstart + (((1-$barWidthScaleFactor)/2) * $xinterval)). '" ';
        $svgStr .= 'y="' .($this->PlotOrigin['y'] - $scaledTraitValue). '" ';
        $svgStr .= 'height="' . $scaledTraitValue . '" ';
        $svgStr .= 'width="' . ($barWidthScaleFactor * $xinterval) . '" ';
        $svgStr .= 'class="' . $this->PlotClass . 'Focal" />' .PHP_EOL;
        $xstart += $xinterval;
      }
    }
    return $svgStr;
  }
}
?>
