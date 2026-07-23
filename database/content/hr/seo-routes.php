<?php

return array(
    'language_id' => 3,
    'store_id' => 0,
    'categories' => array(
        1 => 'proizvodi',
    ),
    'products' => array(
        6 => 'dry-zen-maramice-za-ruke-antiperspirant-maramice-za-ruke',
        9 => 'dryzen-maramice-za-ruke-za-muskarce',
        10 => 'dryzen-maramice-za-ruke-sport',
        11 => 'dryzen-kids-maramice-za-ruke',
        12 => 'dryzen-maramice-za-stopala-za-zene',
        13 => 'dryzen-maramice-za-stopala-za-muskarce',
        14 => 'dryzen-maramice-za-stopala-sport',
        15 => 'dryzen-maramice-za-stopala-za-djecu',
        16 => 'dryzen-roll-on-za-zene',
        17 => 'dryzen-roll-on-za-muskarce',
        18 => 'dryzen-roll-on-sport',
        19 => 'dryzen-kids-roll-on',
        20 => 'dryzen-sprej-za-obucu',
    ),
    'routes' => array(
        'information/contact' => 'kontakt',
    ),
    'route_meta' => array(
        'information/contact' => array(
            'meta_title' => 'Kontakt | DryZen',
            'meta_description' => 'Kontaktiraj DryZen za pitanja o proizvodima, narudžbi ili korištenju. Javi nam se obrascem, telefonom ili e-mailom.',
            'meta_keyword' => '',
        ),
    ),
    'settings' => array(
        // The richer OnlineStore schema already covers the organization. Disable
        // the older duplicate Store block with placeholder contact information.
        'hb_snippets_local_enable' => '0',
    ),
);
