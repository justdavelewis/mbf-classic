# STATUS — mbf-blank (classic starter)

> Read this first, every session. Newest entry first. Dated headings. Quote decisions.

**Owners:** theme — NAME · content/scripts — NAME · live writes announced in #channel
**Environments:** local `https://mbf-classic.local` only (a starter never deploys)
**Repo:** `wp-content/` → TODO: create `github.com/andonette-mbf/mbf-blank` (private) · no CI

## Outstanding

### DECISION FIRST (don't build on these)
- [ ] Q2 Header CTA button and footer menu: every-site, or per-fork? Left out of the starter.
- [ ] Q3 Tailwind's default theme (`--color-red-500`…) is still importable as utilities.
      Reset the `--color-*` namespace in the starter, or leave it?
### Resolved 2026-11-05 (David: *"Go with your recommendations on D1–D8"*)
- D1 `inc/`: `setup enqueue builder helpers options forms` only. No `woocommerce`,
  `post-types`, `admin-help`, `schema`. (`schema` added 2026-11-06, Q9.)
- D2 Header: single row; dropdowns `<details>`; no mega menu.
- D3 Breakpoint `64rem`.
- D4 Editor presets: fontSizes sm/body/lead/h4–h1; spacing fluid s/m/l/xl. Display stays
  in tokens.css only.
