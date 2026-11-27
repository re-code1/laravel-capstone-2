---
name: "ATLAS Design System"
version: "1.0"
platform: "web"
framework: "tailwindcss-4"
inclusion: auto
description: "Visual design system specification for ATLAS (PSHS Asset Management System). Contains design tokens, component visual patterns, and rationale for AI agent consumption and developer reference."
Generated: "2026-08-24"
colors:
  primary:
    50: "#eff6ff"
    100: "#dbeafe"
    200: "#bfdbfe"
    300: "#93c5fd"
    400: "#60a5fa"
    500: "#3b82f6"
    600: "#2563eb"
    700: "#1d4ed8"
    800: "#1e40af"
    900: "#1e3a8a"
    950: "#172554"
  secondary:
    50: "#f9fafb"
    100: "#f3f4f6"
    200: "#e5e7eb"
    300: "#d1d5db"
    400: "#9ca3af"
    500: "#6b7280"
    600: "#4b5563"
    700: "#374151"
    800: "#1f2937"
    900: "#111827"
    950: "#030712"
  success:
    50: "#f0fdf4"
    100: "#dcfce7"
    200: "#bbf7d0"
    300: "#86efac"
    400: "#4ade80"
    500: "#22c55e"
    600: "#16a34a"
    700: "#15803d"
    800: "#166534"
    900: "#14532d"
    950: "#052e16"
  warning:
    50: "#fefce8"
    100: "#fef9c3"
    200: "#fef08a"
    300: "#fde047"
    400: "#facc15"
    500: "#eab308"
    600: "#ca8a04"
    700: "#a16207"
    800: "#854d0e"
    900: "#713f12"
    950: "#422006"
  danger:
    50: "#fef2f2"
    100: "#fee2e2"
    200: "#fecaca"
    300: "#fca5a5"
    400: "#f87171"
    500: "#ef4444"
    600: "#dc2626"
    700: "#b91c1c"
    800: "#991b1b"
    900: "#7f1d1d"
    950: "#450a0a"
  info:
    50: "#ecfeff"
    100: "#cffafe"
    200: "#a5f3fc"
    300: "#67e8f9"
    400: "#22d3ee"
    500: "#06b6d4"
    600: "#0891b2"
    700: "#0e7490"
    800: "#155e75"
    900: "#164e63"
    950: "#083344"
  sidebar:
    default: "#1C398E"
    options:
      - "#1e293b"
      - "#1e40af"
      - "#047857"
      - "#7c2d12"
      - "#4c1d95"
      - "#1f2937"
typography:
  font_family: "'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'"
  scale:
    xs: "0.75rem / 1rem"
    sm: "0.875rem / 1.25rem"
    base: "1rem / 1.5rem"
    lg: "1.125rem / 1.75rem"
    xl: "1.25rem / 1.75rem"
  weights:
    normal: 400
    medium: 500
    semibold: 600
    bold: 700
spacing:
  button_group_gap: "gap-2"
  filter_grid_gap: "gap-3"
  form_vertical: "space-y-4"
  card_padding_sm: "p-4"
  card_padding_md: "p-6"
  card_padding_lg: "p-8"
  filter_container: "p-3"
  table_cell: "px-3 py-2"
  modal_body: "px-6 py-4"
border_radius:
  button: "rounded-lg"
  card: "rounded-lg"
  badge_default: "rounded"
  badge_pill: "rounded-full"
  input: "rounded-lg"
  modal: "rounded-lg"
  dropdown: "rounded-lg"
shadows:
  none: "none"
  sm: "shadow-sm"
  md: "shadow-md"
  lg: "shadow-lg"
  modal: "shadow-xl"
breakpoints:
  sm: "640px"
  md: "768px"
  lg: "1024px"
  xl: "1280px"
  2xl: "1536px"
z_index:
  sidebar: 50
  modal_overlay: 50
  dropdown: 50
  batch_action_modal: 60
  idle_timeout_modal: 9999
  toast_stack: 9999
transitions:
  modal_enter: "ease-out 200ms"
  modal_leave: "ease-in 150ms"
  dropdown_enter: "ease-out 100ms"
  dropdown_leave: "ease-in 75ms"
  button_hover: "transition-colors"
  sidebar_slide: "duration-300 ease-in-out"
opacity:
  inactive_user: "opacity-60"
  disabled_state: "opacity-50"
  loading_spinner_ring: "opacity-25"
  loading_spinner_path: "opacity-75"
  modal_backdrop: "opacity-75"
  scrollbar_thumb: "rgba(255,255,255,0.2)"
  scrollbar_thumb_hover: "rgba(255,255,255,0.35)"
---

# ATLAS Design System

Visual design token specification for the PSHS Asset Management System (ATLAS). This document is the authoritative reference for all visual decisions — color values, spacing patterns, component appearance, and design rationale. It is consumed by AI agents (Arona, Plana) and developers building new pages.

**Source of truth:** `resources/css/app.css` (Tailwind CSS 4 `@theme` block)
**Component library:** Custom-built (no external UI library — see ADR-020)
**Tech stack:** Tailwind CSS 4.0 + Vue 3.5 + Inertia.js v2

---

## Layout Architecture

The application uses a fixed sidebar + top navbar + scrollable content area pattern. All layout structure is defined in `resources/js/layouts/AppLayout.vue`.

