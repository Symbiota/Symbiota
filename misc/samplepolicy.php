<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Sample Use Policy</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Sample Use Policy</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="margin-bottom: 3rem;">
      <h1 style="text-align: center;">Sample Use Policy</h1>
      <p>NEON, the National Ecological Observatory Network, aims to provide "open data to understand how our aquatic and terrestrial ecosystems are changing". Therefore, the <a href="https://docs.google.com/document/d/1C1vSiysUJXSwgg5q2w0y7AP6i2wmCnUQ/edit?usp=sharing&ouid=111202796006164717361&rtpof=true&sd=true" target=_blank rel="noopener noreferrer">General NEON Biorepository Sample Use Conditions</a> and <a href="https://drive.google.com/file/d/1UwUH5LcfdN7n-CLzFvxuaAQzRxo5lZp8/view?usp=sharing" target="_blank" rel="noopener noreferrer">NEON Sample Use Policy</a> reflect the need to provide researchers with access to NEON samples for a wide-variety of purposes while preserving the future research potential of those samples. </p>
        <p>Below are additional criteria related to sample use approval:</p>
        <ul>
          <li>
            <p><a href="mailto:biorepo@asu.edu">Contact us</a> well in advance of any grant proposal deadlines in order to optimally integrate sample uses into project narratives, data management plans, and budgets. The NEON Biorepository <span style="font-weight: bold">must be consulted during preparation of any grant proposal</span> involving existing NEON samples and specimens for several reasons:</p>
            <ul style="font-size: initial">
              <li>We must ensure that the samples and specimens appropriate for the work are available and suitable for the proposed work;</li>
              <li>We may need to consult with the external <a href="https://www.neonscience.org/about/advisory-groups/twgs/biorepository-twg" target="_blank" rel="noopener noreferrer">NEON Biorepository Technical Working Group</a> concerning approval of destructive or consumptive sample use;</li>
              <li>We will only then be able to prevent scenarios in which multiple researchers are funded to destructively use the same samples;</li>
              <li>We need to provide quotes of service fees, if applicable.</li>
            </ul>
          </li>
          <li>
            <p>Sample uses can be non-invasive, invasive, consumptive, or destructive. Uses that reduce the future research potential of a sample will likely require stronger justification and a plan to disseminate all sample-associated data.</p>
          </li>
          <li>
            <p>The <a href="https://www.neonscience.org/about/advisory-groups/twgs/biorepository-twg" target="_blank" rel="noopener noreferrer">NEON Biorepository Technical Working Group</a>, may be consulted prior to approval of any sample use request. As a general rule, external input is required for requests that involve destructive or consumptive use of greater than one third of the available samples of a given taxon, site, and year combination.</p>
          </li>
          <li>
            <p>Other considerations relevant to approval of destructive and consumptive sample use approval include:</p>
            <ul style="font-size: initial">
              <li>Species rarity,</li>
              <li>Physical condition of the specimen,</li>
              <li>Significance of the research relative to NEON’s mission to enable continental-scale ecology,</li>
              <li>Investigator qualifications.</li>
            </ul>
            <p>When multiple requests require use of the same samples, the priority will be NSF-BIO sponsored research followed by other NSF-sponsored research and finally non-NSF funded research.</p>
          </li>
          <li>
            <p>Service fees are determined on a case-by-case basis to cover sample processing and shipping. Cost recovery is required for requests for large numbers of samples or that require additional sample processing. <span style="font-weight: bold">Pilot projects and requests that require few samples and/or no special preparation or subsampling will generally not incur a fee</span>.</p>
          </li>
          <li>
            <p>We will generally be able to fulfill requests of < 100 samples within 4 weeks of approval. Larger requests or those that require additional sample processing may take significantly longer, and we will work with you to determine a timeline.</p>
          </li>
          <li>
            <p>Researchers are responsible for proper handling of all samples, adhering to all aspects of the sample use agreement, and following all NEON and National Science Foundation data reporting and citation policies. The NEON Biorepository may elect not to approve future requests from researchers who do not adhere to these guidelines.</p>
          </li>
      <p>Please read our <a href="https://docs.google.com/document/d/1C1vSiysUJXSwgg5q2w0y7AP6i2wmCnUQ/edit?usp=sharing&ouid=111202796006164717361&rtpof=true&sd=true" target="_blank" rel="noopener noreferrer">General Guidelines for Sample Use</a> and the <a href="https://drive.google.com/file/d/1UwUH5LcfdN7n-CLzFvxuaAQzRxo5lZp8/view?usp=sharing" target="_blank" rel="noopener noreferrer">NEON Sample Use Policy</a> for more details and <a href="mailto:biorepo@asu.edu">contact us</a> for more information.</p>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
