# Markup contract — mbf-blank

CSS is half a component; this is the other half (docs/14). Each entry is the structure the
partial emits and the CSS relies on. Moves to `mbf-css/contract.md` once that package exists
(STATUS D5). Change a class or an element here → it's a **major** change for every fork.

Every layout is a `section` built by `mbf_section_attrs()`:

```
section.mbf-section.mbf-{layout}[.mbf-section--{ground}][.mbf-section--accent-{slot}][.mbf-section--{spacing}]
        aria-labelledby → its heading id (only when it has a heading)
  .mbf-section__inner          container-type: inline-size; capped at --wrap
```

`ground` ∈ tertiary | secondary | primary (base = no modifier). `accent` only when
`mbf_accent_colour()` lets an editor's choice through. `spacing` ∈ flush-top | flush-btm | flush.

## Shared partials (`template-parts/`)

```
card.php                     ($args: title, text, url, image, level, cta, tag, icon)
article.mbf-card
  img.mbf-card__image         optional; alt="" aria-hidden="true"; size 'card' (16:10)
  .mbf-card__body             required; carries the padding
    .mbf-card__head           only with a tag or icon; one subgrid row, holds the title
      span.mbf-card__icon-tile > i.mbf-card__icon[aria-hidden]  only with no image and icons on
                                (every mbf_icon(): i.fa-duotone.fa-regular, style via mbf_icon_style)
      span.mbf-card__tag      read before the title
    h{level}.mbf-card__title  h2 under an archive h1, h3 under a section h2
      a.mbf-card__hit         title text ONLY — the one link, stretched by ::before
    p.mbf-card__text          optional
    span.mbf-card__cta.mbf-go aria-hidden="true", never a link; only when linked
      span.mbf-go__arrow      nudges with the card's hover/focus (go.css)
  · no url → no a, no cta, no lift, no image zoom, no border change (all gated on :has(.mbf-card__hit))
  · in .mbf-grid: subgrid rows image | title | text | cta
  · surface is always light: re-scopes --section-text/heading/muted and --card-accent
    to the light-ground roles; keeps the section's --focus-ring (the ring is outside)

section-heading.php          ($args: eyebrow, heading, lead, link, id, level=2, size='h2')
header.mbf-section-heading
  p.mbf-eyebrow               optional
  h{level}.{size}#{id}        optional
  p.lead.mbf-section-heading__lead   optional
  a.mbf-section-heading__link.mbf-go   optional
    span.mbf-go__arrow[aria-hidden]   nudges on hover/focus (go.css)

ticks.php                    ($args: items[text])
ul.mbf-ticks[role=list] > li.mbf-ticks__item    tick = ::before with empty alt

badges.php                   ($args: items[name, logo])
ul.mbf-badges[role=list] > li.mbf-badge[.mbf-badge--logo]    text, or img (alt = name)

file-list.php                ($args: files[title(req), file(req), note, cover])
ul.mbf-files[role=list] > li.mbf-file
  img.mbf-file__cover         optional; decorative
  .mbf-file__body > a.mbf-file__link[download] (span.mbf-file__meta "(PDF, 1.2 MB)")  p.mbf-file__note

finder.php                   ($args: context=panel|glass|section, level, show_question) data: inc/finder.php
.mbf-finder.mbf-finder--{context}[data-mbf-finder=JSON]
  .mbf-finder__head
    p.mbf-eyebrow             panel and glass
    p.mbf-finder__step[aria-live=polite]    empty (hidden) until finder.js runs
  .mbf-finder__bars[aria-hidden] > span.mbf-finder__bar[.is-on]   built by finder.js, one per step
  .mbf-finder__stage
    h{level}.h4.mbf-finder__question      when show_question
    ul.mbf-finder__options[role=list] > li > a.mbf-finder__option   the no-JS finder
  · finder.js swaps the stage: button.mbf-finder__option (a link if it has no page),
    p.mbf-finder__intro, .mbf-ticks, .mbf-buttons, button.mbf-finder__back
  · panel: light card, re-scopes the section family AND --focus-ring (its rings are inside it)
  · glass: frosted, keeps the dark ground's section family; solid under prefers-reduced-transparency
  · the form button adds ?need={key}&site={key}: WS Form fields read #query_var(need)

cta-buttons.php              ($args: buttons[ACF link], phone_fallback)
.mbf-buttons
  a.mbf-button                first
  a.mbf-button.mbf-button--secondary   any second; "Call {phone}" when phone_fallback
    span.sr-only "(opens in a new tab)"   only for target=_blank

page-hero.php                ($args: title, lead, meta, datetime, crumbs)
.mbf-section.mbf-section--flush-btm.mbf-page-hero > .mbf-section__inner
  nav.mbf-crumbs              via crumbs.php
  .mbf-page-hero__text
    h1.h1                     the page's only h1
    p.mbf-meta > time[datetime]   optional
    p.lead                    optional

crumbs.php                   ($args: items[[label, url]]) — hidden for a 1-item trail
nav.mbf-crumbs[aria-label=Breadcrumb] > ol.mbf-crumbs__list[role=list]
  li.mbf-crumbs__item > a.mbf-crumbs__link | span[aria-current=page] (last)

social-links.php             ($args: links[[network, url]], icons)
ul.mbf-social[role=list] > li > a.mbf-social__link[.mbf-social__link--icon]
  i.fa-brands.fa-{mark}.mbf-social__icon[aria-hidden] + span.sr-only {name}   icons on
  {name} as visible text                                icons off, or unmapped network
  · icons = Site Settings › Icons has a Font Awesome kit token (CSS build, Brands family)

enquiry-form.php             ($args: form_id)
.mbf-form > form.wsf-form     admin-only comment when WS Form or the ID is missing
  · styled through WS Form's own --wsf-* variables (src/vendor/wsform.css, unlayered):
    labels/required mark/errors read the section family; fields are a light surface
  · focus = our outline (WS Form's box-shadow is overridden); fields + submit ≥ --button-h
```