### Structure

```
┌──────────────────────────────────────────────────────────┐
│  Sidebar (fixed, left)  │  Main Content Area             │
│  w-64, z-50             │                                │
│  bg: dynamic            │  ┌─ Top Navbar ─────────────┐  │
│  (AppConfiguration)     │  │  h-16, bg-white, shadow  │  │
│                         │  ├─ Flash Messages ──────────┤  │
│  ┌─ Logo Area ───────┐ │  ├─ Breadcrumbs ─────────────┤  │
│  │  h-16, px-6       │ │  ├─ Page Content ────────────┤  │
│  ├─ Navigation ──────┤ │  │  px-4 sm:px-6 lg:px-8     │  │
│  │  mt-6, px-4       │ │  │  (fluid) or max-w-7xl     │  │
│  │  overflow-y-auto  │ │  │                            │  │
│  │  space-y-1        │ │  └────────────────────────────┘  │
│  └────────────────────┘ │                                │
└──────────────────────────────────────────────────────────┘
```

### Sidebar

| Property | Value | Source |
|----------|-------|--------|
| Width | `w-64` (256px) | Fixed — no collapsed state on desktop |
| Position | `fixed inset-y-0 left-0 z-50` | Always above content |
| Background | Dynamic from `AppConfiguration::get('sidenav_color')` | Default `#1C398E` (deep blue) |
| Text color | `text-white` (nav items), `text-blue-100` (inactive items) | — |
| Hover background | `color-mix(in srgb, {sidenav_color} 90%, white)` | CSS color-mix for lighter tint |
| Section headers | `text-[10px] font-semibold uppercase tracking-widest text-blue-300/60` | — |
| Nav item padding | `px-4 py-3` | — |
| Scrollbar | 4px width, transparent track, `rgba(255,255,255,0.2)` thumb | Custom webkit scrollbar in `app.css` |
| Mobile | Slides in/out via `translate-x-0` / `-translate-x-full` | `transition-transform duration-300 ease-in-out` |

### Content Area

| Property | Value | When |
|----------|-------|------|
| Background | `bg-gray-100` | Always (page-level background) |
| Max width | `max-w-5xl mx-auto` (`detail`) or `max-w-3xl mx-auto` (`form`) | Show pages / forms, applied by `PageShell` |
| Full width | No max-width constraint (`full`) | Index tables, reports |
| Horizontal padding | `px-4 sm:px-6 lg:px-8` | All pages, supplied once by `AppLayout`'s `<main>` (pages add no gutters of their own) |

### Content Width Decision

Every page picks one of three widths through `PageShell` (`resources/js/components/page/PageShell.vue`):

| Width | Class | Used by |
|-------|-------|---------|
| `full` | none | Index pages, reports, wide tables |
| `detail` | `max-w-5xl mx-auto` | Show pages; Create/Edit pages with line-item tables |
| `form` | `max-w-3xl mx-auto` | Simple Create/Edit pages |

