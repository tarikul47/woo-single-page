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

    // Filling option controls
    $('input[name="filing_option"]').on("change", showHideAddons);

    // Bind on document change
    $('select[name="document_option"]').on("change", showHideDocumentUpload);

    $('input[name="filing_option"]').on("change", function () {
      // Reset all parent radios (premium_addon, gold_addon, basic_addon)
      $(
        'input[name="premium_addon"], input[name="gold_addon"], input[name="basic_addon"]'
      ).prop("checked", false);

      updateOrderSummary(); // Recalculate total after reset
    });

    //Update total when any addon changes
    $(
      'input[name="addon_selection[]"], input[name="premium_addon"], input[name="gold_addon"], input[name="basic_addon"]'
    ).on("change", updateOrderSummary);

    // Form submission
    $("#accordion-checkout-form").on("submit", handleFormSubmit);

    // Filling option show or hide
    function showHideAddons() {
      let filingOptions = document.querySelectorAll(
        "input[name='filing_option']"
      );
      let addonContainers = {
        basic: document.getElementById("basic-addons"),
        gold: document.getElementById("gold-addons"),
        premium: document.getElementById("premium-addons"),
      };

      let selectedOption = document.querySelector(
        "input[name='filing_option']:checked"
      ).value;

      // Hide all addon containers
      Object.values(addonContainers).forEach((container) => {
        if (container) container.style.display = "none";
      });

      // Show only the selected addon container
      if (addonContainers[selectedOption]) {
        addonContainers[selectedOption].style.display = "block";
      }

      // updateOrderSummary();
    }

    // Function version (jQuery style, clean & modular)
    function showHideDocumentUpload() {
      var selectedValue = $('select[name="document_option"]').val();

      // Hide all
      $("#passport-upload, #driving-upload").hide();

      // Show selected one
      if (selectedValue === "passport") {
        $("#passport-upload").show();
      } else if (selectedValue === "driving") {
        $("#driving-upload").show();
      }
    }

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
      if ($(".member-entry").length > 1 || $(".manager-entry").length > 0) {
        $(event.target).closest(".member-entry").remove();
      } else {
        alert("You must have at least one member or one manager.");
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
      if ($(".manager-entry").length > 1 || $(".member-entry").length > 0) {
        $(event.target).closest(".manager-entry").remove();
      } else {
        alert("You must have at least one member or one manager.");
      }
    }

    function updateOrderSummary() {
      let orderTotal = 0; // Start from zero

      $(".summary-item").remove(); // Clear previous summary

      var addonPrice = 0;

      // Handle Addons (checkboxes and radios)
      $(
        'input[name="addon_selection[]"]:checked, input[name="premium_addon"]:checked, input[name="gold_addon"]:checked, input[name="basic_addon"]:checked'
      ).each(function () {
        var addonPrice = parseFloat($(this).data("price")) || 0;
        orderTotal += addonPrice;

        // Determine group/tab name
        let tab = "";
        if ($(this).attr("name") === "basic_addon") {
          tab = "Basic";
        } else if ($(this).attr("name") === "gold_addon") {
          tab = "Gold";
        } else if ($(this).attr("name") === "premium_addon") {
          tab = "Premium";
        } else if ($(this).attr("name") === "addon_selection[]") {
          tab = "Business";
        }

        console.log(tab);

        $(".summary-items").append(`
              <div class="summary-item addon-item">
                  <span class="item-name">${$(this)
                    .closest(".addon-option")
                    .find("h3")
                    .text()} ( ${tab} )</span>
                  <span class="item-price">$${addonPrice.toFixed(2)}</span>
              </div>
          `);
      });

      // Update total price display
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
      const form = document.getElementById("accordion-checkout-form");
      const formData = new FormData(form);
      const $submitButton = $("#submit-button");

      // Handle document validation
      const documentOption = formData.get("document_option");
      if (documentOption) {
        const fileField = `${documentOption}_file`;
        if (!formData.get(fileField)) {
          alert(`Please upload ${documentOption.replace("_", " ")} document`);
          return;
        }
      }

      // Handle order summary
      let orderSummary = [];
      $(".order-summary .summary-item").each(function () {
        const $item = $(this);
        const name = $item.find(".item-name").text().trim();
        const price = parseFloat(
          $item
            .find(".item-price")
            .text()
            .replace(/[^\d.]/g, "")
        );

        if (name && !isNaN(price)) {
          orderSummary.push({ name, price });
        }
      });

      formData.append("order_summary", JSON.stringify(orderSummary));
      formData.append(
        "total_price",
        parseFloat(
          $("#order-total")
            .text()
            .replace(/[^\d.]/g, "")
        )
      );

      // Add product ID if missing
      if (!formData.has("product_id") && wc_add_to_cart_params?.product_id) {
        formData.append("product_id", wc_add_to_cart_params.product_id);
      }

      // Add security nonce
      formData.append("security", $("#custom_form_nonce").val());
      formData.append("action", "custom_add_to_cart");

      $.ajax({
        type: "POST",
        url: wc_add_to_cart_params?.ajax_url || "/wp-admin/admin-ajax.php",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: () => {
          $submitButton.prop("disabled", true).text("Processing...");
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
        error: (xhr) => {
          alert(`Error: ${xhr.statusText}`);
        },
        complete: () => {
          $submitButton.prop("disabled", false).text("Submit Order");
        },
      });
    }

    // function submitForm() {
    //   const form = document.getElementById("accordion-checkout-form");
    //   const formData = new FormData(form);

    //   // Add WooCommerce-specific data
    //   formData.append("action", "custom_add_to_cart");
    //   formData.append(
    //     "security",
    //     document.getElementById("custom_form_nonce").value
    //   );

    //   // Handle document validation
    //   const documentOption = formData.get("document_option");
    //   if (documentOption) {
    //     const fileField = `${documentOption}_file`;
    //     if (!formData.get(fileField)) {
    //       alert(`Please upload ${documentOption.replace("_", " ")} document`);
    //       return;
    //     }
    //   }

    //   // Handle order summary
    //   const orderSummary = [];
    //   document
    //     .querySelectorAll(".order-summary .summary-item")
    //     .forEach((item) => {
    //       const name = item.querySelector(".item-name").textContent.trim();
    //       const price = parseFloat(
    //         item.querySelector(".item-price").textContent.replace(/[^\d.]/g, "")
    //       );
    //       if (name && !isNaN(price)) {
    //         orderSummary.push({ name, price });
    //       }
    //     });

    //   // Add structured data
    //   formData.append("order_summary", JSON.stringify(orderSummary));
    //   formData.append(
    //     "total_price",
    //     parseFloat(
    //       document
    //         .getElementById("order-total")
    //         .textContent.replace(/[^\d.]/g, "")
    //     )
    //   );

    //   // Add product ID if missing
    //   if (!formData.has("product_id") && wc_add_to_cart_params?.product_id) {
    //     formData.append("product_id", wc_add_to_cart_params.product_id);
    //     //formData.append("product_id", 104);
    //   }

    //   // Submit the request
    //   fetch(wc_add_to_cart_params?.ajax_url || "/wp-admin/admin-ajax.php", {
    //     method: "POST",
    //     body: formData,
    //     headers: {
    //       "X-Requested-With": "XMLHttpRequest",
    //     },
    //   })
    //     .then((response) => response.json())
    //     .then(handleResponse)
    //     .catch(handleError);

    // }

    // function submitForm() {
    //   const formData = $("#accordion-checkout-form").serializeArray();
    //   const cartData = formData.filter((item) => item.value);

    //   // Add product ID if missing
    //   if (
    //     wc_add_to_cart_params?.product_id &&
    //     !formData.some((item) => item.name === "product_id")
    //   ) {
    //     cartData.push({
    //       name: "product_id",
    //       value: wc_add_to_cart_params.product_id,
    //     });
    //   }

    // $.ajax({
    //   type: "POST",
    //   url: wc_add_to_cart_params?.ajax_url || "/wp-admin/admin-ajax.php",
    //   data: {
    //     action: "custom_add_to_cart",
    //     cart_items: cartData,
    //     security: $("#custom_form_nonce").val(),
    //   },
    //   beforeSend: () => {
    //     $("#submit-button").prop("disabled", true).text("Processing...");
    //   },
    //   success: (response) => {
    //     if (response.success) {
    //       window.location.href =
    //         response.data?.redirect ||
    //         wc_add_to_cart_params?.checkout_url ||
    //         "/cart/";
    //     } else {
    //       alert(
    //         response.data?.message ||
    //           "Error adding to cart. Please try again."
    //       );
    //     }
    //   },
    //   error: () => {
    //     alert("An error occurred. Please try again.");
    //   },
    //   complete: () => {
    //     $("#submit-button").prop("disabled", false).text("Submit Order");
    //   },
    // });

    // }

    // function submitForm() {
    //   const formData = $("#accordion-checkout-form").serializeArray();
    //   const cartData = formData.filter((item) => item.value);

    //   // Extract order summary details dynamically
    //   let orderSummary = [];
    //   $(".order-summary .summary-item").each(function () {
    //     let itemName = $(this).find(".item-name").text().trim();
    //     let itemPrice = $(this)
    //       .find(".item-price")
    //       .text()
    //       .trim()
    //       .replace("$", "");

    //     if (itemName && itemPrice) {
    //       orderSummary.push({ name: itemName, price: parseFloat(itemPrice) });
    //     }
    //   });

    //   let totalPrice = parseFloat(
    //     $("#order-total").text().trim().replace("$", "")
    //   );

    //   // Add product ID if missing
    //   if (
    //     wc_add_to_cart_params?.product_id &&
    //     !formData.some((item) => item.name === "product_id")
    //   ) {
    //     cartData.push({
    //       name: "product_id",
    //       value: wc_add_to_cart_params.product_id,
    //     });
    //   }

    //   $.ajax({
    //     type: "POST",
    //     url: wc_add_to_cart_params?.ajax_url || "/wp-admin/admin-ajax.php",
    //     data: {
    //       action: "custom_add_to_cart",
    //       cart_items: cartData,
    //       order_summary: orderSummary,
    //       total_price: totalPrice,
    //       security: $("#custom_form_nonce").val(),
    //     },
    //     beforeSend: () => {
    //       $("#submit-button").prop("disabled", true).text("Processing...");
    //     },
    //     success: (response) => {
    //       if (response.success) {
    //         window.location.href =
    //           response.data?.redirect ||
    //           wc_add_to_cart_params?.checkout_url ||
    //           "/cart/";
    //       } else {
    //         alert(
    //           response.data?.message ||
    //             "Error adding to cart. Please try again."
    //         );
    //       }
    //     },
    //     error: () => {
    //       alert("An error occurred. Please try again.");
    //     },
    //     complete: () => {
    //       $("#submit-button").prop("disabled", false).text("Submit Order");
    //     },
    //   });
    // }

    function validateSection(sectionId) {
      let isValid = true;
      let errorMessage = "";

      console.log(sectionId);

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
          const memberCount = $(".member-entry").length;
          const managerCount = $(".manager-entry").length;

          // Step 1: Ensure at least one member or manager exists
          if (memberCount === 0 && managerCount === 0) {
            isValid = false;
            errorMessage = "You must add at least one member or one manager.";
            break; // Stop further validation if none exists
          }

          // Step 2: Validate names for members and managers
          let hasValidEntry = false;

          $(".member-entry").each(function () {
            const $name = $(this).find('input[name="member_name[]"]');
            if (!$name.val().trim()) {
              $name.addClass("error");
              isValid = false;
            } else {
              $name.removeClass("error");
              hasValidEntry = true;
            }
          });

          $(".manager-entry").each(function () {
            const $name = $(this).find('input[name="manager_name[]"]');
            if (!$name.val().trim()) {
              $name.addClass("error");
              isValid = false;
            } else {
              $name.removeClass("error");
              hasValidEntry = true;
            }
          });

          if (!hasValidEntry) {
            isValid = false;
            errorMessage =
              "At least one member or manager must have a valid name.";
          }
          break;

        case 4:
          const $documentSection = $(".document-upload");
          const documentOption = $('select[name="document_option"]').val();
          let fileUploaded = false;

          // Only validate if document option is selected
          if (documentOption) {
            const $fileInput = $(`#${documentOption}_file`);
            const fileName = $fileInput.val().trim();

            if (!fileName) {
              isValid = false;
              errorMessage = `Please upload ${documentOption.replace(
                /_/g,
                " "
              )} document.`;
              $documentSection.addClass("error");
              $fileInput.closest(".document-input").addClass("error");
            } else {
              // Validate file extension
              const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.pdf)$/i;
              if (!allowedExtensions.exec(fileName)) {
                isValid = false;
                errorMessage =
                  "Invalid file type. Allowed formats: JPG, PNG, PDF.";
                $documentSection.addClass("error");
                $fileInput.closest(".document-input").addClass("error");
              } else {
                $documentSection.removeClass("error");
                $fileInput.closest(".document-input").removeClass("error");
                fileUploaded = true;
              }
            }
          }

          // If document option selected but no file uploaded
          if (documentOption && !fileUploaded) {
            isValid = false;
            if (!errorMessage) {
              errorMessage = "Please upload the required document.";
            }
          }
          break;

        case 5:
          const $fillingSection = $(".filing-option label");
          const $basicChecked = $('input[name="basic_addon"]:checked');
          const $goldChecked = $('input[name="gold_addon"]:checked');
          const $premiumChecked = $('input[name="premium_addon"]:checked');

          if (
            !$basicChecked.length &&
            !$goldChecked.length &&
            !$premiumChecked.length
          ) {
            isValid = false;
            errorMessage = "Please select at least one option.";
            $fillingSection.addClass("error"); // Add error styling
          } else {
            errorMessage = "";
            $fillingSection.removeClass("error"); // Remove error styling if valid
          }
          break;

        case 6:
          const $addonSection = $(".business-addons");
          const $addonsChecked = $('input[name="addon_selection[]"]:checked');
          console.log($addonsChecked);

          if (!$addonsChecked.length) {
            isValid = false;
            errorMessage = "Please select at least one business addon.";
            $addonSection.addClass("error"); // Add error styling
          } else {
            $addonSection.removeClass("error"); // Remove error styling if valid
          }
          break;
      }

      if (!isValid && errorMessage) alert(errorMessage);

      // Existing error handling
      if (!isValid && errorMessage) {
        alert(errorMessage);
        // Scroll to error section
        $(`[data-section="${sectionId}"]`).get(0)?.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });
      }

      return isValid;
    }
  });
})(jQuery);