- D5 Components in `src/components/` until `mbf-css` exists.
- D6 Phase 4 provisions a placeholder WS Form by script; ID in Site Settings.
- D7 Gold-standard review recorded here only. Standards README untouched (outside repo).
- D8 Out: `photo` ground, `.mbf-actionbar`, `.mbf-subnav`, `comments.php`, `.mbf-dialog`.
- Q5 Accent control **kept** as the seam forks plug their measured pairs into, although
  it has no visible effect with the placeholder palette (David, 2026-11-05: *"Keep the
  accent field"*).
- Link fields: fixed in the standards (`mbf-site-standards` docs/05 + CHANGELOG, not
  committed). Buttons use ACF `link`, card links `page_link`; `page_link` has no
  `allow_custom` (David, 2026-11-05: *"correct the bug here and do it in the source
  standards folder"*).

- Q4 Standards fixed in `mbf-site-standards` (not committed), and mirrored here
  (David, 2026-11-05: *"fix those in the standards too"*): docs/12 bridge reads `--h-2` and
  bridges `--wrap`/`--measure`; docs/02 base drops `var(--header-h, 0)` (sticky headers set
  their own) and gains the plain-link rule; `templates/gitignore` gains `!**/src/vendor/`
  (tested in a scratch repo: keeps `src/vendor/`, still drops Composer `vendor/`). Each in
  the standards CHANGELOG.

- Q6 docs/03 card fixes made in the standards too (docs/03 + CHANGELOG, not committed)
  (David, 2026-11-05: *"YES"*).
- Q7 Icons: Font Awesome Pro, the CSS build, ported from Mantech (David, 2026-11-05: *"Use
  font awesome, the snippet should be available in mantech"*). Kit token in Site Settings ›
  Icons (`fontawesome_token`); blank = not loaded, socials fall back to visible names.
  Socials use the Brands family. Mantech's token was not copied (domain-bound, that site's
  setting).

- Q8 Fixed in the standards (docs/02 + docs/14 `main.css` → `source(none)`, docs/02 rule,
  CHANGELOG 2026-11-06; not committed) (David, 2026-11-06: *"fix Q8 in the standards"*).
- Q9 `inc/schema.php` added, **amending D1** (David, 2026-11-06: *"add schema.php"*).

- Q1 Editor checks done 2026-11-06 in Chrome (David logged in).

- Q10 Fixed 2026-11-06 with an editor-only build (`src/editor.css` → `dist/editor.css`) and
  `contentSize` in rem; both also fixed in the standards (David: *"yes do the editor
  stylesheet then tag v1.0"*).

### Tasks
- [ ] Switch Local to PHP 8.4 (theme CLAUDE.md TODO; Local is on 8.2.29)
- [ ] Enter ACF Pro + WS Form Pro licences (David)
- [ ] **Before any fork goes live (down the line):** no agent/Claude files on the server
      (David, 2026-11-06). The standards' `templates/deploy.yml` already rsyncs only the theme
      folder (so `docs/` never ships) and excludes `CLAUDE.md`, `.claude/` and `src/`
      (`contract.md`). **Gap:** `theme.json.md` sits in the theme root and isn't excluded, so it
      would be publicly readable. Add an exclude (or move the file) when the deploy is set up,
      and in the standards template too.
- [ ] Enter a Font Awesome kit token in Site Settings › Icons (David), then verify the
      social glyphs render (Brands family) and the stylesheet loads with 0 console errors

## Live write log

| Date | Who | Command (`wp @live …`) | Backup file | Result / verified |
|---|---|---|---|---|

---

## 2026-11-06 — Editor stylesheet (Q10) + v1.0

**Changed:** `src/editor.css` → `dist/editor.css`, loaded by `add_editor_style()` instead of
`dist/style.css`: Tailwind theme + utilities but **no Preflight**, Typography applied to the
editor root (`className: is-root-container`), Preflight's `border: 0 solid` kept for prose
(a quote's rule had width but `border-style: none`), root `max-width: none` so WordPress's
layout sets the width. `vendor/typography.css` maps `:is(.prose, .is-root-container)`: one
recipe for both. `package.json` builds both files. `theme.json` `contentSize` `68ch` →
**`45.5rem`** (`ch` resolved per element: the 34px post title went 1516px wide and pinned
left; 45.5rem = the same 728px for every element). Version 1.0.0 (`style.css`,
`package.json`). Standards: docs/01, 02, 05, 09, 10, 12, 16, `templates/deploy.yml`, CHANGELOG.
**Verified:** editor (Chrome): title, p, h2, ul all at left 348 / width 728; h2 44px/700 in the
heading colour; bullets; links underlined in primary; quote 4px solid; tokens resolve
(`--measure` 45.5rem); 0 console errors; `dist/style.css` no longer in the iframe. Front end
(Playwright), 6 URLs × 6 widths: prose still 728px (and narrower on small screens), quote 4px,
0 overflow, 0 contrast failures, 1 h1, 0 notices, 0 console errors. `verify-tokens.sh`: none
undefined. A brace slip while moving a block broke the editor build once; caught because only
one of the two builds reported "Done", rewritten and re-verified.
**Tagged:** `v1.0` (see the release notes in the tag).

---

## 2026-11-06 — Editor + ACF admin checks (Q1)

**Changed:** nothing in code.
**Verified (Claude in Chrome, logged in as the local admin):**
- Token bridge **in the editor iframe**: presets and layout sizes print on the iframe's
  `:root`; our tokens resolve there (`--color-primary` #2d5ba8, `--fs-h2` and
  `--space-fluid-m` keep their `cqi` clamps, `--wrap` 80rem, `--font-sans`); paragraph text
  gets `--color-text`. docs/12's three checks now pass in both places.
- Editor presets: colours = our 6 slots only, custom colour off; font sizes = sm/body/lead/
  h4–h1 (D4), custom off; spacing = fluid s/m/l/xl. WordPress keeps its default palette
  (12), gradients (12) and duotones (8) registered under the `default` origin, but
  `defaultPalette/Gradients/Duotone: false`, so the editor doesn't offer them.
- ACF admin: the Page layouts box on About (empty) and on /_sample/ (44 rows, in order,
  Content + Section tabs, controls holding `ground/accent/spacing`); Site Settings with
  Identity/Contact/Social/Icons/Forms tabs and the placeholder values. 0 console errors on
  both after a reload.
**Found:** editor content unstyled (Q10).

---

## 2026-11-06 — Standards brought level with mbf-blank

**Changed (mbf-site-standards, uncommitted, on `main`):** every fix and lesson from phases
0–5 is now in the source standards, not only in this theme (David: *"Make sure its fixed at
the source too so i can push the site build rules company wide"*): docs/05 (schema.php
pattern; WS Form allow-list gate, template provisioning, `--wsf-*` styling seam, outline +
44px fixes; Font Awesome CSS build + token + Brands; verified `get_row()` shape, composite
clone key, inactive clone group), docs/01 + 03 (section family + `-on-accent/-link/-error`,
card re-scope, `__inner` `minmax(0, 1fr)`, drawer forced-colours edge), docs/08 (prove
notices visible; screenshot limit), docs/10 (7 symptom rows), docs/12 (contract location),
README (gold-standard review detail), CHANGELOG 2026-11-06. Earlier the same day/yesterday:
docs/02 (`source(none)`, base), 03 (card), 05 (link fields), 12 (bridge), 14, gitignore.
**Verified:** grep for the superseded guidance (`allow_custom`, `--h2` preset,
`var(--header-h`, plain `@import "tailwindcss";`, the classic `VERIFY on first build`)
finds nothing outside CHANGELOG, except docs/02's explanation of the removal and docs/16's
block-field VERIFY (block track, not exercised here, correctly still open).
**Not done:** committing/pushing the standards (needs David's go, and a branch: it's `main`).

---

## 2026-11-06 — Q8 standards fix + `inc/schema.php` (Q9)

**Changed:** standards (not committed): docs/02 + docs/14 `main.css` snippets →
`@import "tailwindcss" source(none)`, with the reason in docs/02's rules; CHANGELOG
2026-11-06. Theme: `inc/schema.php` (loaded from `functions.php`): one `Organization` node in
`<head>` from Site Settings (name, url, logo, telephone, email, address as one line, socials
as `sameAs`; empty values omitted; `@id` `/#organization`). Off when Yoast, Rank Math,
SEOPress or AIOSEO is active; `mbf_print_business_schema` filter overrides per page.
`JSON_HEX_TAG` so a value can't close the script tag.
**Why:** David: *"fix Q8 in the standards and add schema.php"*. docs/05: exactly one
business node per page, from Site Settings, gated where an SEO plugin outputs one.
**Verified:** WP-CLI: default prints one node with no empty keys; a company name containing
`</script>` stays escaped and parses; with a stub `RankMath` class it prints nothing; the
filter forces it back on. Playwright, 14 URLs (incl. 404, search, archive, /_sample/):
exactly 1 JSON-LD block, parses, 1 business node, in `<head>`, name + phone match the
footer, 0 notices, 0 console errors.
**Deliberately not done:** `LocalBusiness` subtype, opening hours, geo (per fork: the
starter has no premises data); BreadcrumbList / FAQPage / Article (per fork).
**Noticed, not changed:** WordPress's `home` URL here is `http://mbf-classic.local` while
Local serves https, so `@id`/`url` are http. That's this Local site's options, not the theme.

---

## 2026-11-05 — Phase 5: verification sweep + fork test

**Changed:** `src/main.css` → `@import "tailwindcss" source(none)` (Q8; found by the fork
test). Nothing else: this phase is verification.
**Verified (Playwright, Chromium; Local `https://mbf-classic.local`):**
- **URL statuses** (15: home, page 2, /_sample/, pages, child pages, posts, category,
  search hit/miss, 404): all as expected; `?p=1` → 301 to its permalink; canonicals 200
  on-host. 0 PHP notices (notices proved visible in Phase 1). 0 console errors (only the
  404 page's own status), 0 failed requests. Landmarks 1/1/1 and one h1 on every URL. No
  adjacent sections share a ground. No leaks (staging host, test emails). No upscaled
  images. **0 business nodes (Q9).**
- **Widths** 1440/1280/1024/1023/768/390/320 × 12 URLs (84 checks): 0 horizontal overflow,
  header 1 row, every `__inner` edge = the header edge (above and below `--wrap`), nav ↔
  drawer exactly at 1024/1023, 0 margins between section children (all `gap`).
- **Keyboard** (/_sample/, 76 stops per pass): first stop the skip link, every stop has a
  ≥2px ring (cards on the card), 0 backwards jumps, 0 off-screen stops. Drawer, dropdown,
  FAQ and form keyboard behaviour verified in phases 1/3/4.
- **Forced colours:** same 76 stops all ringed; borders/outlines kept (phases 1/3/4).
- **Reduced motion:** 791 elements, 0 transitions > 1ms, 0 animations, scroll-behaviour
  auto; card lift none; drawer slide none. (The skip link reaches its focused position one
  frame late under `reduce`: docs/02's base rule gives every property a .01ms transition.
  Harmless; measured visible at 12px.)
- **200% zoom** (1280 → 640 CSS px): 5 templates, 0 overflow, drawer takes over; h1 69.9 →
  96.6 device px, body ×2. **Text spacing (1.4.12)** at 1280/390: 0 clipping, 0 fixed
  heights on text, 10/10 cards keep their text.
- **Contrast:** every text element on its real background ≥ AA, on 6 URLs × 5 widths
  (/_sample/ alone: 265 elements on all 4 grounds).
- **Tokens:** `verify-tokens.sh` → consumed-but-undefined: none. No colour literal in
  code outside `tokens.css`/`theme.json` (one in a comment).
- **Fork test:** changed ONLY `theme.json` (palette → green/aubergine/gold, font → Georgia)
  and `tokens.css` (radii → 0, error colour). On /_sample/, /about/, home and 404: **0
  computed colours left from the old palette** (header, footer, cards, buttons, WS Form
  included), all 6 new slots in use, serif on body and headings, card/button radius 0, WS
  Form fields followed the new base. Screenshot: cards on the new secondary. Restored both
  files byte-identical (`shasum`).
  The restore's `dist/` checksum didn't match the pre-test one: Tailwind's automatic
  source detection had scanned Markdown/JSON, so Phase 4's docs edits had changed the CSS.
  Fixed with `source(none)`; proved by adding words to `contract.md` (dist unchanged) and
  removing them (rebuilt identical). Re-ran contrast/overflow/h1/sr-only/prose on 6 URLs ×
  5 widths after the change: clean.
**NOT verified:** the editor half of the token bridge and the ACF admin screens (Q1: Chrome
still at the login screen); Font Awesome glyphs (no kit token); a real form submission end
to end; VoiceOver itself (the accessibility tree and names were checked, not a screen
reader); browsers other than Chromium.

### v1.0 done when (docs/12)
- [x] The build passes and `dist/style.css` (43.7KB) and `dist/editor.css` (21.7KB) aren't empty.
- [x] `/_sample/` renders every layout on every ground, with 0 console errors and 0 notices.
- [x] The full `08` sweep passes at all widths, plus keyboard, forced colours, reduced
      motion and 200% zoom. (Business node: 0 at first; `schema.php` added 2026-11-06 → 1
      on every URL.)
- [x] The token bridge is verified in the editor and on the front end (editor 2026-11-06,
      after David logged Chrome in). See Q10: the bridge works, but editor content is unstyled.
- [x] Fork test: change only `theme.json` and `tokens.css`, and the site fully re-skins.
- [x] `CLAUDE.md` (rules) and `docs/STATUS.md` (history) exist.

---

## 2026-11-05 — Phase 4: /_sample/

**Changed:** `scripts/build-sample-page.php` (dry/force, idempotent): draws 4 neutral
placeholder PNGs with GD (no binaries in the repo; matched by `_mbf_sample_asset` meta),
creates the placeholder WS Form **"MBF placeholder enquiry"** from WS Form's own Contact
Us template (Mantech's `db_create` → `db_update_from_object` → `db_publish` path) and sets
Site Settings › Forms to its ID (D6), and builds `/_sample/`: 11 layouts × 4 grounds = 44
sections, grounds cycling so no neighbours match, card counts 3/2/4/1 per ground (auto-fill,
pairs, pairs + unlinked, lone), split image side alternating, CTA bands with no second
button (phone fallback). Rows match by a hash in `_mbf_sample_hash`. ·
`src/vendor/wsform.css` (WS Form's `--wsf-*` variables → our families; outline focus and
44px targets) · `--section-error` added to the section family · contract + theme.json.md.
**Why:** brief phase 4; docs/08 (`/_sample/` during the build, deleted at launch).
**Fixed on the way (found by the sweep):** WS Form labels 1.24:1 on secondary and 3.17:1
on primary, required mark 1.02:1 on primary → variables; WS Form's `outline: 0` left
fields with **no visible focus** → unlayered 0-5-0 outline rule (seam #3, its selector is
0-4-1); fields/submit 43px → `min-block-size: var(--button-h)`.
**Verified:**
- Script: dry → force → force again no-op, and a final dry run lists nothing; exactly 1
  form (published), 4 attachments, 1 page.
- Action gate in this env (`local`): database/message/redirect run; email/webhook/hubspot
  don't.
- `/_sample/` × 7 widths (1440/1280/1024/1023/768/390/320): 44 sections, 0 adjacent same
  ground, 1 h1, 0 duplicate ids (4 form instances), 0 overflow, 0 notices, 265 text
  elements all ≥ AA, 0 console errors, 0 failed requests. Only small target: the GDPR
  checkbox ≈24px, which its clickable `<label>` enlarges.
- Forms on base/secondary/primary: outline on fields + submit in the ground's ring; 48px
  tall; empty submit shows "This field is required." in `--section-error`, linked by
  `aria-describedby`.
- Screenshots: 1280 + 390 full page (the 1280 capture repeats past Chromium's 16,384px
  limit; DOM confirmed 1 header, 44 sections), form on secondary, logos on tertiary.
**NOT verified:** a real submission end to end (would write a local submission; the gate
logic is verified directly); Font Awesome glyphs (no token yet); editor (Q1).
**Deliberately not done:** styling for WS Form field types the template doesn't use
(select, radio, file); per-row form choice (one site-wide enquiry form).

---

## 2026-11-05 — Phase 3: partials + 11 layouts

**Changed:** `template-parts/` card, section-heading, cta-buttons, page-hero, crumbs,
social-links, enquiry-form · `template-parts/blocks/` hero, text, split, cards, cta-band,
faq, stats, quote, steps, logos, form ($args only; each returns nothing without its
required content) · `src/contract.md` (one entry per partial and layout) · 16 component
CSS files + `.mbf-button--secondary`, `.mbf-buttons`, `.mbf-grid--pairs` · `inc/forms.php`
(WS Form: outside production only `database`/`message`/`redirect` actions run;
allow-list) · helpers `mbf_link()`, `mbf_grid_class()`, `mbf_crumbs()`;
`mbf_section_attrs()` takes the layout's own classes; builder passes `position` (a hero
owns the h1 only as row 1) · image size `card` 800×500 · page/single/index/search/404
use page-hero (+ crumbs); index/search list posts as h2 cards · footer socials.
**Why:** brief phase 3; docs/03 catalogue + linked-card rules; docs/05 partials.
**Standards fixes first** (David: *"fix those in the standards too"*): see Q4 in Resolved.
**Verified** on a throwaway page (all 11 layouts across all 4 grounds, two hero rows,
temporary images + socials; all deleted after, verify URL now 404):
- Outline: 1 h1 (first hero), second hero → h2; every `aria-labelledby` resolves; 0
  duplicate ids. Crumbs right on page/child page/post/search/404, none on the front page.
- Cards: one link per linked card, link name = title, CTA aria-hidden, image `alt=""` +
  aria-hidden; unlinked card has no link, no lift. Keyboard: one tab stop per linked card,
  ring on the card in the section's ring colour (white on secondary), link ring removed;
  clicking card text follows the link; subgrid rows align at 1440/1280/768 **after the
  fix** (Q6a); blog index = 10 single-link h2 cards.
- Contrast: 64 text elements on their real backgrounds, 0 below AA (incl. white cards on
  the dark ground, after Q6b). Primary-ground button hover 4.6:1 (tight; re-measure on a
  palette change).
- CTA band with no second button → "Call 01234 567890" (`tel:`). Split image-start: image
  left visually, DOM text first. FAQ opens/closes with Enter. Steps numbers are `::before`
  with alt "". Socials 48px targets.
- 9 URLs × 7 widths: 0 overflow, 1 h1, 0 notices, 0 console errors, 0 failed requests.
- Reduced motion: card lift was still 2px (**fixed**, Q6c), now none. Forced colours: card
  outline + border, step numbers, quote rule all kept. Screenshots 1280/390 full page.
**NOT verified:** WS Form rendering (no form until phase 4's script); editor + ACF admin
screens (Q1).
**Deliberately not done:** card modifiers `--feature/--compact/--flush-media` (no
consumer); FAQ `::details-content` animation (13 tier 2, optional); `FAQPage` /
`BreadcrumbList` schema (`schema.php` out per D1); WS Form variable styling
(`src/vendor/wsform.css`) until a real form renders in phase 4; social icons (Q7).

---

## 2026-11-05 — Phase 2: ACF (layouts, section controls, Site Settings)

**Changed:** `acf-json/` (readable keys, filename = key): `group_mbf_section_controls`
(ground/accent/spacing; inactive, clone-only), `group_mbf_page_layouts` (one
`page_layouts` Flexible Content, the 11 docs/12 layouts, each = Content tab + Section tab
with the seamless clone), `group_mbf_site_settings` (company name, logo, **white logo**,
phone, email, address, legal line, socials repeater, enquiry form ID),
`ui_options_page_mbf_site_settings`. · `inc/options.php` (`mbf_setting()`,
`mbf_company_name()`, `mbf_logo( $ground )`, `mbf_tel_href()`) · `inc/builder.php`
(`mbf_render_layouts()`, `mbf_row_args()`, `mbf_layout_names()`) · `inc/helpers.php`:
`MBF_GROUNDS`, `mbf_is_dark_ground()` (the one list), `mbf_ground_accent()`,
`mbf_accent_colour()` (the contrast guard, measured matrix in its comment),
`mbf_section_attrs()` (BEM modifiers only), heading-id registry, `mbf_rows()` ·
`section.css` accent + spacing modifiers · `footer.css` · header/drawer/footer read Site
Settings (logo by ground) · `page.php`: title unless the first section is a hero →
layouts → `the_content()` only when there are no sections · `index.php` renders the posts
page's sections after the list · placeholder script also fills empty Site Settings (keys
read from acf-json).
**Why:** brief phase 2; docs/05 + docs/12 §Builder, §FSE-ready (rows hold content only;
field names = future block attributes).
**Verified:**
- ACF loads all four from local JSON; the clone expands into every layout although its
  group is inactive; options page registers (`edit_posts`). Locations: page →
  page_layouts only; post → none; options page → site_settings only.
- `get_row( true )` (docs/05 "VERIFY on first build"): keyed by **name**, clone flattened
  to top-level `ground/accent/spacing`. Throwaway probe page, deleted after. The clone's
  `_page_layouts_N_ground` reference key is composite
  (`field_mbf_cards_controls_field_mbf_section_ground`); `update_field()` by name handles it.
- Guard: `accent: primary` on secondary → re-mapped, no modifier emitted; heading ids dedupe
  (`our-services`, `our-services-2`). Accent × ground matrix measured in Chromium.
- Missing template → `<!-- mbf: no template for "…" -->` for editors only (anonymous: empty).
- ACF deactivated: 3 URLs 200/404, 0 notices, footer falls back to the site title.
  Reactivated.
- Logo by ground with two temporary PNGs: header = main logo; drawer + footer = white
  logo; 44px tall; alt = company name. Test logos unset and attachments deleted after.
- Placeholder script: dry → force → second run no-op.
- Sweep 7 URLs × 7 widths (incl. 1024/1023): 0 overflow, 1 h1, 0 notices, footer NAP
  present, footer edges = header edges. 0 console errors, 0 failed requests. Footer
  contrast on secondary: links/address 16.9:1, legal (muted) 10.59:1. Screenshots
  1280/320 footer, 390 logos.
**NOT verified:** ACF admin screens rendered, and the editor token check (Q1).
**Deliberately not done:** layout partials (phase 3, so every layout currently prints the
admin-only comment); socials output (social-links partial, phase 3); `inc/forms.php`
(phase 3, with the form layout); `mbf_linked_text()`, `mbf_kses_svg()`,
`mbf_prose_classes()` (no consumer yet; prose already follows the ground through
`--tw-prose-*` → section vars, so `mbf_prose_classes()` may never be needed).

---

## 2026-11-05 — Phase 1: skeleton

**Changed:** `style.css` (header only) · `theme.json` v3 (placeholder palette, core presets
and custom values off, `fluid: false`) · `theme.json.md` (measured ratios) ·
`functions.php` → `inc/setup.php`, `inc/helpers.php` (menu tree + one renderer for nav and
drawer), `inc/enqueue.php` (mtime versions, `defer`, emoji off, block-library CSS off on
singular content without blocks) · `package.json` (Tailwind 4.3.4 CLI + Typography
0.5.20) · `src/main.css`, `base.css`, `wordpress.css`, `vendor/{typography,dialog}.css`,
`components/{type,section,stack,grid,button,chevron,skip,site-header,nav,drawer,pagination,search}.css`
· `src/tokens.css` bridged (19 lines → `var(--wp--preset--*)` / `--wp--style--global--*`) ·
templates `header footer index page single 404 search` + `searchform.php` ·
`assets/js/{nav,drawer}.js` · `scripts/build-placeholder-content.php` (dry/force; 5 pages,
12 posts, Primary menu) · theme CLAUDE.md (breakpoint, plugins, bridge) · theme activated.
**Why:** docs/12 skeleton; D1–D8 as resolved above. Header/drawer ported from Mantech's
pattern (menu rendered twice from one source; `<details>` dropdowns; `<dialog>` drawer),
with `commandfor`/`closedby` doing the open/close and JS only as fallback.
**Verified (Playwright, Chromium):**
- Token bridge, front end: presets **and** layout sizes print on `:root`; every bridged
  token resolves (h1 54.7px at 1440 = the cqi clamp); `cqi` clamps pass through unchanged;
  `static` kept: all 42 seed lines in `dist/`. Found: WP kebab-cases `h2` → `h-2` (Q4).
- Widths 1440/1280/1024/1023 (switch)/768/390/320 × 10 URLs: 0 horizontal overflow, 1
  `<h1>` each, header 1 row, edges aligned (header = main = footer), nav ↔ toggle swap
  exactly at 1024/1023. **Fixed on the way:** 11px overflow at 320 from `auto` grid tracks
  growing to a search field's intrinsic width → `minmax(0, 1fr)` on `__inner`, `.mbf-stack`,
  `.mbf-search`. Dropdown ran 12px from the viewport edge → end-aligned. Drawer left a
  6px backdrop sliver at 390 → a `--button-h` tap-to-close strip.
- 0 console errors (only the 404 page's own document status), 0 failed requests.
  0 PHP notices on 10 URLs, **proved** by a throwaway probe that a notice does appear in
  the HTML (probe deleted).
- Keyboard: skip link first + visible + moves focus to main; tab order brand → nav;
  `aria-current` on About; dropdown opens on Enter, Esc closes + focus to summary, closes on
  outside click and when focus tabs past. Drawer: modal, scroll lock, Esc/close
  button/backdrop all close, focus returns to toggle, closes when widened past 64rem.
- Contrast on 4 grounds × 7 roles: all ≥ AA (lowest muted on tertiary 5.36:1; border on
  tertiary 3.06:1). Table in `theme.json.md`.
- Reduced motion: drawer translate none, transitions ~0. Forced colours: focus outline,
  button/field borders, hamburger, chevron kept; **fixed** missing drawer edge
  (transparent border). 200% zoom: h1 56.5 → 83.4 device px, body ×2, no overflow.
- Screenshots: 1280 dropdown, 390 drawer, 320 404, 768 blog, forced-colours drawer.
**NOT verified:** the editor half of the token bridge (Q1). Generating a login cookie via
WP-CLI was blocked by the permission classifier, so no wp-admin session. JS fallbacks
for `commandfor`/`closedby` not exercised (this Chromium supports both).
**Deliberately not done:** builder, Site Settings, logo, footer NAP (phase 2); card +
count-aware grid (phase 3); header CTA, footer menu (Q2); `.mbf-stack--loose` (no
consumer yet). Local-only `mu-plugins/mbf-local-debug.php` shows notices; gitignored,
not part of the theme. Base adds one rule beyond the docs/02 list (plain-link underline, Q4).

---

## 2026-11-05 — Phase 0: gold-standard check + plan

**Changed:** No theme code. This STATUS entry + the DECISION FIRST list above.
**Why:** docs/12 requires a gold-standard check before building `mbf-blank`.
**Checked** (GitHub API, 2026-11-05):

| Starter | State | Fit |
|---|---|---|
| Underscores `Automattic/_s` | **archived**, last push 2025-09-05 | Dead |
| Twenty Twenty-One (last classic default) | repo archived 2020; ships with core | A theme, not a starter |
| `_tw` (gregsullivan) | active, last push 2026-04-04, Tailwind 4 since 2025-10 | Closest. `_s` + Tailwind utilities in markup, plain `@theme` + full TW default theme, PostCSS, no ACF, no `$args` partials, no strict_types/defer |
| Sage (roots) | active, 2026-10-07 | Blade + Acorn + Vite: not plain PHP `$args` partials |
| WP Rig | active, 2026-10-03 | Large framework (~470 files: components, customizer, PWA, Jetpack, AI tooling). Not lean |

**Recommendation:** none fits the stack. Keep `mbf-blank` as our own code (docs/12 step 3).
**Taken:** (1) `_tw` bridges `theme.json` palette → `@theme` via
`var(--wp--preset--color--*)`, the same pattern as our token bridge; it confirms the
approach, and we keep `static`. (2) `_tw` builds a separate editor CSS with Preflight
scoped, so check `add_editor_style( 'dist/style.css' )` doesn't break editor UI in phase 1.
(3) WP Rig's WP 7.1 note: the post editor is fully iframed in 7.1, so editor JS must not
query the top-level `document`, and the token-bridge editor check must look inside the
iframe.
**Verified:** repo states via api.github.com; `_tw` source read (tailwind.css,
tailwind-theme.css, functions.php, theme.json). Block-track starters not checked: that's
`mbf-block` (16).
**Deliberately not done:** any build, pending go-ahead and D1–D8.

---

## Conventions worth not relearning (project-specific)
- WP-CLI here needs Local's socket: `wp --exec="ini_set('mysqli.default_socket','<Local run dir>/mysql/mysqld.sock');" …`
  (Homebrew WP-CLI 2.12 on PHP 8.5 prints deprecations; filter them, they aren't ours).
- `mu-plugins/mbf-local-debug.php` (gitignored) is what makes PHP notices visible locally.
- Tailwind builds from the `@source` paths only (`source(none)`): adding a doc never changes `dist/`.
- Full-page screenshots over 16,384px repeat (Chromium limit): check the DOM, screenshot by element.

## Settings that do NOT travel (re-set per environment)
- `WP_ENVIRONMENT_TYPE` (dangerous when UNSET → production → leads fire)
- `blog_public` (dangerous when CARRIED OVER → live noindexed)
- Licences: ACF Pro, form plugin, SEO plugin — domain-bound
- …
