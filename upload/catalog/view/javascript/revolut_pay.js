waitForjQuery()
  .then(($) => waitForRevolutCheckout())
  .then(() => fetchMethodParams("revolut_pay"))
  .then((params) => {
    $("#revolut-pay-overlay img").attr("src", params.styles.loader);

    function initRevolutPay(params) {
      const instance = RevolutCheckout.payments({
        locale: "en",
        publicToken: params.public_token,
      });

      const paymentOptions = {
        currency: params.order.currency,
        totalAmount: parseInt(params.order.amount),
        createOrder: async () => {
          const publicId = await createRevOrder("revolut_pay");
          if (!publicId) {
            instance.destroy();
            handleError([
              new Error(
                "Failed to initialize payment. Please try again later."
              ),
            ]);

            return null;
          }
          return { publicId: publicId };
        },
        mobileRedirectUrls: {
          success: params.mobile_redirection_url,
          failure: params.mobile_redirection_url,
          cancel: params.mobile_redirection_url,
        },
        __metadata: {
          environment: "opencart3",
          context: "checkout",
          origin_url: params.base_url,
        },
        buttonStyle: {
          cashbackCurrency: params.order.currency,
          variant: params.styles.revolut_pay.payment_revolut_pay_theme,
          size: params.styles.revolut_pay.payment_revolut_pay_size,
          radius: params.styles.revolut_pay.payment_revolut_pay_radius,
        },
      };

      instance.revolutPay.mount(
        document.getElementById("revolut-pay-field"),
        paymentOptions
      );

      instance.revolutPay.on("payment", function (event) {
        switch (event.type) {
          case "success":
            $("#revolut-pay-overlay").show();
            pollOrderForAuthorisation(event.orderId, "revolut_pay")
              .then(() => completeOrder(event.orderId, handleSuccess))
              .catch((err) => {
                instance.destroy();
                handleError([err]);
                $("#revolut-pay-overlay").hide();
              });
            break;
          case "error":
            handleError([event.error.message].filter(Boolean));
            break;
        }
      });
    }

    function handleError(errors) {
      if (errors.length) {
        $("#revolut-pay-error").html("");
        $("#revolut-pay-error").append(errors[0].message);
      } else {
        $("#revolut-pay-error").html("");
      }
    }

    function handleSuccess(json) {
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
      $("#revolut-pay-overlay").hide();
    }

    initRevolutPay(params);
  })
  .catch((err) => {
    alert(err);
  });
