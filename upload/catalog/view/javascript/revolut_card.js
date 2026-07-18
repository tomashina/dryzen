waitForjQuery()
  .then(() => waitForRevolutCheckout())
  .then(() => fetchMethodParams("revolut_card"))
  .then((params) => {
    var instanceUpsell = null;
    const billing_address = params.order.billing_address;
    const payWithPopup = async (public_id) => {
      const RC = RevolutCheckout(public_id, params.mode);

      RC.then(function (instance) {
        var popup = instance.payWithPopup({
          name: `${billing_address.firstname} ${billing_address.lastname}`,
          email: billing_address.email,
          phone: billing_address.telephone,
          billingAddress: {
            countryCode: billing_address.iso_code_2,
            region: billing_address.zone,
            city: billing_address.city,
            streetLine1: billing_address.address_1,
            streetLine2: billing_address.address_2,
            postcode: billing_address.postcode,
          },
          onSuccess() {
            pollOrderForAuthorisation(public_id, "revolut_card")
              .then(() => completeOrder())
              .catch((err) => {
                alert(err);
                $("#button-confirm").button("reset");
              });
          },
          onError(message) {
            alert(message);
            popup.destroy();
            $("#button-confirm").button("reset");
          },
          onCancel() {
            popup.destroy();
            $("#button-confirm").button("reset");
          },
        });
      });
    };

    function handleSuccess(json) {
      const { success, error, redirect } = json || {};

      if (success) {
        const navigate = () => {
          window.location = redirect;
        };

        if (params.styles.revolut_card.widget_type) {
          setTimeout(navigate, 1500);
        } else {
          navigate();
        }
      } else {
        alert(error || "Unknown error occurred");
        $("#button-confirm").button("reset");
      }
    }

    const mountCardField = async (public_id) => {
      const instance = await RevolutCheckout(public_id, params.mode);
      const card = instance.createCardField({
        hidePostcodeField: true,
        orderUpdated: true,
        target: document.getElementById("revolut-card-field"),
        styles: {
          default: {
            color: params.styles.revolut_card.payment_revolut_card_font_colour,
            "::placeholder": {
              color:
                params.styles.revolut_card.payment_revolut_card_font_colour,
            },
          },
        },
        onSuccess() {
          pollOrderForAuthorisation(public_id, "revolut_card")
            .then(() => completeOrder(public_id, handleSuccess))
            .catch((err) => {
              alert(err);
              instance.destroy();
              $("#button-confirm").button("reset");
            });
        },
        onValidation(errors) {
          if (errors.length) {
            $("#button-confirm").button("reset");
            $("#revolut-card-error").html(errors[0].message);
          } else {
            $("#revolut-card-error").html("");
          }
        },
        onError(message) {
          alert(message);
          $("#button-confirm").button("reset");
        },
        onCancel() {
          $("#button-confirm").button("reset");
        },
      });

      $("#button-confirm").on("click", function () {
        $("#button-confirm").button("loading");

        card.submit({
          name: `${billing_address.firstname} ${billing_address.lastname}`,
          email: billing_address.email,
          phone: billing_address.telephone,
          billingAddress: {
            countryCode: billing_address.iso_code_2,
            region: billing_address.zone,
            city: billing_address.city,
            streetLine1: billing_address.address_1,
            streetLine2: billing_address.address_2,
            postcode: billing_address.postcode,
          },
        });
      });
    };

    createRevOrder("revolut_card").then((publicId) => {
      if (!publicId) {
        alert("Failed to initialize payment. Please try again later.");
        $("#button-confirm").hide();
        return;
      }

      if (params.styles.revolut_card.widget_type === "popup") {
        $("#button-confirm").on("click", function () {
          payWithPopup(publicId);
        });
        return;
      }

      mountCardField(publicId);

      if (params.order.upsell_banner_enabled) {
        const initCheckoutUpsellBanner = (params, publicId) => {
          let upsellBannerElement = document.getElementById(
            "revolut-upsell-banner"
          );
          if (instanceUpsell != null) {
            instanceUpsell.destroy();
          }
          instanceUpsell = RevolutUpsell({
            locale: "auto",
            mode: params.mode,
            publicToken: params.public_token,
            channel: "opencart",
          });
          instanceUpsell.cardGatewayBanner.mount(upsellBannerElement, {
            orderToken: publicId,
          });
        };

        var upsellScript = document.createElement("script");
        upsellScript.src = params.upsell_embed_script_url;
        upsellScript.onload = function () {
          initCheckoutUpsellBanner(params, publicId);
        };

        document.head.appendChild(upsellScript);
      }
    });
  })
  .catch((err) => {
    alert(err);
    $("#button-confirm").hide();
  });
