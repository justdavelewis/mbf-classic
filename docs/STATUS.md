# STATUS — mbf-blank (classic starter)

> Read this first, every session. Newest entry first. Dated headings. Quote decisions.

**Owners:** theme — NAME · content/scripts — NAME · live writes announced in #channel
**Environments:** local `https://mbf-classic.local` only (a starter never deploys)
**Repo:** `wp-content/` → TODO: create `github.com/andonette-mbf/mbf-blank` (private) · no CI

## Outstanding

### DECISION FIRST (don't build on these)
- [ ] Q1 Token-bridge check in the **editor** not yet run: needs a wp-admin session in
      Playwright (see Phase 1 entry). Also confirms core's default sizes are hidden there.
- [ ] Q2 Header CTA button and footer menu: every-site, or per-fork? Left out of the starter.
- [ ] Q3 Tailwind's default theme (`--color-red-500`…) is still importable as utilities.
      Reset the `--color-*` namespace in the starter, or leave it?
- [ ] Q4 Standards fixes to raise (outside this repo): docs/12 example uses
      `--wp--preset--font-size--h2` (WP prints `--h-2`); docs/02 base's `var(--header-h, 0)`
      is flagged by `verify-tokens.sh` when the header isn't sticky; docs/02 base has no
      plain-link underline rule, so Preflight leaves links outside `.prose` undistinguished.
      Also `templates/gitignore`'s `**/vendor/` swallows docs/02's `src/vendor/*.css`
      (fixed here with a `!/themes/mbf-blank/src/vendor/` exception).

### Resolved 2026-11-05 (David: *"Go with your recommendations on D1–D8"*)
- D1 `inc/`: `setup enqueue builder helpers options forms` only. No `woocommerce`,
  `post-types`, `admin-help`, `schema`.
- D2 Header: single row; dropdowns `<details>`; no mega menu.
- D3 Breakpoint `64rem`.
- D4 Editor presets: fontSizes sm/body/lead/h4–h1; spacing fluid s/m/l/xl. Display stays
  in tokens.css only.
- D5 Components in `src/components/` until `mbf-css` exists.
- D6 Phase 4 provisions a placeholder WS Form by script; ID in Site Settings.
- D7 Gold-standard review recorded here only. Standards README untouched (outside repo).
- D8 Out: `photo` ground, `.mbf-actionbar`, `.mbf-subnav`, `comments.php`, `.mbf-dialog`.

### Tasks
- [ ] Switch Local to PHP 8.4 (theme CLAUDE.md TODO; Local is on 8.2.29)
- [ ] Enter ACF Pro + WS Form Pro licences (David)

## Live write log

| Date | Who | Command (`wp @live …`) | Backup file | Result / verified |
|---|---|---|---|---|

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
- …

## Settings that do NOT travel (re-set per environment)
- `WP_ENVIRONMENT_TYPE` (dangerous when UNSET → production → leads fire)
- `blog_public` (dangerous when CARRIED OVER → live noindexed)
- Licences: ACF Pro, form plugin, SEO plugin — domain-bound
- …
