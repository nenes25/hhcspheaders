$(document).ready(function () {
  cspBuilder.init();
  $('input[name="HHCSPHEADERS_CSP_DEFAULT_SRC"]').trigger('blur');
});

/**
 * Generate a csp preview in back office
 */
cspBuilder = {

  argumentPrefix: "csp-eval-argument",
  cspRadioSelector : ".radio-select-csp-mode",
  previewBlockId: "#csp_preview",
  debug: false,

  /**
   * Init the builder
   *
   * @return void
   */
  init: function () {
    let self = this;
    $("input[class^='" + this.argumentPrefix + "']").on('blur', function () {
      self.previewCsp();
    });
    $(this.cspRadioSelector).on('click',function(){
      self.previewCsp();
    });
  },

  /**
   * Generate the preview of the csp
   *
   * @return void
   */
  previewCsp: function () {
    let self = this;
    let cspMode = $('#configuration_form input[name=HHCSPHEADERS_MODE]:checked').val();
    let generatedCsp = '';
    let generatedCspReport = '';

    //Evaluate all csp policy fields
    $("input[class^='" + this.argumentPrefix + "']").each(function () {
      let cspPolicy = $(this).attr('class').replace(self.argumentPrefix + '-', '');
      self.log('Process cspPolicy ' + cspPolicy);
      if ($(this).val() != "") {
        if (cspMode == 'BOTH' || cspMode == 'BLOCK') {
          generatedCsp += ' ' + cspPolicy + " " + $(this).val() + ';'
          self.log('Value for csp ' + $(this).val());
        }
        if (cspMode == 'BOTH' || cspMode == 'REPORT-ONLY') {
          generatedCspReport += ' ' + cspPolicy + " " + $(this).val() + ';'
          self.log('Value for report csp ' + $(this).val());
        }
      } else {
        self.log('No value for ' + cspPolicy);
      }
    });

    //Render generated policies
    if (generatedCsp != '') {
      generatedCsp = 'Content-Security-Policy: ' + generatedCsp;
    }
    if (generatedCspReport != '') {
      generatedCspReport = 'Content-Security-Policy-Report-Only:' + generatedCspReport;
    }

    let previewContent = generatedCsp + "<br />" + generatedCspReport;
    this.log('Preview content ' + previewContent.replace("<br />","\n"));
    $(this.previewBlockId).html('').html(previewContent);
  },

  /**
   * Wrapper for console.log only if debug mode is enable
   *
   * @param string message
   * @return void
   */
  log: function (message) {
    if (this.debug === true) {
      console.log(message);
    }
  }
}
