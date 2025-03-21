(function ($) {
  "use strict";

  $(document).ready(function () {
    // Variables to track state
    let orderTotal = 99.0; // Default base price
    let productPrice = 0;

    // Get product price from localized data
    if (
      typeof wc_add_to_cart_params !== "undefined" &&
      wc_add_to_cart_params.product_price
    ) {
      productPrice = Number.parseFloat(wc_add_to_cart_params.product_price);
      orderTotal = productPrice;
    }

    // Initialize form
    updateOrderSummary();

    // Section header click handler
    $(".section-header").on("click", function () {
      const $sectionContent = $(this).next(".section-content");
      $(this).toggleClass("active");
      $sectionContent.slideToggle(300);
    });

    // Continue button handler
    $(".continue-btn").on("click", function () {
      const $currentSection = $(this).closest(".checkout-section");
      const nextSectionId = $(this).data("next");
      const $nextSection = $("#section-" + nextSectionId);
      const currentSectionId = $currentSection
        .find(".section-header")
        .data("section");

      if (validateSection(currentSectionId)) {
        $currentSection.find(".section-content").slideUp(300, function () {
          $(this).removeClass("active");
          $currentSection.find(".section-header").removeClass("active");

          $nextSection.find(".section-header").addClass("active");
          $nextSection.find(".section-content").slideDown(300, function () {
            $(this).addClass("active");
            $("html, body").animate(
              { scrollTop: $nextSection.offset().top - 50 },
              500
            );
          });
        });
      }
    });

    // Company management
    $(".add-company-btn").on("click", addCompany);
    $(document).on("click", ".remove-company-btn", removeCompany);

    // Member/Manager management
    $("#management-type").on("change", handleManagementType).trigger("change"); // Initialize on load

    $(".add-member-btn").on("click", addMember);
    $(document).on("click", ".remove-member-btn", removeMember);

    $(".add-manager-btn").on("click", addManager);
    $(document).on("click", ".remove-manager-btn", removeManager);

    // Pricing controls
    $('input[name="filing_option"]').on("change", updateOrderSummary);
    $('input[name="addon_selection"]').on("change", updateOrderSummary);

    // Form submission
    $("#accordion-checkout-form").on("submit", handleFormSubmit);

    // Helper functions
    function addCompany() {
      const companyCount = $(".company-entry").length + 1;
      const newCompany = `
		  <div class="company-entry">
			<div class="form-group">
			  <label for="company-name-${companyCount}">Company Name <span class="required">*</span></label>
			  <input type="text" id="company-name-${companyCount}" name="company_name[]" required>
			</div>                               
			<button type="button" class="remove-company-btn">Remove</button>
		  </div>
		`;
      $(".company-container").append(newCompany);
      updateOrderSummary();
    }

    function removeCompany() {
      if ($(".company-entry").length > 1) {
        $(this).closest(".company-entry").remove();
        updateOrderSummary();
      } else {
        alert("At least one company is required.");
      }
    }

    function handleManagementType() {
      const isMember = $(this).val() === "member";

      $("#member-container").toggle(isMember);
      $(".add-member-btn").toggle(isMember);
      $("#manager-container").toggle(!isMember);
      $(".add-manager-btn").toggle(!isMember);

      // Update required attributes
      $("#member-container [required]").prop("required", isMember);
      $("#manager-container [required]").prop("required", !isMember);
    }

    function addMember() {
      const memberCount = $(".member-entry").length + 1;
      const newMember = `
		  <div class="member-entry">
			<h3>Member Information</h3>
			<div class="form-group">
			  <label for="member-name-${memberCount}">Name <span class="required">*</span></label>
			  <input type="text" id="member-name-${memberCount}" name="member_name[]" required>
			</div>
			<button type="button" class="remove-member-btn">Remove</button>
		  </div>
		`;
      $("#member-container").append(newMember);
    }

    function removeMember() {
      if ($(".member-entry").length > 1) {
        $(this).closest(".member-entry").remove();
      } else {
        alert("At least one member is required.");
      }
    }

    function addManager() {
      const managerCount = $(".manager-entry").length + 1;
      const newManager = `
		  <div class="manager-entry">
			<h3>Manager Information</h3>
			<div class="form-group">
			  <label for="manager-name-${managerCount}">Name <span class="required">*</span></label>
			  <input type="text" id="manager-name-${managerCount}" name="manager_name[]" required>
			</div>			
			<button type="button" class="remove-manager-btn">Remove</button>
		  </div>
		`;
      $("#manager-container").append(newManager);
    }

    function removeManager() {
      if ($(".manager-entry").length > 1) {
        $(this).closest(".manager-entry").remove();
      } else {
        alert("At least one manager is required.");
      }
    }

    function updateOrderSummary() {
      orderTotal = productPrice || 99.0; // Use product price if available

      // Update filing option price
      const filingOption = $('input[name="filing_option"]:checked').val();
      if (filingOption === "gold") orderTotal = 199.0;
      if (filingOption === "premium") orderTotal = 299.0;

      // Handle addons
      $(".summary-item.addon-item").remove();
      const $selectedAddon = $('input[name="addon_selection"]:checked');

      if ($selectedAddon.length) {
        const addonPrice = Number.parseFloat($selectedAddon.data("price"));
        orderTotal += addonPrice;

        $(".summary-items").append(`
			<div class="summary-item addon-item">
			  <span class="item-name">${$selectedAddon
          .closest(".addon-option")
          .find("h3")
          .text()}</span>
			  <span class="item-price">$${addonPrice.toFixed(2)}</span>
			</div>
		  `);
      }

      $("#order-total").text(`$${orderTotal.toFixed(2)}`);
    }

    function handleFormSubmit(e) {
      e.preventDefault();
      let allValid = true;

      // Validate all sections
      for (let i = 1; i <= 5; i++) {
        if (!validateSection(i)) {
          allValid = false;
          const $section = $("#section-" + i);
          $section.find(".section-header").addClass("active");
          $section.find(".section-content").slideDown(300).addClass("active");
          $("html, body").animate(
            { scrollTop: $section.offset().top - 50 },
            500
          );
          break;
        }
      }

      if (allValid) submitForm();
    }

    function submitForm() {
      const formData = $("#accordion-checkout-form").serializeArray();
      const cartData = formData.filter((item) => item.value);

      // Add product ID if missing
      if (
        wc_add_to_cart_params?.product_id &&
        !formData.some((item) => item.name === "product_id")
      ) {
        cartData.push({
          name: "product_id",
          value: wc_add_to_cart_params.product_id,
        });
      }

      $.ajax({
        type: "POST",
        url: wc_add_to_cart_params?.ajax_url || "/wp-admin/admin-ajax.php",
        data: {
          action: "custom_add_to_cart",
          cart_items: cartData,
          security: $("#custom_form_nonce").val(),
        },
        beforeSend: () => {
          $("#submit-button").prop("disabled", true).text("Processing...");
        },
        success: (response) => {
          if (response.success) {
            window.location.href =
              response.data?.redirect ||
              wc_add_to_cart_params?.checkout_url ||
              "/cart/";
          } else {
            alert(
              response.data?.message ||
                "Error adding to cart. Please try again."
            );
          }
        },
        error: () => {
          alert("An error occurred. Please try again.");
        },
        complete: () => {
          $("#submit-button").prop("disabled", false).text("Submit Order");
        },
      });
    }

    function validateSection(sectionId) {
      let isValid = true;
      let errorMessage = "";

      switch (sectionId) {
        case 1:
          $(".company-entry").each(function () {
            const $input = $(this).find('input[name="company_name[]"]');
            if (!$input.val().trim()) {
              isValid = false;
              $input.addClass("error");
              errorMessage = "Company name is required.";
            } else {
              $input.removeClass("error");
            }
          });
          break;

        case 3:
          const isMember = $("#management-type").val() === "member";
          const selectorPrefix = isMember ? "member" : "manager";
          let hasValidEntry = false;

          $(`.${selectorPrefix}-entry`).each(function () {
            const $name = $(this).find(
              `input[name="${selectorPrefix}_name[]"]`
            );
            const $email = $(this).find(
              `input[name="${selectorPrefix}_email[]"]`
            );
            let valid = true;

            if (!$name.val().trim()) {
              $name.addClass("error");
              valid = false;
            } else {
              $name.removeClass("error");
            }

            if (!$email.val().trim() || !isValidEmail($email.val())) {
              $email.addClass("error");
              valid = false;
              errorMessage = "Valid email is required.";
            } else {
              $email.removeClass("error");
            }

            if (valid) hasValidEntry = true;
          });

          if (!hasValidEntry) {
            isValid = false;
            errorMessage = `At least one valid ${
              isMember ? "member" : "manager"
            } is required.`;
          }
          break;

        case 5:
          if (!$('input[name="addon_selection"]:checked').length) {
            isValid = false;
            errorMessage = "Please select a business addon.";
            $(".business-addons").addClass("error");
          } else {
            $(".business-addons").removeClass("error");
          }
          break;
      }

      if (!isValid && errorMessage) alert(errorMessage);
      return isValid;
    }

    function isValidEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
  });
})(jQuery);
