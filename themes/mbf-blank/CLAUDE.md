# CLAUDE.md — mbf-blank (classic starter)

@~/Documents/GitHub/mbf-site-standards/CLAUDE.md

If the Made by Factory standards (§0–9) aren't in your context, the import above didn't
load. Stop and say so before doing any work.

The Made by Factory standards (`mbf-site-standards/CLAUDE.md` and its `docs/`) apply in
full. This file holds **only rules specific to this site**. Dated history goes in
`docs/STATUS.md`, not here. Keep this file short: if a section needs a story, the story
belongs in STATUS and the rule belongs here.

**Read `docs/STATUS.md` before starting work.**

## Facts

- What this is: **`mbf-blank`, the classic starter** that every classic client site forks
  (spec: docs/12). Not a client site. Built in the Local site `mbf-classic`.
- Track: **classic** (why: this is the classic-track starter; the block starter is
  `mbf-block`, docs/16)
- Theme slug / PHP prefix: `mbf-blank` / `mbf_` · text domain `mbf-blank`. A fork renames
  all three.
- Environments: local `https://mbf-classic.local` only. No staging or live: a starter
  never deploys, so no `deploy.yml` and no `@staging`/`@live` aliases.
- Repo: rooted at `wp-content/` (docs/09). A fork that deploys adds `wp-cli.yml`
  (`path: ..`) and `.github/workflows/deploy.yml` here, and its rsync sends
  `themes/<slug>/` only, with the template's excludes.
- MCP (docs/17): `mu-plugins/mbf-mcp.php`, local and read only, three abilities, started by
  `scripts/mcp-serve.sh`. Register it per machine under the site's name.
- PHP: 8.4.x (docs/09). Local serves 8.4.27 (`X-Powered-By`, 2026-11-06).
- WordPress: 7.1.4 (2026-11-05)
- Plugins: ACF Pro 6.8.10 · WS Form Pro 1.12.16 (both active, licensed) · Woo:
  no (opt-in per fork, docs/06)
- Fonts: Archivo (variable, latin) + IBM Plex Mono 500, self-hosted in `assets/fonts/` (OFL),
  declared in `theme.json` (WordPress writes the `@font-face`). `--font-mono` is for eyebrows,
  step numbers and codes, never body text. A fork replaces both files and the families.
- Icons: Font Awesome Pro, **Duotone Regular**, from `mbf_icon()` only (`inc/helpers.php`). A fork
  changes the style with the `mbf_icon_style` filter, and its kit must serve that style. The
  second layer's colour is `src/components/icon.css`.
- `mbf-css` version: not yet. `src/tokens.css` is the seed (docs/14 moves it out later)
- DB table prefix: `wp_`
- Render fingerprint (standards docs/08): pages in `docs/fingerprint.json` (repo root); captures
  go in `.fingerprints/` (ignored). Baseline: `.fingerprints/baseline-page-types.json`.

## Brand tokens and contrast

None, deliberately. A starter holds **placeholder** slot values only (docs/12 §Deliberately
not included). Values live in `theme.json`; measured ratios per ground in `theme.json.md`.
Each fork replaces both and re-measures.

## Breakpoint

Nav ↔ drawer at `75rem` (1200px), stated once in `src/components/site-header.css`; JS copy
in `assets/js/drawer.js` (closes an open drawer when widening past it).

## Token bridge

`src/tokens.css` reads slots, type, fluid space, `--wrap` and `--measure` from `theme.json`.
Font-size slugs are kebab-cased by WordPress: slug `h2` → `--wp--preset--font-size--h-2`.

## ACF

- Site Settings options page slug: `mbf-site-settings`. Read it only through `mbf_setting()`.
  Editors and administrators (`edit_others_pages`): client staff update contact details here.
  Authors and contributors can't: it holds site-wide config and the icon token.
- Header button: Site Settings › Header (`header_cta`). Menus: `primary` and `footer` (legal,
  top level only). Placeholder values come from `scripts/build-placeholder-content.php`.
- Dark grounds: `mbf_is_dark_ground()` is the one list. The accent guard
  (`mbf_accent_colour()`) holds the measured pairs; re-measure and edit it on any palette
  change.
- Style Settings (`mbf-style-settings`, admins only): saved choices override `theme.json`
  through `wp_theme_json_data_theme`; defaults stay in `theme.json` `settings.custom` + palette.
  Selects default to `theme` (override nothing). A new colour pair in the CSS must be added to
  `mbf_contrast_pairs()`, or the save check won't cover it.
- Schema: `inc/schema.php` prints the ONE business node from Site Settings, and goes
  silent when an SEO plugin is active (`mbf_print_business_schema` filter to override).
- Solution finder: Site Settings › Solution finder, read only by `mbf_finder_config()`
  (`inc/finder.php`). One set of answers per site. Keys travel as `?need=&site=`; form fields
  read them with `#query_var(need)`, and their option values must be the same keys.
- Hotspots: Site Settings › Hotspots (heading, lead, scenes), read only by
  `mbf_hotspots_config()` (`inc/hotspots.php`). The page section only picks scenes
  (`show_scenes`, empty = all) plus section controls. Spots point at finder answers.
- Enquiry form: `scripts/data/enquiry-form.json`, shared by every site. A fork changes only its
  own instances (product list, enquiry types). Fields in action text are `{{Label}}`, never IDs.
- Scripts take field keys from `acf-json/`. The section-controls clone's reference key is
  composite (`field_mbf_{layout}_controls_field_mbf_section_ground`); write by field name
  with `update_field()`, or read that composite from the JSON.

## Site-specific rules

- **Everything here propagates to every fork.** Add only what every classic site needs.
  When in doubt, leave it out and note it in STATUS.
- Build to docs/12 and its "v1.0 done when" checklist. The starter layouts are exactly the
  docs/12 set (17 since 2026-11-06) unless a decision in STATUS changes it.
- Placeholder content, the `/_sample/` page and the page-type baseline are built by idempotent
  `scripts/`, never by hand in wp-admin, so a fork can rebuild them. Run order:
  `build-placeholder-content.php`, `build-sample-page.php`, `build-page-types.php`.
- Nothing client-specific, and no copy of `mbf-site-standards` (it's gitignored).

## ⚠️ Deliberate divergences: do not "fix"

- None. (Git at `wp-content/` was listed here until 2026-11-11; it's now the standard,
  docs/09.)