## Layouts (`template-parts/blocks/`)

```
hero.php        eyebrow, heading(req), lead, button_1, button_2, badges[text], panel(image|finder|gallery), image, gallery[ids], glance[label,value], background_image, position
  section.mbf-hero[.mbf-hero--media][.mbf-hero--photo]
    img.mbf-hero__photo[alt=""]   dark grounds only, under a veil (::before, --hero-veil)
  __inner > .mbf-hero__grid
    .mbf-hero__text > p.mbf-eyebrow  h1|h2.display#id  p.lead  .mbf-buttons  .mbf-badges
    img.mbf-hero__image       library alt; eager + fetchpriority=high when first
    | .mbf-finder--panel      panel=finder, on a light ground, when the finder has options
    | .mbf-finder--glass      panel=finder, on a dark ground (mbf_is_dark_ground())
  · h1 only when position = 1 (page.php then skips page-hero)
  panel=gallery with photos → the solution hero instead:
  section.mbf-hero.mbf-hero--solution > __inner
    nav.mbf-crumbs            only when position = 1
    .mbf-hero__grid > .mbf-gallery (gallery.php)
                      .mbf-hero__text > p.mbf-eyebrow  h1|h2.h1.mbf-hero__title#id  p.lead  .mbf-badges (mono)
                                        dl.mbf-glance > .mbf-glance__row > dt.__label dd.__value   .mbf-buttons

gallery.php (template-parts/; $args: ids, eager, label)   JS: assets/js/gallery.js
  .mbf-gallery[data-mbf-gallery when 2+] > figure.mbf-gallery__main
    img.mbf-gallery__image.is-on (first; others aria-hidden="true", lazy)
    p.mbf-gallery__count[aria-live=polite]   button.mbf-gallery__nav--prev|next[tabindex=-1][aria-hidden]
    figcaption.mbf-gallery__caption          only when a photo has a Media Library caption
  ul.mbf-gallery__thumbs > li > button.mbf-gallery__thumb[aria-pressed]   2+ photos

text.php        heading, lead, body
  section.mbf-text > __inner > header.mbf-section-heading  .prose

split.php       eyebrow, heading, body, points[text], image(req), image_side(end|start), button_1, button_2
  section.mbf-split.mbf-split--image-{side}[.mbf-split--portrait] > __inner > .mbf-split__grid
    .mbf-split__text > header.mbf-section-heading  .prose  .mbf-ticks  .mbf-buttons
    img.mbf-split__image      library alt; 3:2 cropped, or whole when portrait:
                              style="--split-image-ratio: w/h" from its metadata, width from the 24rem cap
  · DOM is always text → image; image-start moves it with grid-column

cards.php       heading, lead, link, cards[tag, title(req), text, url, icon, image]
  section.mbf-cards > __inner > header.mbf-section-heading
    .mbf-grid[.mbf-grid--pairs] > article.mbf-card × n   (--pairs: exactly 2 or 4)

cta-band.php    eyebrow, heading(req), text, button_1, button_2
  section.mbf-cta-band > __inner > .mbf-cta-band__row
    .mbf-cta-band__text > p.mbf-eyebrow  h2.h2#id  p.lead
    .mbf-buttons              phone_fallback: no button_2 → "Call {phone}"

faq.php         heading, lead, items[question(req), answer], aside_heading, aside_text, aside_button
  section.mbf-faq > __inner
    [.mbf-faq__grid > .mbf-faq__intro  when a heading, lead or aside: intro | questions, intro sticky once split]
      header.mbf-section-heading
      aside.mbf-faq__aside > h3.h4  p  .mbf-buttons     when aside_heading
    .mbf-faq__list > details.mbf-faq__item[open on the first non-blank row]
      summary.mbf-faq__question  text + span.mbf-faq__marker[aria-hidden]  (plus → cross)
      .mbf-faq__answer.prose

stats.php       heading, items[value(req), label], badges[name, logo]
  section.mbf-stats > __inner > header  ul.mbf-stats__list[role=list]  .mbf-badges
    li.mbf-stat > span.mbf-stat__value  span.mbf-stat__label   (DOM = visual order)

quote.php       quote(req), name, role
  section.mbf-quote > __inner > figure.mbf-quote__figure
    blockquote.mbf-quote__text > p   figcaption.mbf-quote__cite (name, role)

steps.php       heading, lead, items[title(req), text, tag], button_1
  section.mbf-steps > __inner > header  ol.mbf-steps__list[role=list]  .mbf-buttons
    li.mbf-steps__item > h3|h2.h4.mbf-steps__title  p  span.mbf-steps__tag
  · number = CSS counter in ::before with alt "" (the list position is announced)

logos.php       heading, logos[attachment IDs]
  section.mbf-logos > __inner > header  ul.mbf-logos__list[role=list] > li > img (library alt)

form.php        heading, text, points[text], show_contact   (form ID: Site Settings form_enquiry)
  section.mbf-enquiry > __inner > .mbf-enquiry__grid
    .mbf-enquiry__intro > header.mbf-section-heading  .mbf-ticks  ul.mbf-enquiry__contact[role=list]
    .mbf-form

finder.php      heading, lead   (everything else: Site Settings › Solution finder)
  section.mbf-finder-section > __inner > header.mbf-section-heading  .mbf-finder--section
  · no heading: the first question is the section heading, and isn't asked twice

case-study.php  eyebrow, heading(req), text, metrics[value(req), label], quote, name, role, image(req), image_side, button_1
  section.mbf-case-study.mbf-case-study--image-{side} > __inner > .mbf-case-study__grid
    .mbf-case-study__text > header.mbf-section-heading
      ul.mbf-case-study__metrics[role=list] > li.mbf-case-study__metric > span.__value  span.__label
      figure.mbf-case-study__quote > blockquote > p   figcaption.mbf-meta
      .mbf-buttons
    img.mbf-case-study__image library alt

hotspots.php    show_scenes[scene keys; empty = all]  (scenes: Site Settings › Hotspots → mbf_hotspots_config())
  section.mbf-hotspots > __inner > header (hotspot_heading, hotspot_lead)  .mbf-hotspots__scenes[data-mbf-hotspots]
    div.mbf-hotspots__tabs[role=tablist] > button.mbf-hotspots__tab[role=tab]   built by hotspots.js
    .mbf-hotspots__scene[data-mbf-hotspots-scene=label]      image | list at 2:1 from 56rem; stacked: list 2 cols from 30rem, 1 below
      h3|h2.h4.mbf-hotspots__label  several scenes; hidden once tabs exist
      .mbf-hotspots__media > img.mbf-hotspots__image (library alt)
        span.mbf-hotspots__marker[aria-hidden][data-mbf-spot][style=--x;--y]    not focusable
          > span.mbf-hotspots__pop.--start|--end[.--below]   on hover/pairing; Escape hides
              img.mbf-hotspots__pop-image[alt=""] (linked page's featured image, if any)
              .mbf-hotspots__pop-body > span.__pop-title  span.__pop-text#uid
      ol.mbf-hotspots__list[role=list] > li.mbf-hotspots__item[data-mbf-spot]
        a.mbf-hotspots__link[href][aria-describedby=uid] | span.mbf-hotspots__link (no page)
          span.mbf-hotspots__n[aria-hidden]  span.mbf-hotspots__title
  · spot: solution (finder answer key) → title, info (answer label) and page, unless the spot
    sets its own. A spot with no title either is dropped.
  · the list is the content; markers and popups repeat it. .is-active pairs both ways; a marker
    click follows its link, else marks and scrolls to it.

specs.php       heading, lead, groups[heading, rows[label, value]], files[title, file]
  section.mbf-specs > __inner > header  .mbf-specs__tables  .mbf-files
    table.mbf-specs__table > caption.mbf-specs__caption  tbody > tr > th[scope=row]  td

downloads.php   heading, lead, files[title(req), file(req), note, cover]
  section.mbf-downloads > __inner > header  .mbf-files

posts.php       heading, lead, source(latest|chosen), category, count, chosen[IDs], link
  section.mbf-posts > __inner > header  .mbf-grid[.mbf-grid--pairs] > article.mbf-card × n
```

Each layout returns nothing when its required content is missing, so an empty row never
prints an empty section.