No other page-level `max-w-*` wrappers (`2xl`, `4xl`, `6xl`, `7xl`, `max-w-full`) and no `<div class="py-6">` (it doubled `AppLayout`'s own `py-6`). Pages being migrated to this standard are tracked in `.kiro/opus_plans/ui-page-standardization.md`.

### Page Type Scale

One scale for Index, Create, Edit and Show pages:

| Element | Classes |
|---------|---------|
| Page title (`h1`) | `text-xl font-semibold text-gray-900` |
| Subtitle | `mt-1 text-xs text-gray-600` |
| Card / section title | `text-sm font-semibold text-gray-900` |
| Empty-state headline | `text-lg font-medium text-gray-900` |
| Everything else (labels, inputs at `size="sm"`, table cells, detail values, buttons, hints, errors) | `text-xs` |
| Header row spacing | `mb-4` |

Standard buttons come from `ActionButton` (variant, icon and label per action) and sit in fixed positions per page type; see `UI_PATTERNS.md` → "Button Placement Rules".

---

## Component Visual Patterns

This section documents the visual appearance of each core UI component. For API documentation (props, events, slots), see `COMPONENTS_REFERENCE.md`.

### Button (`resources/js/components/ui/Button.vue`)

Base classes applied to all buttons: `inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed`

**Size dimensions:**

| Size | Padding | Font Size | Tailwind Classes |
|------|---------|-----------|-----------------|
| `sm` (default) | 12px horizontal, 6px vertical | 12px (0.75rem) | `px-3 py-1.5 text-xs` |
| `md` | 16px horizontal, 8px vertical | 14px (0.875rem) | `px-4 py-2 text-sm` |
| `lg` | 24px horizontal, 12px vertical | 16px (1rem) | `px-6 py-3 text-base` |

**Variant color mapping:**

| Variant | Background | Text | Hover | Focus Ring |
|---------|-----------|------|-------|-----------|
| `primary` | `bg-blue-600` (#2563EB) | `text-white` | `hover:bg-blue-700` (#1D4ED8) | `focus:ring-blue-500` (#3B82F6) |
| `secondary` | `bg-gray-200` (#E5E7EB) | `text-gray-900` (#111827) | `hover:bg-gray-300` (#D1D5DB) | `focus:ring-gray-500` (#6B7280) |
| `danger` | `bg-red-600` (#DC2626) | `text-white` | `hover:bg-red-700` (#B91C1C) | `focus:ring-red-500` (#EF4444) |
| `success` | `bg-green-600` (#16A34A) | `text-white` | `hover:bg-green-700` (#15803D) | `focus:ring-green-500` (#22C55E) |

**Loading state:** Displays a spinning SVG spinner (`animate-spin`) to the left of content with `-ml-1 mr-2 h-4 w-4`. The spinner circle uses `opacity-25` stroke and the animated path uses `opacity-75` fill.

**Disabled state:** `opacity-50` + `cursor-not-allowed`. Button is non-interactive.

**LinkButton** (`resources/js/components/ui/LinkButton.vue`) uses identical visual styling but renders an `<a>` tag instead of `<button>`. Used for all navigational actions so users can middle-click to open in a new tab.

---

### Card (`resources/js/components/ui/Card.vue`)

Base appearance: `bg-white rounded-lg border border-gray-200`

**Padding options:**

| Prop Value | CSS Class | Use Case |
|-----------|-----------|----------|
| `none` | (none) | Custom internal padding needed |
| `sm` | `p-4` (16px) | Compact cards (filters) |
| `md` (default) | `p-6` (24px) | Standard content cards |
| `lg` | `p-8` (32px) | Spacious cards (forms) |

**Shadow options:**

| Prop Value | CSS Class | Use Case |
|-----------|-----------|----------|
| `none` | (none) | Nested cards, no elevation needed |
| `sm` | `shadow-sm` | Subtle elevation |
| `md` (default) | `shadow-md` | Standard card elevation |
| `lg` | `shadow-lg` | Prominent cards |

**Slots and dividers:**
- **Header slot:** Rendered with `border-b border-gray-200 pb-4 mb-4` below it.
- **Footer slot:** Rendered with `border-t border-gray-200 pt-4 mt-4` above it.
- Divider color is always `border-gray-200` (#E5E7EB).

---

### Modal (`resources/js/components/ui/Modal.vue`)

**Backdrop:** `fixed inset-0 bg-gray-500 opacity-75` — a semi-transparent gray overlay covering the entire viewport.

**Panel:** `bg-white rounded-lg shadow-xl` centered horizontally with `sm:mx-auto sm:w-full`.

**Max-width sizes:**

| Prop Value | CSS Class | Approximate Width |
|-----------|-----------|-------------------|
| `sm` | `max-w-sm` | 384px |
| `md` (default) | `max-w-md` | 448px |
| `lg` | `max-w-lg` | 512px |
| `xl` | `max-w-xl` | 576px |
| `2xl` | `max-w-2xl` | 672px |

**Animation keyframes:**

| Phase | Backdrop | Panel |
|-------|----------|-------|
| Enter (200ms ease-out) | `opacity-0` → `opacity-100` | `opacity-0 translate-y-4 sm:scale-95` → `opacity-100 translate-y-0 sm:scale-100` |
| Leave (150ms ease-in) | `opacity-100` → `opacity-0` | `opacity-100 translate-y-0 sm:scale-100` → `opacity-0 translate-y-4 sm:scale-95` |

**Internal layout:**
- Header: `border-b border-gray-200 px-6 py-4`
- Body: `px-6 py-4`
- Footer: `border-t border-gray-200 px-6 py-4 bg-gray-50`

**Keyboard interaction:** Escape key closes the modal (when `closeable: true`). Body scroll is locked while modal is open.

---

### Select (`resources/js/components/ui/Select.vue`)

**Trigger button/input styling:** Identical border and focus ring patterns as `Input` (see below), plus `cursor-pointer` and a chevron icon (`h-5 w-5 text-gray-400`) positioned at the right edge.

**Dropdown panel:** `absolute z-50 mt-1 w-full rounded-lg bg-white shadow-lg border border-gray-200 max-h-60 overflow-auto`

**Dropdown animation:**

| Phase | Classes |
|-------|---------|
| Enter (100ms ease-out) | `opacity-0 scale-95` → `opacity-100 scale-100` |
| Leave (75ms ease-in) | `opacity-100 scale-100` → `opacity-0 scale-95` |

**Option item:** `px-3 py-1.5 hover:bg-blue-50 cursor-pointer transition-colors`
**Selected item highlight:** `bg-blue-100`
**Empty state:** `px-3 py-1.5 text-gray-500 text-center` with "No options found" text.
**Searchable mode:** Input field replaces the button trigger; filtering happens client-side on `option.label`.

---

### Input (`resources/js/components/ui/Input.vue`)

Base classes: `block w-full rounded-lg border text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed`

**Size dimensions:**

| Size | Padding | Font Size | Tailwind Classes |
|------|---------|-----------|-----------------|
| `sm` | 12px horizontal, 6px vertical | 12px (0.75rem) | `px-3 py-1.5 text-xs` |
| `md` (default) | 16px horizontal, 8px vertical | 14px (0.875rem) | `px-4 py-2 text-sm` |
| `lg` | 20px horizontal, 12px vertical | 16px (1rem) | `px-5 py-3 text-base` |

**State-based border and ring colors:**

| State | Border | Focus Border | Focus Ring |
|-------|--------|-------------|-----------|
| Default | `border-gray-300` (#D1D5DB) | `focus:border-blue-500` (#3B82F6) | `focus:ring-blue-500` |
| Error | `border-red-300` (#FCA5A5) | `focus:border-red-500` (#EF4444) | `focus:ring-red-500` |
| Success | `border-green-300` (#86EFAC) | `focus:border-green-500` (#22C55E) | `focus:ring-green-500` |

**Placeholder color:** Inherits Tailwind default `placeholder-gray-400` (#9CA3AF).
**Validation error text:** Displayed below the input as `<p class="mt-1 text-xs text-red-600">`.

---

### Badge (`resources/js/components/ui/Badge.vue`)

Base classes: `inline-flex items-center font-medium`

**Variant color mapping:**

| Variant | Background | Text Color | Hex Values |
|---------|-----------|-----------|-----------|
| `primary` | `bg-blue-100` | `text-blue-800` | #DBEAFE bg, #1E40AF text |
| `secondary` | `bg-gray-100` | `text-gray-800` | #F3F4F6 bg, #1F2937 text |
| `success` | `bg-green-100` | `text-green-800` | #DCFCE7 bg, #166534 text |
| `danger` | `bg-red-100` | `text-red-800` | #FEE2E2 bg, #991B1B text |
| `warning` | `bg-yellow-100` | `text-yellow-800` | #FEF9C3 bg, #854D0E text |
| `info` | `bg-cyan-100` | `text-cyan-800` | #CFFAFE bg, #155E75 text |

**Size dimensions:**

| Size | Padding | Font Size | Tailwind Classes |
|------|---------|-----------|-----------------|
| `sm` | 8px horizontal, 2px vertical | 12px (0.75rem) | `px-2 py-0.5 text-xs` |
| `md` (default) | 10px horizontal, 4px vertical | 14px (0.875rem) | `px-2.5 py-1 text-sm` |
| `lg` | 12px horizontal, 6px vertical | 16px (1rem) | `px-3 py-1.5 text-base` |

**Border radius:** `rounded` (default rectangular) or `rounded-full` (pill shape when `rounded: true`).

---

### Table (Index Pages)

Standard Index tables use `resources/js/components/page/IndexTable.vue`, which applies the classes below. Unique pages that keep their own raw `<table>` (e.g. `Assets/Index.vue`, which has `ColumnCustomizer` and bulk selection) must match them. Reference: `resources/js/pages/Iar/Index.vue`.

| Element | Classes |
|---------|---------|
| Wrapper | `overflow-x-auto` |
| Table | `min-w-full divide-y divide-gray-200` |
| Header row | `bg-gray-50` |
| Header cell | `px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider` |
| Sortable header | Adds `cursor-pointer hover:bg-gray-100` |
| Body | `bg-white divide-y divide-gray-200` |
| Body row | `hover:bg-gray-50 cursor-pointer` (clickable rows) |
| Body cell | `px-3 py-2 text-xs text-gray-900` + alignment class |
| Empty state | Full-colspan cell with centered icon (`w-12 h-12 text-gray-400 mb-4`) + `text-lg font-medium text-gray-900` headline + `text-gray-500` subtitle |

**Sort indicators:** Active column shows `fas fa-sort-up` or `fas fa-sort-down` in `text-gray-700`. Inactive sortable columns show `fas fa-sort text-gray-300`.

---

### Toast Notification (`resources/js/components/ui/ToastNotification.vue`)

Server flash messages appear as toasts, not inline banners. `AppLayout` no longer renders flash banners at all — `ToastContainer.vue` (mounted once, last in the AppLayout template) owns them. `AuthLayout` and `GuestLayout` keep their inline banners, because unauthenticated pages do not load AppLayout.

| Property | Value |
|----------|-------|
| Position | `fixed top-4 right-4`, stacked with `flex flex-col items-end gap-2` |
| Stacking order | `z-[9999]`, teleported to `<body>`, rendered after the idle-timeout modal so it paints on top |
| Card | `w-80 rounded-lg border shadow-lg`, body `p-3` |
| Colors | Per the "Flash Message Colors" table below |
| Text size | `text-xs leading-relaxed` |
| Status icon | `h-4 w-4`, `fill="currentColor"`, 20x20 viewBox |
| Close button | `h-3.5 w-3.5` X glyph, tinted to the toast variant |
| Auto-dismiss | 5000ms (`DEFAULT_TOAST_DURATION`); `duration: 0` keeps it until dismissed |
| Max visible | 5 (`MAX_VISIBLE_TOASTS`); the oldest is dropped beyond that |
| Enter | 200ms ease-out, `opacity-0 translate-x-4` → `opacity-100 translate-x-0` |
| Leave | 150ms ease-in, reversed, with `absolute` so remaining toasts animate up |
| Accessibility | Container is `aria-live="polite"`; error toasts are `role="alert"`, others `role="status"`; a `sr-only` type prefix ("Error: ") carries the meaning that color alone would |

The wrapper is `pointer-events-none` so the empty column never blocks clicks; each toast re-enables `pointer-events-auto` for itself.

---

### Empty State (`resources/js/components/ui/EmptyState.vue`)

| Property | Value |
|----------|-------|
| Layout | `flex flex-col items-center text-center` inside the full-colspan `<td class="px-3 py-8">` |
| Icon | `h-12 w-12 text-gray-400 mb-4`, stroke-based inline SVG |
| Headline | `text-lg font-medium text-gray-900 mb-1` |
| Description | `text-xs text-gray-500 max-w-md` |
| CTA | `LinkButton variant="primary" size="sm"` with a plus icon, `mt-4` |

The empty state stays **inside** the table body rather than replacing the table, so the header row and the filter controls above remain visible. A user who filtered themselves into an empty result needs to see which filters are active.

**Copy varies by cause.** Three states, and the CTA appears in only one of them:

| Situation | Icon | CTA |
|-----------|------|-----|
| Filters or search active, zero matches | `search` | None — creating a record would not reveal the missing one |
| Archived view, zero archived records | entity icon | None |
| Genuinely no records, no filters | entity icon | Create/Add, gated on the relevant permission |

---

### Loading Affordances

ATLAS index pages reload with `preserveState: true` (SYSTEM_REFERENCE rule 16), which keeps the current rows on screen during a refetch. The loading treatment is built around that rather than against it.

| Situation | Treatment | Component |
|-----------|-----------|-----------|
| Reload with rows already on screen | 2px indeterminate bar at the top of the Card + table faded to `opacity-60 pointer-events-none` | `LoadingBar.vue` + `TableScrollHint`'s `dimmed` prop |
| Reload with zero rows (first paint, or empty result being refetched) | Pulsing placeholder rows | `TableSkeleton.vue` |
| Form submission | Existing `Button` `loading` spinner | `Button.vue` |

**Do not swap a populated table for a skeleton.** That discards the preserved rows and reintroduces the blank flash the preserve was meant to prevent. The guard is always `v-if="loading && items.data.length === 0"`.

| Property | Value |
|----------|-------|
| Progress bar | `h-0.5` track, always rendered so toggling causes no layout shift; `bg-blue-100` track, `bg-blue-600` runner at `w-1/3` |
| Progress animation | Scoped `@keyframes loading-bar-slide`, 1.1s ease-in-out infinite — the only hand-written CSS in the UI library, because Tailwind has no indeterminate-progress utility |
| Reduced motion | `prefers-reduced-motion: reduce` replaces the slide with a static full-width bar at `opacity-60` |
| Skeleton bar | `h-3 animate-pulse rounded bg-gray-200`, width cycled through `w-16 / w-28 / w-20 / w-24 / w-12 / w-20 / w-32 / w-16` so rows read as tabular data |
| Dim transition | `transition-opacity duration-150` |
| Show delay | 150ms grace period (`useInertiaLoading`) — fast responses finish inside it and never flicker the indicator |

---

## Responsive Table Strategy

ATLAS tables carry 8-12 columns at `text-xs`. That fits a 1920px display, which is what the density was tuned for, but overflows on 1366px laptops and tablets. Two mechanisms address this, and they are complementary.

### Mechanism 1 — Breakpoint Column Hiding (`hiddenBelow`)

`ColumnDefinition.hiddenBelow` marks a column as secondary and drops it below a Tailwind breakpoint. `responsiveColumnClass(column)` in `resources/js/composables/useColumnPreferences.ts` maps it to a class:

| `hiddenBelow` | Class | Column hidden under |
|---------------|-------|---------------------|
| `'md'` | `hidden md:table-cell` | 768px |
| `'lg'` | `hidden lg:table-cell` | 1024px |
| `'xl'` | `hidden xl:table-cell` | 1280px |

`table-cell` rather than `block` is required, so the cell rejoins the table layout correctly when it becomes visible again.

**The class must be applied to both the `<th>` and its matching `<td>`.** Applying it to only one desynchronises the header from the body and visibly breaks the table. On pages whose `<td>` markup is positional rather than key-driven, the mirrored class is hard-coded on the `<td>` with a comment naming the column.

**Always-visible priority order:** identifier → name/description → status → actions. Never assign `hiddenBelow` to any of these.

### Mechanism 2 — Scroll Affordance (`TableScrollHint`)

`overflow-x-auto` already made overflowing columns reachable. The gap was discoverability: with overlay scrollbars on a trackpad there is no visual cue that content continues past the right edge. `TableScrollHint.vue` adds gradient fades on whichever edge has content behind it, plus a hint line in a fixed-height strip.

This is the universal fallback and applies on every index page, including those that also use `hiddenBelow`.

### Per-Page Column Visibility

**Assets Index — intentionally no `hiddenBelow`.** Columns here are user-selected through `ColumnCustomizer` (max 10, persisted per user in localStorage). Silently hiding a column the user explicitly chose is worse than asking them to scroll. Users who need a narrower table remove columns themselves. `TableScrollHint` covers the overflow.

**IAR Index** (`resources/js/pages/Iar/Index.vue`)

| Column | Visibility |
|--------|-----------|
| IAR No. | Always |
| Supplier | Always |
| Campus | Hidden below `xl` (1280px) — cross-campus users only |
| PO Number | Hidden below `lg` (1024px) |
| IAR Date | Hidden below `lg` (1024px) |
| Status | Always |
| Lines | Hidden below `xl` (1280px) |
| Actions | Always |

**Users Index** (`resources/js/pages/Users/Index.vue`)

| Column | Visibility |
|--------|-----------|
| Checkbox (bulk select) | Always — when the user has `deactivate users` |
| Date Added | Hidden below `lg` (1024px) |
| Campus | Hidden below `xl` (1280px) — cross-campus users only |
| Name | Always |
| Email | Hidden below `lg` (1024px) |
| Roles | Hidden below `lg` (1024px) |
| Status | Always |
| Activity | Always — when the user has `view online users` |
| Category Access | Hidden below `xl` (1280px) |
| Actions | Always |

**Work Requests Index** (`resources/js/pages/WorkRequests/Index.vue`)

| Column | Visibility |
|--------|-----------|
| Date | Hidden below `lg` (1024px) |
| Campus | Hidden below `xl` (1280px) — cross-campus users only |
| WR No. | Always |
| Asset | Always |
| Priority | Hidden below `lg` (1024px) |
| Status | Always |
| Requested By | Hidden below `xl` (1280px) |
| Actions | Always |

**RIS Index** (`resources/js/pages/Ris/Index.vue`) — 5-6 columns per tab, narrow enough that no `hiddenBelow` is assigned. `TableScrollHint` applies to all three tabs.

### Verification Status

The breakpoint assignments above were derived from static analysis of column counts, `max-w-*` constraints and the 256px fixed sidebar. **They have not been verified in a browser at 1366px / 1024px / 768px.** Confirming the "no horizontal overflow at 1366px" target requires manual DevTools responsive-mode testing on each page, with a cross-campus account (which adds the Campus column) and with the widest realistic data.

---

## Iconography

ATLAS uses a hybrid icon approach: Font Awesome 6 (solid) for semantic action icons and inline SVGs for navigation and structural elements.

### Font Awesome Icons

Used for action buttons, navigation items, and status indicators. Always prefixed with `fas fa-` (solid style).

| Context | Icon | Class |
|---------|------|-------|
| Save/Submit | `fas fa-save` | Action buttons |
| Delete | `fas fa-trash` | Danger actions |
| Edit | `fas fa-edit` | Edit navigation |
| Back | `fas fa-arrow-left` | Back to list |
| Cancel | `fas fa-times` | Cancel actions |
| PDF | `fas fa-file-pdf` | Document export |
| Sort ascending | `fas fa-sort-up` | Table headers |
| Sort descending | `fas fa-sort-down` | Table headers |
| Sort neutral | `fas fa-sort` | Inactive sortable headers |
| Plus/Add | `fas fa-plus` | Create buttons (alternative to SVG) |

### Inline SVG Icons

Used for UI components where precise sizing and coloring control is needed.

| Context | Size | Typical SVG Attributes |
|---------|------|----------------------|
| Button icons (navigation) | `w-4 h-4` | `fill="none" stroke="currentColor" stroke-width="2"` |
| Navigation icons (sidebar) | `w-5 h-5` or `w-6 h-6` | `fill="currentColor"` |
| Empty state illustrations | `w-12 h-12` | `text-gray-400` |
| Select chevron | `h-5 w-5` | `fill="currentColor" text-gray-400` |
| Loading spinner | `h-4 w-4` | `animate-spin`, circle + path elements |

### Icon Pairing with Text

- Button icons always precede text with `mr-1.5` (SVG) or `mr-2` (Font Awesome `<i>` tag) spacing.
- Table action buttons are icon-only (no text label) at `size="sm"`.
- Empty state icons sit above the headline text with `mb-4`.

---

## Color Semantics

This section documents when to use each color and the reasoning behind the choice. All colors are defined in the `@theme` block of `resources/css/app.css` and mapped to Tailwind utility classes.

### Asset Status Colors

| Status | Badge Variant | Background | Text | Rationale |
|--------|--------------|-----------|------|-----------|
| Serviceable | `success` | `bg-green-100` | `text-green-800` | Green = operational, good condition |
| Unserviceable | `warning` | `bg-yellow-100` | `text-yellow-800` | Yellow/amber = caution, needs attention |
| Under maintenance | `info` | `bg-cyan-100` | `text-cyan-800` | Cyan = in-process, temporary state |
| Disposed | `secondary` | `bg-gray-100` | `text-gray-800` | Gray = terminal, no longer active |

### Document Status Colors

| Status | Badge Variant | Rationale |
|--------|--------------|-----------|
| Draft | `secondary` (gray) | Not yet actionable |
| Finalized / Submitted | `primary` (blue) | Active, awaiting next step |
| Signed / Approved / Accepted | `success` (green) | Completed successfully |
| Cancelled / Rejected | `danger` (red) | Terminated, negative outcome |
| Pending (approval/inspection) | `warning` (yellow) | Awaiting action |
| In Progress | `info` (cyan) | Currently being worked on |

### Flash Message Colors

These are the colors used by toast notifications (`ToastNotification.vue`) and by
the inline flash banners that remain in `AuthLayout` and `GuestLayout`.

| Type | Background | Border | Icon Color | Text | Use When |
|------|-----------|--------|-----------|------|----------|
| Success | `bg-green-50` | `border-green-200` | `text-green-600` | `text-green-800` | Operation completed successfully |
| Error | `bg-red-50` | `border-red-200` | `text-red-600` | `text-red-800` | Operation failed, validation error |
| Warning | `bg-yellow-50` | `border-yellow-200` | `text-yellow-600` | `text-yellow-800` | Caution, side effects occurred |
| Info | `bg-blue-50` | `border-blue-200` | `text-blue-600` | `text-blue-800` | Neutral information |

> ⚠️ **"Info" means two different hues depending on context, and both are correct.** Flash messages
> and toasts use **blue** (above), matching `ToastNotification.vue`. The `info` *design token* in the
> `@theme` block and the `Badge` `info` variant are **cyan** (`bg-cyan-100 text-cyan-800`) — which is
> also what "Under maintenance" uses. Pick by context: blue for transient notifications, cyan for
> persistent status labels. Do not "unify" them without a deliberate decision.

### Form Validation Colors

| State | Border | Focus Ring | Error Text | Source |
|-------|--------|-----------|-----------|--------|
| Default | `border-gray-300` | `ring-blue-500` | — | Normal input state |
| Error | `border-red-300` | `ring-red-500` | `text-red-600 text-xs` | Failed validation |
| Success | `border-green-300` | `ring-green-500` | — | Confirmed valid (rare) |

### Interactive State Colors

| State | Color Application | Value |
|-------|------------------|-------|
| Hover (primary button) | Background darkens one shade | `blue-600` → `blue-700` |
| Hover (table row) | Background tints | `bg-gray-50` |
| Hover (dropdown option) | Background tints | `bg-blue-50` |
| Selected (dropdown option) | Background fills | `bg-blue-100` |
| Focus ring | 2px ring with offset | `ring-{variant}-500` with `ring-offset-2` |
| Disabled | Reduced opacity | `opacity-50` |
| Inactive user row | Reduced opacity | `opacity-60` |

---

## Density & Government Form Aesthetic

ATLAS uses a deliberately dense UI that diverges from typical SaaS applications. This is intentional and driven by the system's domain.

### Why `text-xs` Is the Base Size

Philippine government asset management involves forms like PAR (Property Acknowledgment Receipt), ICS (Inventory Custodian Slip), RIS (Requisition and Issue Slip), and IAR (Inspection and Acceptance Report). These official forms pack significant data into compact spaces — 8pt to 9pt font equivalents on A4/Letter paper.

ATLAS mirrors this density so that:
1. **Screen views match printed output** — users see data at roughly the same density as the physical forms they're accustomed to.
2. **More rows visible per screen** — property officers scan hundreds of assets daily; vertical density reduces scrolling.
3. **Table columns fit without horizontal scroll** — compact text allows 8-12 columns on a standard 1920px display.

### Text Size Hierarchy

| Element | Size | Tailwind | Reasoning |
|---------|------|----------|-----------|
| Table cells, button labels, filter controls | 12px | `text-xs` | Base density — matches government form aesthetics |
| Form field text (md inputs), secondary info | 14px | `text-sm` | Used sparingly in larger form contexts |
| Empty state headlines | 18px | `text-lg` | Needs to stand out from surrounding xs text |
| Page titles (`<h1>`) | 20px | `text-xl` | Single use per page — largest text on screen |
| Sidebar section headers | 10px | `text-[10px]` | Minimal — labels for nav groups |

### Table Density

| Property | Value | Comparison to Typical SaaS |
|----------|-------|---------------------------|
| Cell padding | `px-3 py-2` (12px / 8px) | SaaS typically uses `px-6 py-4` (24px / 16px) |
| Row height | ~32-36px | SaaS typically 48-56px |
| Header text | `text-xs uppercase tracking-wider` | SaaS often `text-sm` |
| Body text | `text-xs` | SaaS typically `text-sm` |
| Long text handling | `max-w-[250px] truncate` | Prevents layout breakage at compact sizes |

### Form Density

| Property | Value |
|----------|-------|
| Field vertical spacing | `space-y-4` (16px gaps) |
| Label to input gap | `mb-1` (4px) |
| Filter grid | `grid-cols-4 gap-3` (12px between filters) |
| Card internal padding | `p-3` for filter cards, `p-6` for form cards |

---

## Accessibility

### Focus Ring Pattern

All interactive elements use a consistent focus ring: `focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{color}-500`

| Component | Focus Ring Color | Source |
|-----------|-----------------|--------|
| Primary button | `ring-blue-500` | Matches variant |
| Secondary button | `ring-gray-500` | Matches variant |
| Danger button | `ring-red-500` | Matches variant |
| Success button | `ring-green-500` | Matches variant |
| Default input | `ring-blue-500` | Always blue for inputs |
| Error input | `ring-red-500` | Matches error state |
| Success input | `ring-green-500` | Matches success state |

The `ring-offset-2` creates a 2px white gap between the element border and the ring, ensuring visibility against both light and dark backgrounds.

### Keyboard Navigation

| Component | Key | Action |
|-----------|-----|--------|
| Modal | `Escape` | Closes modal (when `closeable: true`) |
| Select (non-searchable) | `Enter` / `Space` | Toggles dropdown open/close |
| Select (searchable) | Focus | Opens dropdown automatically |
| Select (dropdown open) | Click outside | Closes dropdown |
| Button | `Enter` / `Space` | Activates button (native behavior) |

### Color Contrast

The design system uses high-contrast color pairings:
- Dark text on light backgrounds: `text-gray-900` (#111827) on `bg-white` — contrast ratio > 15:1
- White text on colored backgrounds: `text-white` on `bg-blue-600` (#2563EB) — contrast ratio > 4.5:1
- Muted text: `text-gray-500` (#6B7280) on `bg-white` — contrast ratio ~5.7:1 (passes AA for normal text)
- Badge text: e.g., `text-green-800` (#166534) on `bg-green-100` (#DCFCE7) — contrast ratio > 7:1

### ARIA and Semantic HTML

- Buttons use native `<button>` elements (never styled `<div>` or `<span>`).
- Navigation links use `<a>` via `LinkButton` for native link semantics.
- Modal uses `<teleport to="body">` to escape DOM nesting issues.
- Inputs use associated `<label>` elements with `for` / `id` pairing (handled in `FormField` wrapper).
- Disabled inputs get `disabled` attribute (not just visual styling).
- Required fields are indicated with a red asterisk `<span class="text-red-500">*</span>`.

---

## Dark Mode

**Dark mode is NOT implemented.** ATLAS assumes a light-mode-only environment.

All color tokens are designed for display against:
- Page background: `bg-gray-100` (#F3F4F6)
- Card/content background: `bg-white` (#FFFFFF)
- Sidebar: dynamic color from `AppConfiguration` (default `#1C398E`)

If dark mode is ever implemented, the following areas would require token variants:
- Card backgrounds (white → dark gray)
- Border colors (gray-200 → gray-700)
- Text colors (gray-900 → gray-100)
- Table row hover (gray-50 → gray-800)
- Modal backdrop opacity may need adjustment

---

## Configurable Theming

Certain visual properties are stored in the `app_configurations` database table and can be changed at runtime by the System Administrator via the App Configuration management page (`/app-configurations`).

### Runtime-Configurable Visual Properties

| Config Key | Data Type | Default | Effect |
|-----------|-----------|---------|--------|
| `sidenav_color` | Hex string | `#1C398E` | Sidebar background color |
| `app_name` | String | "PSHS Asset Management System" | Browser tab title, navbar text |
| `app_short_name` | String | "ATLAS" | Sidebar logo text |

**Sidebar color options** (predefined in seeder, but any valid hex is accepted):

| Label | Hex Value | Description |
|-------|-----------|-------------|
| Slate | `#1e293b` | Dark slate gray |
| Blue | `#1e40af` | Deep blue |
| Green | `#047857` | Forest green |
| Orange | `#7c2d12` | Burnt orange |
| Purple | `#4c1d95` | Deep purple |
| Gray | `#1f2937` | Charcoal gray |
| Default | `#1C398E` | PSHS brand blue |

### How Dynamic Colors Work

The sidebar color is applied via inline `style` binding in `AppLayout.vue`:

```vue
<aside :style="{ backgroundColor: appConfig.sidenav_color }">
```

Hover states for sidebar items use CSS `color-mix()` to derive a lighter tint:

```css
color-mix(in srgb, {sidenav_color} 90%, white)  /* Hover: 10% lighter */
color-mix(in srgb, {sidenav_color} 80%, black)  /* Active: 20% darker */
```

### Scope

All configurable visual properties are **system-wide** (not per-campus). They affect all users regardless of campus assignment. The System Administrator controls these values centrally.

---

## Tailwind CSS 4 Configuration

ATLAS uses Tailwind CSS 4's new configuration approach — no `tailwind.config.ts` or `tailwind.config.js` file exists. All theme configuration lives in the `@theme` directive inside `resources/css/app.css`.

### Configuration File

```css
/* resources/css/app.css */
@import 'tailwindcss';

@plugin '@tailwindcss/forms';
@plugin '@tailwindcss/typography';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, ...;
    --color-primary-50: #eff6ff;
    --color-primary-600: #2563eb;
    /* ... full palette defined in YAML front matter above */
}
```

### Key Differences from Tailwind CSS 3

| Feature | Tailwind 3 | Tailwind 4 (ATLAS) |
|---------|-----------|-------------------|
| Config file | `tailwind.config.js` | None — uses `@theme` in CSS |
| Plugin loading | `require(...)` in config | `@plugin '...'` in CSS |
| Content scanning | `content: [...]` in config | `@source '...'` in CSS |
| Custom colors | `theme.extend.colors` in config | `--color-{name}-{shade}` CSS custom properties |
| Font family | `theme.fontFamily` in config | `--font-sans` CSS custom property |

### Plugins Active

| Plugin | Purpose | Declaration |
|--------|---------|-------------|
| `@tailwindcss/forms` | Consistent form element styling (resets browser defaults) | `@plugin '@tailwindcss/forms'` |
| `@tailwindcss/typography` | Rich text/prose styling (for guide pages, descriptions) | `@plugin '@tailwindcss/typography'` |

### Color Usage Convention

In templates, use Tailwind's standard utility classes. The `@theme` custom properties are automatically registered as Tailwind colors:

```html
<!-- These map to the @theme custom properties -->
<div class="bg-primary-600 text-white">  <!-- --color-primary-600: #2563eb -->
<div class="bg-danger-100 text-danger-800">  <!-- --color-danger-100, --color-danger-800 -->
<div class="border-secondary-200">  <!-- --color-secondary-200: #e5e7eb -->
```

However, most of the codebase uses Tailwind's **built-in color names** (`blue-600`, `gray-200`, `red-100`) rather than the semantic aliases (`primary-600`, `secondary-200`, `danger-100`). Both are valid — the `@theme` block maps to the same hex values as Tailwind's defaults. The semantic names exist for documentation clarity and potential future rebranding.
