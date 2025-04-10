// Admin Interface
jQuery(document).ready(function ($) {
  // -----------------

  function handleCheckoutVisibility() {
    var isEnabled = $("#_enable_custom_checkout").is(":checked");
    // Always hide first, then conditionally show
    $("#custom_checkout_options, #package_descriptions").hide();

    if (isEnabled) {
      $("#custom_checkout_options, #package_descriptions").show();
    }
  }

  // Initial state
  handleCheckoutVisibility();

  // Toggle on checkbox change
  $("#_enable_custom_checkout").on("change", handleCheckoutVisibility);

  // -----------------

  $(".filing-options-group, .business-addons-group").on(
    "click",
    ".add-package-addon, .add-addon",
    function () {
      const type = $(this).data("type");
      const index = Date.now();
      const isBusiness = type === "business_addons";

      const fieldHtml = `
          <div class="addon-entry">
              <input type="text" name="${type}[${index}][label]" placeholder="Addon Label">
              <input type="number" name="${type}[${index}][price]" placeholder="Price" step="0.01">
              ${
                isBusiness
                  ? `<input type="hidden" name="${type}[${index}][type]" value="checkbox">`
                  : ""
              }
              <button type="button" class="button remove-addon">Remove</button>
          </div>
      `;

      $(this).closest(".options_group").find(".addons-list").append(fieldHtml);
    }
  );

  // Frontend Package Switching
  $('input[name="selected_package"]').change(function () {
    const packageType = $(this).val();
    $(".addon-container").hide();
    $(`#${packageType}-addons`).show();
  });

  $(".filing-options-group, .business-addons-group").on("click", ".remove-addon", function () {
    $(this).closest(".addon-entry").remove();
  });

  // Add feature
  $(".feature-group").on("click", ".add-feature", function () {
    const type = $(this).data("type");
    const index = Date.now();

    const html = `
        <div class="feature-entry">
            <input type="text" name="${type}[${index}][title]" placeholder="Feature Title">
            <button type="button" class="button remove-feature">Remove</button>
        </div>
    `;

    $(this).siblings(".features-list").append(html);
  });

  // Remove feature
  $(".feature-group").on("click", ".remove-feature", function () {
    $(this).closest(".feature-entry").remove();
  });
});
