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
- PHP: 8.4.x (docs/09). Local serves 8.4.27 (`X-Powered-By`, 2026-11-06).
- WordPress: 7.1.4 (2026-11-05)
- Plugins: ACF Pro 6.8.10 · WS Form Pro 1.12.16 (both active, licensed) · Woo:
  no (opt-in per fork, docs/06)
- `mbf-css` version: not yet. `src/tokens.css` is the seed (docs/14 moves it out later)
- DB table prefix: `wp_`

## Brand tokens and contrast

None, deliberately. A starter holds **placeholder** slot values only (docs/12 §Deliberately
not included). Values live in `theme.json`; measured ratios per ground in `theme.json.md`.
Each fork replaces both and re-measures.

## Breakpoint

Nav ↔ drawer at `64rem` (1024px), stated once in `src/components/site-header.css`; JS copy
in `assets/js/drawer.js` (closes an open drawer when widening past it).

## Token bridge

`src/tokens.css` reads slots, type, fluid space, `--wrap` and `--measure` from `theme.json`.
Font-size slugs are kebab-cased by WordPress: slug `h2` → `--wp--preset--font-size--h-2`.

## ACF

- Site Settings options page slug: `mbf-site-settings`. Read it only through `mbf_setting()`.
- Dark grounds: `mbf_is_dark_ground()` is the one list. The accent guard
  (`mbf_accent_colour()`) holds the measured pairs; re-measure and edit it on any palette
  change.
- Style Settings (`mbf-style-settings`, admins only): saved choices override `theme.json`
  through `wp_theme_json_data_theme`; defaults stay in `theme.json` `settings.custom` + palette.
  Selects default to `theme` (override nothing). A new colour pair in the CSS must be added to
  `mbf_contrast_pairs()`, or the save check won't cover it.
- Schema: `inc/schema.php` prints the ONE business node from Site Settings, and goes
  silent when an SEO plugin is active (`mbf_print_business_schema` filter to override).
- Scripts take field keys from `acf-json/`. The section-controls clone's reference key is
  composite (`field_mbf_{layout}_controls_field_mbf_section_ground`); write by field name
  with `update_field()`, or read that composite from the JSON.

## Site-specific rules

- **Everything here propagates to every fork.** Add only what every classic site needs.
  When in doubt, leave it out and note it in STATUS.
- Build to docs/12 and its "v1.0 done when" checklist. The starter layouts are exactly the
  docs/12 set unless a decision in STATUS changes it.
- Placeholder content and the `/_sample/` page are built by idempotent `scripts/`, never by
  hand in wp-admin, so a fork can rebuild them.
- Nothing client-specific, and no copy of `mbf-site-standards` (it's gitignored).

## ⚠️ Deliberate divergences: do not "fix"

- **Git is rooted at `wp-content/`, not `app/public/`** (2026-11-05, David: *"make the repo
  in the wp-content folder like the skyway site is for consistency"*). So `docs/`,
  `scripts/` and `.vscode/` live in `wp-content/`, and the `.gitignore` paths have no
  `/wp-content/` prefix. A fork that deploys adds `wp-cli.yml` here too, and its
  `deploy.yml` rsyncs from this root. TODO: agree with Andonette whether docs/09 should
  change to match.
