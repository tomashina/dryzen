window.handleFetchResponse = async (response) => {
  if (!response.ok) {
    throw new Error("Unexpected error occurred");
  }

  const json = await response.json();

  if (json.error) {
    throw new Error(json.error);
  }

  return json;
};

window.fetchMethodParams = async (method) => {
  const url = `/index.php?route=extension/payment/${method}/params`;
  const response = await fetch(url);
  return handleFetchResponse(response);
};

window.fetchOrderStatus = async (publicId, method) => {
  const url = `/index.php?route=extension/payment/${method}/check_order_status`;
  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ public_id: publicId }).toString(),
  });

  return handleFetchResponse(response);
};

window.pollOrderForAuthorisation = (publicId, method) => {
  return new Promise((resolve, reject) => {
    let elapsed = 0;

    const poll = () => {
      return fetchOrderStatus(publicId, method)
        .then((order) => {
          if (!order || !order.state) {
            reject(new Error("Unable to fetch order status"));
            return;
          }

          if (order.state.toLowerCase() === "authorised") {
            resolve(true);
            return;
          }

          elapsed += 1000;
          if (elapsed >= 10000) {
            reject(new Error("Payment authorisation is taking too long"));
            return;
          }

          setTimeout(poll, 1000);
        })
        .catch((err) => {
          reject(err);
        });
    };

    poll();
  });
};

window.waitForjQuery = () =>
  new Promise((resolve) => {
    const check = () => {
      if (window.jQuery) {
        return resolve(window.jQuery);
      } else {
        setTimeout(check, 50);
      }
    };

    check();
  });

window.waitForRevolutCheckout = () =>
  new Promise((resolve) => {
    const interval = setInterval(() => {
      if (typeof RevolutCheckout !== "undefined") {
        clearInterval(interval);
        resolve(RevolutCheckout);
      }
    }, 50);
  });

window.processCompletedOrder = async (publicId, method) => {
  const url = `/index.php?route=extension/payment/${method}/process_completed_order`;
  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ public_id: publicId }).toString(),
  });
  const json = await handleFetchResponse(response);

  if (!json.success) {
    throw new Error("Unable to process completed order");
  }

  if (json.redirect) {
    window.location.href = json.redirect;
    return;
  }

  throw new Error("Unknown error occurred");
};

window.createRevOrder = async (method) => {
  try {
    const url = `/index.php?route=extension/payment/${method}/create_revolut_order_ajax`;
    const response = await fetch(url);
    const json = await handleFetchResponse(response);

    if (!json.public_id) {
      throw new Error("Unable to create a payment");
    }

    if (json.state?.toLowerCase() === "completed") {
      await processCompletedOrder(json.public_id, method);
    }

    return json.public_id;
  } catch (error) {
    return null;
  }
};

window.processCapturedOrder = async (publicId) => {
  const url = `/index.php?route=extension/payment/revolut_card/process_captured_order_ajax`;
  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ public_id: publicId }).toString(),
  });
  const json = await handleFetchResponse(response);

  if (!json.success) {
    throw new Error("Unable to process completed order");
  }

  if (json.redirect) {
    window.location.href = json.redirect;
    return;
  }

  throw new Error("Unknown error occurred");
};

window.completeOrder = (
  public_id,
  handleSuccess,
  tryCount = 0,
  retryLimit = 10,
  retry = 0
) => {
  $.ajax({
    url: "index.php?route=extension/payment/revolut_card/completeOrder",
    type: "post",
    data: { public_id, retry },
    dataType: "json",
    success: (json) => {
      if (tryCount >= retryLimit) {
        return processCapturedOrder(public_id);
      }

      if (json.retry) {
        setTimeout(
          () =>
            completeOrder(
              public_id,
              handleSuccess,
              tryCount + 1,
              retryLimit,
              json.retry
            ),
          1000
        );
      } else {
        handleSuccess(json);
      }
    },
  });
};
