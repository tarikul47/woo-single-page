// Admin Interface
jQuery(document).ready(function ($) {
  // -----------------

  // Toggle meta box visibility based on product type
  function toggleMetaBox() {
    var productType = $("#product-type").val();
    var $metaBox = $("#custom_checkout_options");

    if (productType === "simple") {
      $metaBox.show();
    } else {
      $metaBox.hide();
    }
  }

  // Initial check
  toggleMetaBox();

  // Update on product type change
  $("#product-type").on("change", toggleMetaBox);

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

  $(".woocommerce_options_panel").on("click", ".remove-addon", function () {
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
