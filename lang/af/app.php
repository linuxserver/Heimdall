<?php

return array (
  // Settings Section
  'settings.system' => 'Stelsel',
  'settings.appearance' => 'Voorkoms',
  'settings.miscellaneous' => 'Allerlei', // 'Allerlei' is often more natural for 'Miscellaneous' settings than 'Diverse'.
  'settings.advanced' => 'Gevorderd',
  'settings.support' => 'Ondersteuning',
  'settings.donate' => 'Skenk', // Or 'Maak \'n Skenking' for verbosity. 'Skenk' is fine.
  'settings.version' => 'Weergawe',
  'settings.background_image' => 'Agtergrondbeeld',
  'settings.trianglify' => 'Trianglify', // Keep proper name/library name untranslated.
  'settings.trianglify_seed' => 'Trianglify Saad', // Keep 'Trianglify'. 'Saad' is literal; 'Beginwaarde' (Initial Value) could also work but 'Saad' is acceptable here. Removed 'Ewekansige' (Random) for brevity, assuming context implies randomness.
  'settings.window_target' => 'Open skakels in:', // More active and common phrasing.
  'settings.window_target.current' => 'Maak in hierdie oortjie oop',
  'settings.window_target.one' => 'Maak in dieselfde oortjie oop',
  'settings.window_target.new' => 'Maak in \'n nuwe oortjie oop',
  'settings.homepage_search' => 'Tuisbladsoektog', // Compound noun, usually one word.
  'settings.search_provider' => 'Versteksoekverskaffer', // Compound noun. 'Standaard' is also usable instead of 'Verstek'.
  'settings.language' => 'Taal',
  'settings.reset' => 'Herstel na verstek', // Or 'Herstel verstekwaardes'. Original is acceptable.
  'settings.remove' => 'Verwyder',
  'settings.search' => 'Soek', // Capitalized as it might be a button/label.
  'settings.no_items' => 'Geen items gevind nie.', // Added 'nie' for correct negation sentence structure.
  'settings.label' => 'Etiket',
  'settings.value' => 'Waarde',
  'settings.edit' => 'Wysig',
  'settings.view' => 'Besigtig',
  'settings.custom_css' => 'Aangepaste CSS', // Keep 'CSS'.
  'settings.custom_js' => 'Aangepaste JavaScript', // Keep 'JavaScript'.
  'settings.treat_tags_as' => 'Hanteer etikette as:', // Lowercase 'etikette' and 'as' for label style.
  'settings.folders' => 'Vouers', // 'Omslae' is also common. 'Vouers' is fine.
  'settings.tags' => 'Etikette', // 'Merkers' is also common. 'Etikette' is fine.
  'settings.categories' => 'Kategorieë',

  // Options Section
  'options.none' => '- geen -', // Simpler than 'nie gestel nie'.
  'options.google' => 'Google', // Keep proper name.
  'options.ddg' => 'DuckDuckGo', // Keep proper name.
  'options.bing' => 'Bing', // Keep proper name.
  'options.qwant' => 'Qwant', // Keep proper name.
  'options.startpage' => 'StartPage', // Keep proper name.
  'options.yes' => 'Ja',
  'options.no' => 'Nee',
  'options.nzbhydra' => 'NZBHydra', // Keep technical name.
  'options.jackett' => 'Jackett', // Keep technical name.

  // Buttons Section
  'buttons.save' => 'Stoor',
  'buttons.cancel' => 'Kanselleer',
  'buttons.add' => 'Voeg by',
  'buttons.upload' => 'Laai ikoon op', // Slightly more concise.
  'buttons.downloadapps' => 'Dateer toepassingslys op', // Corrected 'Appelys' to 'toepassingslys'. 'Bywerk' -> 'Opdateer' is slightly more common for software updates.

  // Dashboard Section
  'dash.pin_item' => 'Speld item aan paneelbord vas', // Added 'vas' for the pinning action.
  'dash.no_apps' => 'Geen vasgespelde toepassings tans nie. :link1 of :link2', // Corrected 'gespelde' to 'vasgespelde' and improved flow.
  'dash.link1' => 'Voeg \'n toepassing hier by', // Slightly better word order.
  'dash.link2' => 'Speld \'n item aan die paneelbord vas', // Added 'vas'.
  'dash.pinned_items' => 'Vasgespelde Items', // Corrected 'Gespelde'.

  // Applications Section
  'apps.app_list' => 'Toepassingslys',
  'apps.view_trash' => 'Bekyk asblik',
  'apps.add_application' => 'Voeg toepassing by',
  'apps.application_name' => 'Toepassingsnaam',
  'apps.colour' => 'Kleur',
  'apps.icon' => 'Ikoon',
  'apps.pinned' => 'Vasgespeld', // Consistent use of 'vasgespeld'.
  'apps.title' => 'Titel',
  'apps.hex' => 'Heks-kleurkode', // More descriptive than 'Hex kleur'.
  'apps.username' => 'Gebruikersnaam',
  'apps.password' => 'Wagwoord',
  'apps.config' => 'Konfigurasie', // Avoid abbreviation 'Konfig'.
  'apps.apikey' => 'API-sleutel', // Hyphenated compound noun, Keep 'API'.
  'apps.enable' => 'Aktiveer',
  'apps.tag_list' => 'Etikettelys',
  'apps.add_tag' => 'Voeg etiket by',
  'apps.tag_name' => 'Etiketnaam',
  'apps.tags' => 'Etikette',
  'apps.override' => 'Oorskryf URL (indien anders as hoof-URL)', // Clearer meaning.
  'apps.preview' => 'Voorskou',
  'apps.apptype' => 'Toepassingstipe',
  'apps.website' => 'Webwerf', // 'Webtuiste' is also possible.
  'apps.description' => 'Beskrywing',
  'apps.only_admin_account' => 'Slegs vir administrateurrekeninge!', // More concise and common phrasing.
  'apps.autologin_url' => 'URL vir outomatiese aantekening', // Corrected 'url' capitalization and phrasing.
  'apps.show_deleted' => 'Wys verwyderde toepassings', // Sentence case for checkbox label.

  // General UI Elements / Actions
  'app.import' => 'Voer in', // Using the verb form, consistent with 'Uitvoer'. Assuming this refers to the action/button.
  'dashboard' => 'Paneelbord', // 'Tuispaneelbord' is okay, but 'Paneelbord' is often sufficient if 'Tuis' context is clear.
  'user.user_list' => 'Gebruikerslys', // More specific than just 'Gebruikers'.
  'user.add_user' => 'Voeg gebruiker by',
  'user.username' => 'Gebruikersnaam',
  'user.avatar' => 'Avatar',
  'user.email' => 'E-posadres', // Slightly more specific than just 'E-pos'.
  'user.password_confirm' => 'Bevestig wagwoord', // Sentence case.
  'user.secure_front' => 'Beveilig publieke toegang tot koppelvlak', // Shorter label.
  'user.secure_front_tooltip' => 'Word slegs toegepas indien \'n wagwoord vir die hoofgebruiker gestel is.', // Separated explanation for clarity (assuming it could be a tooltip or help text).
  'user.autologin' => 'Laat aanteken via \'n spesifieke URL toe.', // Slightly better phrasing.
  'user.autologin_tooltip' => 'Enigiemand met die skakel kan aanteken.', // Separated explanation.
  'url' => 'URL', // Keep capitalized.
  'title' => 'Titel',
  'delete' => 'Verwyder',
  'optional' => 'Opsioneel',
  'restore' => 'Herstel',
  'export' => 'Voer uit', // Verb form, consistent with 'Voer in'.
  'import' => 'Voer in', // Verb form.

  // Alerts Section
  'alert.success.item_created' => 'Item suksesvol geskep.',
  'alert.success.item_updated' => 'Item suksesvol bygewerk.', // 'Bygewerk' (updated) often fits better than 'opgedateer' for items.
  'alert.success.item_deleted' => 'Item suksesvol verwyder.',
  'alert.success.item_restored' => 'Item suksesvol herstel.',
  'alert.success.updating' => 'Dateer toepassingslys op...', // Consistent with button text.
  'alert.success.tag_created' => 'Etiket suksesvol geskep.',
  'alert.success.tag_updated' => 'Etiket suksesvol bygewerk.',
  'alert.success.tag_deleted' => 'Etiket suksesvol verwyder.',
  'alert.success.tag_restored' => 'Etiket suksesvol herstel.',
  'alert.success.setting_updated' => 'Instelling suksesvol bygewerk.', // More concise than the original.
  'alert.error.not_exist' => 'Hierdie instelling bestaan nie.',
  'alert.error.file_too_big' => 'Die lêer is te groot.', // Added 'Die'.
  'alert.error.file_not_stored' => 'Kon nie lêer stoor nie.', // More direct phrasing.
  'alert.success.user_created' => 'Gebruiker suksesvol geskep.',
  'alert.success.user_updated' => 'Gebruiker suksesvol bygewerk.',
  'alert.success.user_deleted' => 'Gebruiker suksesvol verwyder.',
  'alert.success.user_restored' => 'Gebruiker suksesvol herstel.',

  // Dashboard Actions
  'dashboard.reorder' => 'Herrangskik en speld items vas', // Clarified action.
  'dashboard.settings' => 'Instellings',
);
