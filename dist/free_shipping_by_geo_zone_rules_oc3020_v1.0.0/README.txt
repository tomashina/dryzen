FREE SHIPPING BY GEO ZONE RULES
Version: 1.0.0
Target: OpenCart 3.0.2.0

INSTALLATION

1. In OpenCart Admin open Extensions > Installer.
2. Upload free_shipping_by_geo_zone_rules_oc3020_v1.0.0.ocmod.zip.
3. Open Extensions > Extensions and choose "Shipping" as the extension type.
4. Find "Free Shipping by Geo Zone Rules" and click Install.
5. Click Edit, add the rules, enable the method and save.

RULES

- Rules are evaluated from top to bottom.
- The first enabled rule whose Geo Zone matches the shipping address is used.
- The free method is shown only when the product subtotal reaches that rule's
  minimum subtotal.
- "All Zones" can be placed last as a fallback rule.
- Use the up/down buttons to change rule priority.
- A minimum subtotal of 0 makes shipping free without a minimum.
- The subtotal is the product subtotal before tax, shipping and order-total
  adjustments, matching OpenCart's standard Free Shipping method.

IMPORTANT

- Create Geo Zones first under System > Localisation > Geo Zones.
- Disable OpenCart's standard Free Shipping method if you do not want two free
  shipping methods to appear.
- Croatian translations are included for both common folder names: hr-HR and
  hr-hr.
- If your store uses another language, copy the en-gb language files into that
  language folder and translate their values.

UNINSTALLATION

1. Open Extensions > Extensions > Shipping and uninstall this shipping method.
2. Open Extensions > Installer > Install History and uninstall/remove the
   uploaded package if you also want its files removed.

No OpenCart core file is overwritten.
