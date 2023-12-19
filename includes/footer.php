<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/symbiota/nalStyleguide.css" type="text/css" rel="stylesheet">
<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/symbiota/nalStyleguideSupplement.css" type="text/css" rel="stylesheet">
<footer class="footer-wrapper footer">
  <div class="footer__primary-section">
    <div class="container">
      <div class="region region-footer">
        <nav
          aria-labelledby="block-bootstrap-core-footer-menu"
          id="block-bootstrap-core-footer"
        >
          <h2 class="visually-hidden" id="block-bootstrap-core-footer-menu">
            Footer menu
          </h2>

          <ul class="menu menu--footer nav">
            <li class="first">
              <a
                href="<?php echo $CLIENT_ROOT ?>"
                data-drupal-link-system-path="&lt;front&gt;"
                class="is-active"
                >Home</a
              >
            </li>
            <li class="last">
              <a href="<?php echo $CLIENT_ROOT ?>/sitemap.php">Sitemap</a>
            </li>
          </ul>
        </nav>
        <section
          id="block-socialmediaicons"
          class="block block-nal-social-icons block-social-icons-block clearfix"
        >
          <h2 class="visually-hidden">Social Media</h2>
          <ul class="social-links links">
            <li class="facebook">
              <a
                href="https://www.facebook.com/AgriculturalResearchService"
                class="social-link social-link--facebook"
                ><span class="social-link__text">Facebook</span></a
              >
            </li>
            <li class="twitter">
              <a
                href="https://twitter.com/usda_ars"
                class="social-link social-link--twitter"
                ><span class="social-link__text">Twitter</span></a
              >
            </li>
            <li class="youtube">
              <a
                href="https://www.youtube.com/channel/UCbY4NfKJTwEO1rxTdNGjYbA"
                class="social-link social-link--youtube"
                ><span class="social-link__text">YouTube</span></a
              >
            </li>
          </ul>
        </section>
      </div>
    </div>
  </div>

  <div class="footer__secondary-section">
    <div class="container">
      <nav class="row" aria-labelledby="nal-usda-links">
        <h2 id="nal-usda-links" class="visually-hidden">Government Links</h2>
        <ul>
          <li><a href="https://ask.usda.gov/s/">AskUSDA</a></li>
          <li>
            <a href="/web-policies-and-important-links">Policies and Links</a>
          </li>
          <li>
            <a href="https://www.usda.gov/plain-writing">Plain Writing</a>
          </li>
          <li>
            <a
              href="https://www.ars.usda.gov/oc/foia/freedom-of-information-act-and-privacy-act-reference-guide/"
              >FOIA</a
            >
          </li>
          <li><a href="https://www.usda.gov/accessibility-statement">Accessibility Statement</a></li>
          <li>
            <a href="https://www.usda.gov/privacy-policy">Privacy Policy</a>
          </li>
          <li>
            <a href="https://www.usda.gov/non-discrimination-statement"
              >Non-Discrimination Statement</a
            >
          </li>
          <li>
            <a href="https://www.usda.gov/oascr/civil-rights-statements"
              >Civil Rights Policy</a
            >
          </li>
          <li>
            <a href="https://www.ars.usda.gov/docs/quality-of-information/"
              >Information Quality</a
            >
          </li>
          <li>
            <a href="https://www.ars.usda.gov">Agricultural Research Service</a>
          </li>
          <li><a href="https://www.usda.gov">USDA.gov</a></li>
          <li><a href="https://www.usa.gov">USA.gov</a></li>
          <li><a href="https://www.whitehouse.gov">WhiteHouse.gov</a></li>
        </ul>
        <form
          id="GD-snippet-form"
          action="https://public.govdelivery.com/accounts/USDAARS/subscriber/qualify?qsp=CODE_RED"
          accept-charset="UTF-8"
          method="post"
          target="_blank"
          class="sign-up"
        >
          <h3 class="sign_up-header">Sign up for ARS News updates</h3>
          <input name="utf8" type="hidden" value="✓" />
          <input
            type="hidden"
            name="authenticity_token"
            value="tV2OquJR5xnmtrmmZS3UWsIp7QddNiZcKotw2AMMUx2u9nfu4b3aL1Fb4L6RnJCoF5VYhXZ85qUPjpOyJiUlhg=="
          />
          <label for="email">Your email address</label>
          <input
            type="text"
            name="email"
            class="form-control"
            id="email"
            value=""
          />
          <input
            type="submit"
            name="commit"
            class="form_button btn btn-primary"
            id="go"
            value="Sign up"
          />
        </form>
      </nav>
    </div>
  </div>
</footer>

<script src="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/assets/uswds/js/ARSbranding.js" type="text/javascript"></script>
<script src="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/assets/uswds/js/uswds.js" type="text/javascript"></script>