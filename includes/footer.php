</div> <!-- Closes everything-in-the-middle div in header -->
<link href="<?= $USWDS_ASSETS  ?>/nalStyleguide.css" type="text/css" rel="stylesheet">
<link onload="handleOnCssLoad()" href="<?= $USWDS_ASSETS  ?>/nalStyleguideSupplement.css" type="text/css" rel="stylesheet">
<footer class="footer-wrapper footer" id="footer" style="display: none;">
  <div class="footer__primary-section">
    <div class="container">
      <div class="region region-footer">
        <nav aria-labelledby="block-bootstrap-core-footer-menu" id="block-bootstrap-core-footer">
          <h2 class="visually-hidden" id="block-bootstrap-core-footer-menu">
            Footer menu
          </h2>

          <ul class="menu menu--footer nav">
            <li class="first">
              <a href="<?php echo $CLIENT_ROOT ?>" data-drupal-link-system-path="&lt;front&gt;" class="is-active">
                Home
              </a>
            </li>
            <li class="last">
              <a href="<?php echo $CLIENT_ROOT ?>/sitemap.php">Sitemap</a>
            </li>
          </ul>
        </nav>
        <section id="block-socialmediaicons" class="block block-nal-social-icons block-social-icons-block clearfix">
          <h2 class="visually-hidden">Social Media</h2>
          <ul class="social-links links">
            <li class="facebook">
              <a href="https://www.facebook.com/AgriculturalResearchService" class="social-link social-link--facebook">
                <span class="social-link__text">Facebook</span>
              </a>
            </li>
            <li class="twitter">
              <a href="https://twitter.com/usda_ars" class="social-link social-link--twitter">
                <span class="social-link__text">Twitter</span>
              </a>
            </li>
            <li class="youtube">
              <a href="https://www.youtube.com/channel/UCbY4NfKJTwEO1rxTdNGjYbA" class="social-link social-link--youtube">
                <span class="social-link__text">YouTube</span>
              </a>
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
          <!-- <li>
            <a
              href="#example-modal-1"
              class="usa-button"
              aria-controls="example-modal-1"
              data-open-modal
              style="margin-top: 0;"
              >Accessibilitiy Options
            </a>
            <div
              class="usa-modal"
              id="example-modal-1"
              aria-labelledby="modal-1-heading"
              aria-describedby="modal-1-description"
            >
                <div class="usa-modal__content">
                  <div class="usa-modal__main">
                    <h2 class="usa-modal__heading" id="modal-1-heading">
                      Accessibilitiy Options
                    </h2>
                    <div class="usa-prose">
                      <p id="modal-1-description">
                        More accessibility features will be added over time. Toggle those that suit your needs.
                      </p>
                    </div>
                    <div class="usa-modal__footer">
                      <ul class="usa-button-group">
                        <li class="usa-button-group__item">
                          <button type="button" class="usa-button" onclick="toggleAccessibilityStyles('<?php echo $CLIENT_ROOT . '/includes' . '/' ?>', '<?php echo $CSS_BASE_PATH ?>', '<?php echo $LANG['TOGGLE_508_OFF'] ?>', '<?php echo $LANG['TOGGLE_508_ON'] ?>')" id="accessibility-button" data-accessibility="accessibility-button">
                            <?php echo (isset($LANG['TOGGLE_508_ON'])?$LANG['TOGGLE_508_ON']:'Switch Form Layout'); ?>
                          </button>
                        </li>
                      </ul>
                      <ul class="usa-button-group">
                        <li class="usa-button-group__item">
                          <button type="button" class="usa-button" data-close-modal>
                            Close
                          </button>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="usa-button usa-modal__close"
                    aria-label="Close this window"
                    data-close-modal
                  >
                    <svg class="usa-icon" aria-hidden="true" focusable="false" role="img">
                      <use xlink:href="/assets/img/sprite.svg#close"></use>
                    </svg>
                  </button>
                </div>
            </div>
          </li> -->
          <li><a href="https://ask.usda.gov/s/">AskUSDA</a></li>
          <li>
            <a href="/web-policies-and-important-links">Policies and Links</a>
          </li>
          <li>
            <a href="https://www.usda.gov/plain-writing">Plain Writing</a>
          </li>
          <li>
            <a href="https://www.ars.usda.gov/oc/foia/freedom-of-information-act-and-privacy-act-reference-guide/">
              FOIA
            </a>
          </li>
          <li><a href="https://www.usda.gov/accessibility-statement">Accessibility Statement</a></li>
          <li>
            <a href="https://www.usda.gov/privacy-policy">Privacy Policy</a>
          </li>
          <li>
            <a href="https://www.usda.gov/non-discrimination-statement">
              Non-Discrimination Statement
            </a>
          </li>
          <li>
            <a href="https://www.usda.gov/oascr/civil-rights-statements">
              Civil Rights Policy
            </a>
          </li>
          <li>
            <a href="https://www.ars.usda.gov/docs/quality-of-information/">
              Information Quality
            </a>
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

<script src="<?= $CLIENT_ROOT ?>/assets/uswds/js/uswds.js" type="text/javascript"></script>