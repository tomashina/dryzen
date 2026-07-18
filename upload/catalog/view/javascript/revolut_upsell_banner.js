upsellElement = document.createElement("div");

async function fetchUpsellParams() {
  const urlParams = new URLSearchParams(window.location.search);
  const orderId = urlParams.get("orderId");

  let controllerUrl = `/index.php?route=extension/payment/revolut_card/upsellBannerHandler&orderId=${orderId}`;
  let response = await fetch(controllerUrl);
  let json = await response.json();
  return json;
}

async function initWidget(publicToken, publicId) {
  const { enrollmentConfirmationBanner } = await RevolutUpsell({
    publicToken: publicToken,
    locale: "auto",
    __metadata: { channel: "opencart" },
  });
  enrollmentConfirmationBanner.mount(upsellElement, {
    orderToken: publicId,
    promotionalBanner: true,
  });
}

window.addEventListener("DOMContentLoaded", () => {
  staticElement = document.getElementById("content");
  if (staticElement) {
    fetchUpsellParams().then((params) => {

      if(! params.enabled) {
        return;
      }

      staticElement.prepend(upsellElement);
      initWidget(params.token, params.public_id);
    });
  }
});
