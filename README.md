# Tata Surgical WordPress theme, version 2.5

Custom B2B theme for tatasurgical.com. WooCommerce runs in catalogue mode: buyers build a quote list instead of a cart.
Works with Elementor (and Elementor Pro Theme Builder) and Yoast SEO.

## Install
1. Back up the current site (files and database). Existing pages are not changed, but a backup is always wise.
2. Appearance > Themes > Add New > Upload Theme > tata-surgical.zip > Install > Activate.
   Widgets from the old theme are moved to "Inactive widgets" (Appearance > Widgets). Nothing is deleted.
3. Deactivate the Joinchat plugin. The theme has its own WhatsApp chat.

## What happens when you activate the theme (first time only)
1. Any of these pages that do not exist yet are created, empty, ready for Elementor:
   Home, Products, Custom manufacturing, OEM & private label, Quality & certifications, About us,
   Trade fairs & events, Catalogues, News, Request a quote, Contact, Privacy policy,
   Terms of supply, Complaints and returns.
   Pages that already exist (same address, e.g. /contact/) are kept exactly as they are.
   Request a quote, Custom manufacturing and Contact use the theme's form templates.
2. If not already set: Home = homepage, News = posts page, Products = WooCommerce shop page,
   Privacy policy = privacy page. Settings you already have are not changed.
3. Menus are created and assigned (menus with the same names are replaced):
   - Main menu: Custom manufacturing, Quality, Company (dropdowns with icons and descriptions), Contact.
     The Products mega menu is always added first, built from your product categories.
   - Footer: Services, Footer: Company, Footer: Legal.
   Some menu links point to sections such as /quality/#clean-room. In Elementor, give that section
   the matching CSS ID (clean-room, certifications, process, sterile-packing, custom-kits).
4. Activating the theme again later changes nothing. To rebuild pages and menus on purpose, use
   Appearance > Tata Surgical setup > Run setup again.

## Set up (about 10 minutes)
1. Appearance > Customize > Tata Surgical: check contact details, next exhibition, markets, certifications and footer text.
   "Send enquiries to" sets where quote requests are emailed.
2. Products > Categories: drag to set the order shown in menus (Diagnostic, Single Use Surgical, Ophthalmic, Reusable, Surgical Sets, Dental, Veterinary). Edit a category to choose its menu icon,
   a one-line description and a badge such as "New".
3. WooCommerce > Settings > Products: set the dimensions unit to cm.
4. Appearance > Menus: edit the menus the theme created. Sub-items become dropdowns.
   Products is always added first automatically. For a dropdown item icon, add the CSS class
   tata-icon-i-pencil (or i-tag, i-box, i-shield, i-cleanroom, i-factory, i-users, i-calendar, i-news).
   Turn on "Description" under Screen Options to add the grey line under each item.
   Footer menus: Footer: Services, Footer: Company, Footer: Legal. Defaults are shown until you assign them.
5. Yoast SEO > Settings > Breadcrumbs: enable them to use Yoast breadcrumbs (otherwise the theme shows its own).
6. Test email: send yourself a quote request. If it does not arrive, install an SMTP plugin (e.g. WP Mail SMTP);
   shared hosting often blocks PHP mail. Every request is also saved under Enquiries in the admin.

## Enquiries
- Admin menu "Enquiries": quote requests, custom orders, contact messages and newsletter sign-ups.
- Uploaded drawings are stored in wp-content/uploads/tata-enquiries/ with random names, blocked from public access.
  Download them from the enquiry screen ("Download attached file").
  On Nginx hosting, ask your host to deny access to /wp-content/uploads/tata-enquiries/.
- Spam protection: hidden honeypot field, minimum fill time, and 6 submissions per 10 minutes per visitor.

## Shop pages
- Toolbar: list view, or 2, 3 or 4 products per row; "Show" 12, 24, 36 or 48 per page; sorting.
  Each visitor's choice is remembered. Links like /products/?per_page=36 also work.
  On tablets and phones, 3 and 4 per row become 2, and list view becomes compact.
- Sidebar: every category opens to show its subcategories; the current one is open automatically.
  On phones a "Categories" button opens the list as a slide-in panel.
- Change the per-page options with the filter tata_per_page_options (default 12, 24, 36, 48).

## Languages
- The globe menu in the top bar translates the whole site with Google Translate:
  English, Deutsch, Français, Español, Italiano, Português, Русский, Türkçe, العربية, 中文.
  Change the list in Customize > Tata Surgical > Footer, search and languages (codes such as en, de, fr, nl, pl, ja, ko).
- The choice is remembered while the visitor browses. Google's script loads only after a visitor
  picks a language, so English pages stay fast and load nothing from Google.
- Item codes, the logo, emails, phone numbers and certificate names are never translated.
- Automatic translation is not indexed by search engines. For translated pages that rank in Google,
  install WPML or Polylang later: the theme then shows their languages in the same menu automatically.

## Next exhibition
- The hall and booth in the top bar, and "Our exhibitor profile" in the footer card, open your
  MEDICA profile. Change the link in Customize > Tata Surgical > Next exhibition > Exhibitor profile link.

## Animations
- Menus drop in with their items rising in sequence; a link underline grows on hover.
- Search window and quote list ease in; "Add to quote" sends a small dot flying to the quote icon.
- Footer and enquiry page content fades up as it scrolls into view.
- The WhatsApp button pulses gently three times after the page loads.
- Everything is switched off for visitors who turn on "reduce motion" in their device settings,
  and content is never left hidden if JavaScript fails.

## Elementor
- Any page edited with Elementor shows full width without the theme title.
- The homepage shows your Elementor page full width.
- Elementor Pro Theme Builder can replace header, footer, single and archive templates.
- On activation the theme sets Elementor to use the theme fonts and colours, and a 1280 px content width.

## Files
- inc/            setup, assets, customizer, menus, enquiries, live search (REST), SEO, Elementor, WooCommerce
- template-parts/ header (top bar, search, quote drawer), footer (custom-order band, footer, WhatsApp), enquiry page layout
- page-templates/  request-quote.php, custom-order.php, contact.php, full-width.php
- woocommerce/    archive-product.php, content-product.php
- assets/         css/main.css, css/woocommerce.css, js/main.js, img/ (logos, WeChat QR)
