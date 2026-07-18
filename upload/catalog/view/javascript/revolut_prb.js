waitForjQuery()
  .then(($) => waitForRevolutCheckout())
  .then(() => fetchMethodParams("revolut_prb"))
  .then((params) => {
    $("#revolut-payment-request-overlay img").attr("src", params.styles.loader);

    async function initPaymentRequest() {
      let publicId = null;
      const { paymentRequest } = await RevolutCheckout.payments({
        locale: "en",
        publicToken: params.public_token,
      });

      const options = {
        currency: params.order.currency,
        amount: params.order.amount,
        requestShipping: false,
        onSuccess: () => {
          $("#revolut-payment-request-overlay").show();
          pollOrderForAuthorisation(publicId, "revolut_prb")
            .then(() => completeOrder(publicId, handleSuccess))
            .catch((err) => {
              instance.destroy();
              alert(err);
              $("#button-confirm").button("reset");
            });
        },
        createOrder: async () => {
          publicId = await createRevOrder("revolut_prb");
          if (!publicId) {
            throw new Error(
              "Failed to initialize payment. Please try again later."
            );
          }
          return { publicId: publicId };
        },
        validate: (address) => {},
        onError: (error) => {
          handleError([error]);
        },
        buttonStyle: {
          height: "50px",
          action: params.styles.revolut_prb.payment_revolut_prb_action,
          size: params.styles.revolut_prb.payment_revolut_prb_size,
          variant: params.styles.revolut_prb.payment_revolut_prb_theme,
          radius: params.styles.revolut_prb.payment_revolut_prb_radius,
        },
      };

      const instance = paymentRequest(
        document.getElementById("revolut-prb-field"),
        options
      );
      const method = await instance.canMakePayment();
      if (method) {
        instance.render();
      } else {
        instance.destroy();
      }
    }

    function handleError(errors) {
      if (errors.length) {
        $("#revolut-prb-error").html("");
        $("#revolut-prb-error").append(errors[0].message);
      } else {
        $("#revolut-prb-error").html("");
      }
    }

    const handleSuccess = (json) => {
      if (json && json["success"]) {
        window.location = json["redirect"];
        return;
      } else if (json && json["error"]) {
        alert(json["error"]);
      } else {
        alert(
          "Unknown error occurred! Please try again or contact us for assistance"
        );
      }
      $("#revolut-payment-request-overlay").hide();
    };

    initPaymentRequest();
  })
  .catch((err) => {
    alert(err);
  });
