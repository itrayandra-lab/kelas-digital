# BEAUTYVERSITY LARAVEL APPLICATION - COMPREHENSIVE STYLE GUIDE

**Last Updated:** November 11, 2025
**Framework:** Laravel 12
**CSS Framework:** Tailwind CSS v4
**Design System:** Beautyversity Dusty Rose Theme

---

## TABLE OF CONTENTS

1. [Overview](#overview)
2. [Color System](#color-system)
3. [Typography](#typography)
4. [Spacing System](#spacing-system)
5. [Component Styles](#component-styles)
6. [Shadows & Elevation](#shadows--elevation)
7. [Animations & Transitions](#animations--transitions)
8. [Border & Radius](#border--radius)
9. [Opacity & Transparency](#opacity--transparency)
10. [Responsive Design](#responsive-design)
11. [Common Tailwind CSS Patterns](#common-tailwind-css-patterns)
12. [Component Reference Code](#component-reference-code)
13. [File & Location Reference](#file--location-reference)
14. [Design Principles](#design-principles)

---

## OVERVIEW

### Design Philosophy

Beautyversity is a beauty education platform that combines modern design principles with educational functionality. The design system prioritizes:

- **Accessibility**: High contrast, semantic HTML, ARIA labels
- **Responsiveness**: Mobile-first approach with clear breakpoints
- **Consistency**: Reusable components and design tokens throughout
- **Premium Feel**: Generous spacing, smooth animations, subtle shadows
- **User Experience**: Clear visual hierarchy, intuitive interactions

### Color Theme

**Primary Palette:** Dusty Rose (warm, beauty-focused)
**Secondary Palette:** Warm grays with custom softening
**Accent Colors:** Success (green), Warning (yellow), Danger (red)
**Overall Tone:** Modern, welcoming, educational

### Typography

**Font Family:** Poppins (Google Fonts)
**Weights:** 300 (light), 400 (normal), 500 (medium), 600 (semibold), 700 (bold)
**Approach:** Varied weights for hierarchy, clear readability

---

## COLOR SYSTEM

### Primary Color Palette

The primary color palette uses a warm dusty rose theme, reflecting the beauty education focus.

| Name | Class | Hex Code | RGB | Usage |
|------|-------|----------|-----|-------|
| Primary 400 | `primary-400` | `#f4d2d6` | Pale Pink | Extra light backgrounds, disabled states |
| Primary 500 | `primary-500` | `#e6b4b8` | Dusty Rose | Featured sections, footer background, hero sections |
| Primary 600 | `primary-600` | `#d18a9b` | Rose Pink | Primary actions, badges, links, hover states |
| Primary 700 | `primary-700` | `#b96f80` | Darker Rose | Button hover, dark accents |

**Implementation:** CSS custom properties in `resources/css/app.css`

```css
@theme {
  --color-primary-400: #f4d2d6;
  --color-primary-500: #e6b4b8;
  --color-primary-600: #d18a9b;
  --color-primary-700: #b96f80;
}
```

**Font Configuration:** Poppins with weights 300, 400, 500, 600, 700

### Secondary Status Colors

| Status | Color | Usage |
|--------|-------|-------|
| Published | `oklch(58.82% 0.15 127.41)` | Green badge for published articles |
| Scheduled | `oklch(74.27% 0.18 85.36)` | Yellow badge for scheduled articles |
| Draft | Gray-100 | Light background for draft articles |
| Danger | `oklch(53.76% 0.21 19.31)` | Red for delete actions |

### Gray Palette (Custom Softened)

| Level | Class | Hex Code | Usage |
|-------|-------|----------|-------|
| 50 | `gray-50` | - | Light section backgrounds |
| 100 | `gray-100` | - | Input hover, badge backgrounds |
| 200 | `gray-200` | - | Card borders, subtle dividers |
| 300 | `gray-300` | - | Form input borders |
| 400 | `gray-400` | - | Disabled text, placeholder |
| 500 | `gray-500` | - | Secondary text, metadata, dates |
| 600 | `gray-600` | `#555555` | Navigation links, secondary body text |
| 700 | `gray-700` | `#555555` | Paragraph text, body content |
| 800 | `gray-800` | `#333333` | Primary text, headings |
| 900 | `gray-900` | `#222222` | Darkest backgrounds, main headings |

**Note:** Custom gray-700/800/900 values use softened shades for less harsh appearance on screen.

### Color Usage Examples

**Primary Color Usage:**
- `bg-primary-600` - Buttons, badges, primary actions
- `text-primary-600` - Links, category badges
- `text-primary-300` - Light text on dark backgrounds
- `hover:bg-primary-700` - Button hover states
- `hover:text-primary-600` - Link hover states
- `border-primary-500` - Accent borders

**Gray Color Usage:**
- `text-gray-900` - Heading text
- `text-gray-800` - Primary body text
- `text-gray-600` - Navigation links
- `text-gray-500` - Secondary text, dates, metadata
- `bg-gray-50` - Light section backgrounds
- `bg-gray-800` / `bg-gray-900` - Dark sections
- `border-gray-100` - Subtle card borders
- `border-gray-200` - Standard borders

---

## TYPOGRAPHY

### Font Stack

```css
font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
```

- **Primary:** Poppins (Google Fonts)
- **Fallback:** System fonts (ui-sans-serif, system-ui, sans-serif)
- **Weights:** 300, 400, 500, 600, 700

### Type Scale

| Element | Class | Size | Weight | Usage | Example |
|---------|-------|------|--------|-------|---------|
| Hero Title | `text-5xl` | 48px | 700 | Page/hero titles | Article hero section |
| Section H1 | `text-4xl` | 36px | 700 | Large headings | Section titles (md+) |
| Section H2 | `text-3xl` | 30px | 700 | Main section headings | "Latest Article", "Terpopuler" |
| H3 | `text-2xl` | 24px | 700 | Subsection headings | Category featured titles |
| H4 | `text-xl` | 20px | 600 | Smaller headings | Form labels, admin section titles |
| Body | `text-lg` | 18px | 400 | Rich text content | Article body paragraphs |
| Standard | `text-base` | 16px | 400 | Default body text | Regular paragraphs |
| Small | `text-sm` | 14px | 400 | Form labels, helper text | Input labels, article excerpt |
| Tiny | `text-xs` | 12px | 600 | Badges, timestamps | Category badges, dates |

### Font Weights

| Class | Weight | Usage | Example |
|-------|--------|-------|---------|
| `font-light` | 300 | Rare accent text | Not heavily used |
| `font-normal` | 400 | Default body text | Paragraphs, regular content |
| `font-medium` | 500 | Semi-important text | Navigation, labels |
| `font-semibold` | 600 | Form labels, button text | "Add New", form fields |
| `font-bold` | 700 | Headings, emphasis | All h1-h3, strong emphasis |

**Usage Pattern:**
```html
<!-- Heading with weight -->
<h2 class="text-3xl md:text-4xl font-bold text-gray-900">
  Section Title
</h2>

<!-- Body text with weight -->
<p class="text-base font-normal text-gray-700">
  Regular paragraph text here
</p>

<!-- Label with weight -->
<label class="text-sm font-semibold text-gray-800">
  Form Field Label
</label>
```

### Line Heights

| Class | Value | Usage |
|-------|-------|-------|
| `leading-relaxed` | 1.625 | Prose content, rich text |
| `leading-8` | 2rem | Long paragraphs, articles |
| `leading-7` | 1.75rem | List items, numbered content |
| `leading-tight` | 1.25 | Headings, short text |
| `leading-snug` | 1.375 | Subheadings |

### Letter Spacing

| Class | Value | Usage |
|-------|-------|-------|
| `tracking-wider` | 0.05em | Category labels "SKINCARE" |
| `tracking-widest` | 0.1em | Post type labels, very emphatic |
| Normal | 0 | Standard text |

---

## SPACING SYSTEM

### Padding Reference

| Class | Value | Pixels | Usage |
|-------|-------|--------|-------|
| `p-2` | 0.5rem | 8px | Small element padding |
| `p-3` | 0.75rem | 12px | Badge/label padding |
| `p-4` | 1rem | 16px | Card padding (mobile) |
| `p-6` | 1.5rem | **24px** | **Standard card padding** |
| `p-8` | 2rem | 32px | Large content padding |
| `px-3` | 0.75rem | 12px | Horizontal button padding |
| `px-4` | 1rem | 16px | Input padding, button padding |
| `px-6` | 1.5rem | 24px | Standard button horizontal |
| `py-2` | 0.5rem | 8px | Table cell padding |
| `py-3` | 0.75rem | 12px | Button vertical padding |
| `py-4` | 1rem | 16px | Card content vertical |

**Usage Pattern:**
```html
<!-- Card with standard padding -->
<div class="bg-white rounded-lg p-6">
  <h3 class="text-lg font-bold">Title</h3>
</div>

<!-- Button with padding -->
<button class="px-6 py-3 bg-primary-600 text-white rounded-lg">
  Action
</button>
```

### Margin Reference

| Class | Value | Pixels | Usage |
|-------|-------|--------|-------|
| `m-0` | 0 | 0px | Remove margin |
| `mb-2` | 0.5rem | 8px | Small bottom spacing |
| `mb-3` | 0.75rem | 12px | Standard element spacing |
| `mb-4` | 1rem | 16px | Heading bottom margin |
| `mb-6` | 1.5rem | 24px | Section bottom margin |
| `mb-12` | 3rem | 48px | Major section spacing |
| `mt-2` | 0.5rem | 8px | Small top spacing |
| `mt-3` | 0.75rem | 12px | Standard top spacing |
| `ml-2` | 0.5rem | 8px | Icon left margin |
| `ml-3` | 0.75rem | 12px | Sidebar indent |

**Usage Pattern:**
```html
<!-- Section with margin -->
<section class="mb-12">
  <h2 class="mb-4">Section Title</h2>
  <p class="mb-3">Paragraph text</p>
</section>
```

### Gap (Grid & Flexbox)

| Class | Value | Pixels | Usage |
|-------|-------|--------|-------|
| `gap-2` | 0.5rem | 8px | Tight spacing |
| `gap-3` | 0.75rem | 12px | Close spacing |
| `gap-4` | 1rem | 16px | **Default grid spacing** |
| `gap-6` | 1.5rem | 24px | Standard card spacing |
| `gap-8` | 2rem | **32px** | **Large card spacing** |
| `space-y-2` | 0.5rem | 8px | Vertical element spacing |
| `space-y-4` | 1rem | 16px | Default vertical |
| `space-y-6` | 1.5rem | 24px | Large vertical |

**Usage Pattern:**
```html
<!-- Grid with responsive gaps -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
  <!-- grid items -->
</div>

<!-- Flexbox with gap -->
<div class="flex gap-4">
  <!-- flex items -->
</div>
```

### Section Padding (Consistent Pattern)

**Standard Section Padding:**
```html
<section class="py-16 md:py-24">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- content -->
  </div>
</section>
```

- **Mobile:** `py-16` (64px vertical)
- **Tablet+:** `py-24` (96px vertical)
- **Horizontal:** `px-4` (mobile), `px-6` (sm), `px-8` (lg)

---

## COMPONENT STYLES

### Button Components

#### Primary Button (Hero/Main Actions)

```html
<button class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg
               hover:bg-primary-700 transition">
  Click Me
</button>
```

**Characteristics:**
- **Padding:** `px-6 py-3` (24px horizontal, 12px vertical)
- **Background:** `bg-primary-600` (dusty rose)
- **Text:** `text-white font-semibold`
- **Radius:** `rounded-lg` (8px)
- **Hover:** `bg-primary-700` (darker shade)
- **Animation:** `transition` (150ms default)
- **Usage:** Hero CTAs, major actions, primary forms
- **Example Locations:** Homepage hero section, article detail CTA

#### Secondary Button (Admin)

```html
<button class="px-6 py-2.5 bg-gray-100 text-gray-900 font-semibold text-sm
               rounded-lg border border-gray-300 hover:bg-gray-200 transition">
  Secondary
</button>
```

**Characteristics:**
- **Padding:** `px-6 py-2.5`
- **Background:** `bg-gray-100` (light)
- **Border:** `border border-gray-300`
- **Text:** `text-gray-900 font-semibold text-sm`
- **Radius:** `rounded-lg`
- **Hover:** `bg-gray-200`
- **Usage:** Cancel, secondary actions, less emphasis
- **Example Locations:** Admin forms, dialog actions

#### Text Link Button

```html
<a href="#" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition">
  Learn More ’
</a>
```

**Characteristics:**
- **Style:** Text-only, no background
- **Color:** `text-primary-600`
- **Hover:** `text-primary-700`
- **Weight:** `font-semibold`
- **Size:** `text-sm`
- **Usage:** Secondary navigation, "Learn more" links
- **Example Locations:** Article cards, CTA links

#### Status Badge

```html
<!-- Published (Green) -->
<span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
            bg-green-100 text-green-800">
  Published
</span>

<!-- Scheduled (Yellow) -->
<span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
            bg-yellow-100 text-yellow-800">
  Scheduled
</span>

<!-- Draft (Gray) -->
<span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
            bg-gray-100 text-gray-800">
  Draft
</span>
```

**Characteristics:**
- **Padding:** `px-3 py-1`
- **Typography:** `text-xs font-semibold`
- **Radius:** `rounded-full` (pill shape)
- **Colors:** Status-specific background + text
- **Usage:** Article status, user roles, tags
- **Example Locations:** Admin dashboard, article list, user badges

### Card Components

#### Light Card (Homepage Articles)

```html
<div class="flex flex-col md:flex-row gap-4 bg-white rounded-lg
           overflow-hidden border border-gray-100 hover:shadow-lg transition">
  <img src="" alt="" class="w-full md:w-40 h-40 object-cover">
  <div class="p-4 md:py-4 md:pr-4 flex-1">
    <span class="text-xs font-bold uppercase text-primary-600">Category</span>
    <h3 class="text-lg font-bold text-gray-800 mt-3 line-clamp-2">Title</h3>
    <p class="text-sm text-gray-600 line-clamp-1 mb-3">Excerpt</p>
    <p class="text-xs text-gray-500">Date</p>
  </div>
</div>
```

**Characteristics:**
- **Layout:** Responsive flex (column mobile, row tablet+)
- **Background:** `bg-white`
- **Border:** `border border-gray-100` (subtle)
- **Radius:** `rounded-lg` (8px)
- **Overflow:** `overflow-hidden` (clip image)
- **Shadow:** `hover:shadow-lg` (elevation on hover)
- **Animation:** `transition` (smooth)
- **Responsive:** `gap-4`, image width changes
- **Usage:** Article listings, blog cards
- **Example Locations:** Homepage latest articles, article index

#### Dark Card (Terpopuler Section)

```html
<div class="bg-gray-900 rounded-lg overflow-hidden border border-gray-700
           hover:shadow-lg transition">
  <img src="" alt="" class="w-full h-48 object-cover">
  <div class="p-6">
    <span class="text-xs font-bold uppercase text-primary-300">Category</span>
    <h3 class="text-lg font-bold text-white mt-2 mb-3 line-clamp-2">Title</h3>
    <p class="text-sm text-gray-300 line-clamp-2 mb-3">Excerpt</p>
    <div class="text-xs text-gray-400">Date</div>
  </div>
</div>
```

**Characteristics:**
- **Background:** `bg-gray-900` (dark)
- **Border:** `border border-gray-700` (subtle on dark)
- **Text:** White/gray-300 for contrast
- **Badge:** `text-primary-300` (light on dark)
- **Padding:** `p-6` (standard)
- **Radius:** `rounded-lg`
- **Shadow:** `hover:shadow-lg`
- **Usage:** Featured content, dark theme sections
- **Example Locations:** Terpopuler section, dark featured cards

#### Admin Dashboard Card

```html
<div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
  <div>
    <p class="text-sm font-medium text-gray-500">Total Articles</p>
    <p class="text-3xl font-bold text-gray-900">42</p>
  </div>
  <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center
              bg-primary-100 rounded-full">
    <i class="fas fa-file-alt text-primary-600 text-2xl"></i>
  </div>
</div>
```

**Characteristics:**
- **Layout:** Flexbox space-between
- **Background:** `bg-white`
- **Shadow:** `shadow-sm`
- **Padding:** `p-6`
- **Radius:** `rounded-lg`
- **Icon Container:** `bg-primary-100 rounded-full`
- **Typography:** Label (gray-500) + Number (gray-900 bold)
- **Usage:** KPI cards, stats display
- **Example Locations:** Admin dashboard, analytics

#### Form Card

```html
<div class="bg-white rounded-lg shadow-sm">
  <div class="p-6 md:p-8">
    <!-- form fields -->
  </div>
  <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end">
    <!-- action buttons -->
  </div>
</div>
```

**Characteristics:**
- **Two Sections:** Content area + action footer
- **Padding:** `p-6`/`p-8` (responsive)
- **Footer:** `bg-gray-50 border-t border-gray-200`
- **Radius:** `rounded-b-lg` (bottom only)
- **Layout:** Flexbox `justify-end` for buttons
- **Usage:** Create/edit forms, modal forms
- **Example Locations:** Article form, course creation, admin modals

### Input Fields

#### Text Input

```html
<input type="text" name="title"
       class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg
              focus:outline-none focus:ring-2 focus:ring-primary-500
              focus:border-transparent transition"
       placeholder="Enter title...">
```

**Characteristics:**
- **Width:** `w-full` (full width)
- **Display:** `block`
- **Padding:** `px-4 py-2.5`
- **Border:** `border border-gray-300` (default)
- **Radius:** `rounded-lg` (8px)
- **Focus State:**
  - `focus:outline-none` (remove default)
  - `focus:ring-2 focus:ring-primary-500` (custom ring)
  - `focus:border-transparent` (hide border)
- **Animation:** `transition` (smooth)
- **Usage:** Standard form fields
- **Example Locations:** Article form, search inputs

#### Checkbox (Custom Style)

```html
<div class="flex items-start">
  <div class="flex items-center h-5">
    <input type="checkbox" name="is_recommended"
           class="w-4 h-4 text-primary-600 border-gray-300 rounded
                  focus:ring-primary-500">
  </div>
  <div class="ml-3">
    <label class="font-medium text-gray-700">Recommend this article</label>
    <p class="text-sm text-gray-500">Featured in recommendations section</p>
  </div>
</div>
```

**Characteristics:**
- **Checkbox:** `w-4 h-4`
- **Accent Color:** `text-primary-600`
- **Border:** `border-gray-300`
- **Focus Ring:** `ring-primary-500`
- **Radius:** `rounded` (subtle)
- **Label:** `ml-3` (spacing), `font-medium text-gray-700`
- **Helper Text:** `text-sm text-gray-500`
- **Usage:** Boolean options, feature toggles
- **Example Locations:** Article form, settings

#### File Input

```html
<input type="file" name="thumbnail"
       class="w-full text-sm text-gray-500
              file:mr-4 file:py-2 file:px-4 file:rounded-full
              file:border-0 file:text-sm file:font-semibold
              file:bg-primary-50 file:text-primary-700
              hover:file:bg-primary-100">
```

**Characteristics:**
- **Width:** `w-full`
- **Text Color:** `text-gray-500`
- **File Button Styling:**
  - **Padding:** `py-2 px-4`
  - **Radius:** `rounded-full` (pill shape)
  - **Background:** `bg-primary-50`
  - **Text:** `text-primary-700 font-semibold`
  - **Hover:** `bg-primary-100`
- **Usage:** Image/file uploads
- **Example Locations:** Thumbnail upload, file attachments

#### Select/Dropdown

```html
<select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
              focus:outline-none focus:ring-2 focus:ring-primary-500
              focus:border-transparent transition">
  <option value="">Select an option...</option>
  <option value="1">Option 1</option>
</select>
```

**Characteristics:**
- **Width:** `w-full`
- **Padding:** `px-4 py-2.5`
- **Border:** `border border-gray-300`
- **Radius:** `rounded-lg`
- **Focus:** Ring + transparent border (same as text input)
- **Usage:** Option selection
- **Example Locations:** Category selection, status filters

### Hero Sections

#### Article Hero (with Image & Overlay)

```html
<div class="relative bg-gray-900 h-[500px] md:h-[600px] flex items-center">
  <img src="" alt=""
       class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
  <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 z-10">
    <div class="max-w-3xl">
      <span class="inline-block px-3 py-1 bg-primary-600 text-white
                   text-xs font-bold uppercase rounded-full mb-4">
        Category
      </span>
      <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Title</h2>
      <p class="text-lg text-gray-200 mb-6 line-clamp-2">Excerpt</p>
      <a href="#" class="inline-block px-6 py-3 bg-white text-primary-600
                        font-semibold rounded-md hover:bg-gray-100 transition">
        Read Article
      </a>
    </div>
  </div>
</div>
```

**Characteristics:**
- **Layout:** Relative positioning for layering
- **Image:** Absolute, full cover
- **Overlay:** Gradient `from-black/70 to-transparent`
- **Content:** Relative, z-10 (above overlay)
- **Height:** Responsive (500px mobile, 600px tablet+)
- **Text:** White on dark overlay
- **Badge:** Primary color, rounded-full
- **CTA:** White button with primary text hover
- **Usage:** Featured content, hero sliders
- **Example Locations:** Homepage hero, article show page

#### Colored Hero Section

```html
<section class="py-16 md:py-24 bg-primary-500">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
      Section Title
    </h1>
    <p class="text-lg text-white opacity-90 max-w-2xl mx-auto">
      Description text here
    </p>
  </div>
</section>
```

**Characteristics:**
- **Padding:** `py-16 md:py-24`
- **Background:** Themed color (`bg-primary-500`)
- **Text:** `text-white`
- **Subtitle:** `opacity-90` (slightly transparent)
- **Width:** `max-w-2xl` (centered, readable)
- **Typography:** Bold heading + description
- **Usage:** Featured sections, page headers
- **Example Locations:** Recommendation section, category featured

### Navigation Elements

#### Desktop Navigation Link

```html
<a href="#" class="text-gray-700 text-sm font-semibold uppercase
                   tracking-wider hover:text-primary-600 transition">
  Home
</a>
```

**Characteristics:**
- **Size:** `text-sm`
- **Weight:** `font-semibold`
- **Case:** `uppercase`
- **Spacing:** `tracking-wider` (0.05em)
- **Color:** `text-gray-700`
- **Hover:** `text-primary-600`
- **Animation:** `transition`
- **Usage:** Header navigation
- **Example Locations:** Top navigation bar, footer links

#### Dropdown Menu

```html
<div class="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-lg
           border border-gray-200 py-1 z-50">
  <a href="#" class="block px-4 py-2 text-sm text-gray-700
                     hover:bg-gray-100 transition">
    Dropdown Item
  </a>
</div>
```

**Characteristics:**
- **Positioning:** `absolute top-full left-0 mt-2`
- **Width:** `w-48` (standard dropdown)
- **Background:** `bg-white`
- **Border:** `border border-gray-200`
- **Shadow:** `shadow-lg`
- **Z-index:** `z-50` (above content)
- **Items:** `px-4 py-2 text-sm`
- **Item Hover:** `bg-gray-100`
- **Animation:** Alpine.js transitions
- **Usage:** Navigation dropdowns, action menus
- **Example Locations:** Header menus, user dropdown

---

## SHADOWS & ELEVATION

### Shadow Scale

| Class | Value | Usage |
|-------|-------|-------|
| `shadow-sm` | `0 1px 2px 0 rgba(0, 0, 0, 0.05)` | Subtle elevation, admin buttons |
| `shadow-md` | `0 4px 6px -1px rgba(0, 0, 0, 0.1)` | Standard cards |
| `shadow-lg` | `0 10px 15px -3px rgba(0, 0, 0, 0.1)` | Card hover states, dropdowns |
| `shadow-xl` | `0 20px 25px -5px rgba(0, 0, 0, 0.1)` | Major elevation |
| `shadow-2xl` | `0 25px 50px -12px rgba(0, 0, 0, 0.25)` | Featured sections |

**Usage Pattern:**

```html
<!-- Subtle card shadow -->
<div class="bg-white rounded-lg shadow-sm p-6">
  <!-- content -->
</div>

<!-- Hover elevation -->
<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition">
  <!-- content -->
</div>

<!-- Dark mode shadow -->
<div class="bg-gray-900 shadow-lg border border-gray-700">
  <!-- content -->
</div>
```

**Implementation Notes:**
- Cards start with `shadow-sm` or `shadow-md`
- Hover state upgrades to `shadow-lg`
- Dark backgrounds use `shadow-lg` for visibility
- Dropdowns/modals use `shadow-xl` or `shadow-lg`

---

## ANIMATIONS & TRANSITIONS

### Tailwind Transitions

| Class | Duration | Property | Usage |
|-------|----------|----------|-------|
| `transition` | 150ms | all | **Default - most common** |
| `transition-colors` | 150ms | colors | Color-only changes |
| `transition-all` | 150ms | all | Explicit all properties |
| `transition-shadow` | 150ms | shadow | Shadow only |
| `transition-transform` | 150ms | transform | Scale/rotate only |
| `duration-200` | 200ms | - | Faster animations |
| `duration-300` | 300ms | - | Standard animations |
| `duration-500` | 500ms | - | Slow animations |

**Usage Pattern:**

```html
<!-- Color transition -->
<a href="#" class="text-primary-600 hover:text-primary-700 transition-colors">
  Link
</a>

<!-- Full transition -->
<button class="hover:shadow-lg transition">
  Button
</button>

<!-- Custom duration -->
<div class="hover:bg-gray-100 transition duration-300">
  Content
</div>
```

### Alpine.js Transitions

Alpine.js provides more complex animations:

```html
<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95">
  <!-- content -->
</div>
```

**Transition Patterns:**
- **Menu Open:** `duration-200`, opacity + scale
- **Dropdown Reveal:** `duration-200`, opacity fade
- **Modal Open:** `duration-300`, scale + fade
- **Slide In:** `translate-x` changes with duration

**Implementation Locations:**
- Navigation dropdowns (`resources/views/layouts/app.blade.php`)
- Mobile menu overlay
- Modal dialogs
- Tooltip reveals

---

## BORDER & RADIUS

### Border Radius Scale

| Class | Value | Pixels | Usage |
|-------|-------|--------|-------|
| `rounded` | 0.25rem | 4px | Minimal rounding |
| `rounded-md` | 0.375rem | 6px | Slight rounding |
| `rounded-lg` | 0.5rem | **8px** | **Standard radius - most common** |
| `rounded-xl` | 0.75rem | 12px | Code blocks, accent elements |
| `rounded-2xl` | 1rem | 16px | Large accent elements |
| `rounded-3xl` | 1.5rem | 24px | Extra large radius |
| `rounded-full` | 9999px | - | **Pills & circles** (badges, buttons) |
| `rounded-t-lg` | 8px top | - | Top corners only (section headers) |
| `rounded-b-lg` | 8px bottom | - | Bottom corners only (card footers) |

**Usage Pattern:**

```html
<!-- Standard card -->
<div class="rounded-lg border">Card</div>

<!-- Pill shape (badge) -->
<span class="rounded-full px-3 py-1">Badge</span>

<!-- Partial rounding -->
<div class="rounded-t-lg rounded-b-2xl">Special</div>
```

### Border Styles

| Class | Value | Usage |
|-------|-------|-------|
| `border` | 1px solid | Standard borders (cards, inputs) |
| `border-0` | no border | Remove borders |
| `border-t` | top 1px | Divider lines |
| `border-b` | bottom 1px | Section separators |
| `border-l-4` | left 4px thick | Blockquotes, emphasis |

**Border Colors:**
- `border-gray-100` - Light borders (light cards)
- `border-gray-200` - Standard borders (inputs, nav)
- `border-gray-700` - Dark borders (dark cards)
- `border-primary-500` - Accent borders
- `border-white/20` - Transparent borders (overlays)
- `border-transparent` - Remove focus border

**Usage Pattern:**

```html
<!-- Light card border -->
<div class="border border-gray-100 rounded-lg">
  Light Card
</div>

<!-- Dark card border -->
<div class="border border-gray-700 rounded-lg">
  Dark Card
</div>

<!-- Input border with focus ring -->
<input class="border border-gray-300 focus:ring-2 focus:border-transparent">

<!-- Divider line -->
<div class="border-t border-gray-200"></div>
```

---

## OPACITY & TRANSPARENCY

### Opacity Classes

| Class | Value | Usage |
|-------|-------|-------|
| `opacity-0` | 0% | Hidden but takes space |
| `opacity-50` | 50% | Semi-transparent |
| `opacity-75` | 75% | Slightly transparent |
| `opacity-90` | 90% | **Very light transparency** (hero subtitle text) |
| `opacity-100` | 100% | Fully opaque (default) |

### Transparent Colors

```html
<!-- Transparent overlay -->
<div class="bg-black/70">
  70% opacity black
</div>

<!-- Transparent white -->
<span class="bg-white/20 text-white/90">
  Transparent white background with semi-opaque text
</span>

<!-- Transparent border -->
<div class="border border-white/20">
  Subtle transparent border
</div>
```

**Usage Pattern:**

```html
<!-- Hero overlay gradient -->
<div class="bg-gradient-to-r from-black/70 to-transparent">
  Image overlay
</div>

<!-- Light text on dark (semi-opaque) -->
<p class="text-white opacity-90">
  Subtitle with transparency
</p>

<!-- Color overlay on image -->
<div class="relative">
  <img src="" alt="">
  <div class="absolute inset-0 bg-black/40"></div>
</div>
```

---

## RESPONSIVE DESIGN

### Breakpoints

| Device | Width | Prefix | Typical Usage |
|--------|-------|--------|---------------|
| Mobile | 0-639px | (none) | Default styles |
| Small Tablet | 640px+ | `sm:` | Small screens, landscape phone |
| Tablet | 768px+ | `md:` | **Primary tablet breakpoint** |
| Desktop | 1024px+ | `lg:` | **Main desktop breakpoint** |
| Large Desktop | 1280px+ | `xl:` | Wide screens |
| 2XL Desktop | 1536px+ | `2xl:` | Ultra-wide screens |

### Mobile-First Approach

**Principle:** Base classes apply to mobile (0-639px) by default. Use prefixes for larger screens.

```html
<!-- Example: responsive grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
  <!-- 1 column mobile, 2 tablet, 4 desktop -->
</div>

<!-- Example: responsive sizing -->
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold">
  <!-- 30px mobile, 36px tablet, 48px desktop -->
</h2>

<!-- Example: responsive spacing -->
<section class="px-4 sm:px-6 lg:px-8 py-16 md:py-24">
  <!-- 16px mobile, 24px sm, 32px lg; 64px vertical mobile, 96px tablet+ -->
</section>
```

### Common Responsive Patterns

#### Responsive Grid

```html
<!-- 1 col mobile, 2 col tablet, 4 col desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
  <!-- items -->
</div>
```

#### Responsive Flex

```html
<!-- Vertical mobile, horizontal tablet+ -->
<div class="flex flex-col md:flex-row gap-4">
  <div class="w-full md:w-1/3">Sidebar</div>
  <div class="w-full md:w-2/3">Main Content</div>
</div>
```

#### Responsive Typography

```html
<!-- Heading scaling -->
<h1 class="text-4xl md:text-5xl lg:text-6xl font-bold">
  Large Heading
</h1>

<!-- Body text (typically stays same size) -->
<p class="text-base md:text-lg font-normal">
  Paragraph text
</p>
```

#### Hidden/Visible Elements

```html
<!-- Show only on mobile -->
<button class="md:hidden">Mobile Menu</button>

<!-- Hide on mobile, show on tablet+ -->
<nav class="hidden md:flex">Desktop Navigation</nav>

<!-- Show only on desktop -->
<aside class="hidden lg:block w-1/4">Sidebar</aside>
```

### Responsive Container Pattern

**Standard container with responsive padding:**

```html
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
  <!-- content with responsive padding -->
</div>
```

- **Mobile:** `px-4` (16px)
- **SM:** `px-6` (24px)
- **LG:** `px-8` (32px)
- **Max-width:** Container max-width (standard)

---

## COMMON TAILWIND CSS PATTERNS

### Pattern 1: Card with Image & Overlay

```html
<div class="relative rounded-lg overflow-hidden">
  <img src="" alt="" class="w-full h-48 object-cover">
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center">
    <div class="text-center text-white">
      <h3 class="text-xl font-bold">Overlay Content</h3>
    </div>
  </div>
</div>
```

### Pattern 2: Button Group

```html
<div class="flex gap-3">
  <button class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
    Primary
  </button>
  <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
    Secondary
  </button>
</div>
```

### Pattern 3: Responsive Grid with Images

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition">
    <img src="" alt="" class="w-full h-40 object-cover">
    <div class="p-4">
      <!-- card content -->
    </div>
  </div>
</div>
```

### Pattern 4: Form Layout

```html
<form class="space-y-6">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
      Label
    </label>
    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
  </div>

  <div class="flex gap-3 justify-end">
    <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
      Submit
    </button>
  </div>
</form>
```

### Pattern 5: Navigation with Dropdown

```html
<nav class="bg-white border-b border-gray-200">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <div>Logo</div>
      <div class="flex gap-8">
        <a href="#" class="text-gray-700 hover:text-primary-600 transition">Home</a>
        <div x-data="{ open: false }" class="relative">
          <button @click="open = !open" class="text-gray-700 hover:text-primary-600">
            Menu
          </button>
          <div x-show="open" class="absolute top-full right-0 mt-2 w-48 bg-white shadow-lg rounded-lg border border-gray-200 z-50">
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Item</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>
```

### Pattern 6: Hero Section

```html
<section class="relative h-[500px] md:h-[600px] flex items-center">
  <img src="" alt="" class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
  <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 z-10">
    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Title</h1>
    <p class="text-lg text-gray-200 max-w-2xl">Description</p>
  </div>
</section>
```

---

## COMPONENT REFERENCE CODE

### Complete Card Example (Copy-Paste Ready)

```html
<!-- Light Article Card -->
<div class="flex flex-col md:flex-row gap-4 bg-white rounded-lg overflow-hidden
           border border-gray-100 hover:shadow-lg transition">
  <img src="image.jpg" alt="Article title"
       class="w-full md:w-40 h-40 md:h-40 object-cover flex-shrink-0">

  <div class="p-4 md:py-4 md:pr-4 flex-1">
    <!-- Category Badge -->
    <span class="inline-block text-xs font-bold uppercase text-primary-600 mb-2">
      SKINCARE
    </span>

    <!-- Date -->
    <p class="text-xs text-gray-500 mb-1">10 Nov 2025</p>

    <!-- Title -->
    <h3 class="text-lg font-bold text-gray-800 mt-3 mb-2 line-clamp-2">
      <a href="#" class="hover:text-primary-600 transition">
        Complete Guide to Skincare
      </a>
    </h3>

    <!-- Excerpt -->
    <p class="text-sm text-gray-600 line-clamp-1 mb-3">
      Learn the essential steps for healthy skin care routine...
    </p>

    <!-- CTA (Mobile only) -->
    <a href="#" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition md:hidden inline-block">
      Baca Selengkapnya
    </a>
  </div>
</div>
```

### Complete Form Example (Copy-Paste Ready)

```html
<form class="bg-white rounded-lg shadow-sm">
  <!-- Form Content -->
  <div class="p-6 md:p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Article</h2>

    <div class="space-y-6">
      <!-- Title Field -->
      <div>
        <label class="block text-sm font-semibold text-gray-800 mb-2">
          Article Title
        </label>
        <input type="text" name="title"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                      focus:outline-none focus:ring-2 focus:ring-primary-500
                      focus:border-transparent transition"
               placeholder="Enter article title...">
      </div>

      <!-- Content Field -->
      <div>
        <label class="block text-sm font-semibold text-gray-800 mb-2">
          Content
        </label>
        <textarea name="content" rows="8"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                         focus:outline-none focus:ring-2 focus:ring-primary-500
                         focus:border-transparent transition"
                  placeholder="Enter article content..."></textarea>
      </div>

      <!-- Checkbox -->
      <div class="flex items-start">
        <div class="flex items-center h-5">
          <input type="checkbox" name="is_featured"
                 class="w-4 h-4 text-primary-600 border-gray-300 rounded
                        focus:ring-primary-500">
        </div>
        <div class="ml-3">
          <label class="font-medium text-gray-700">Featured Article</label>
          <p class="text-sm text-gray-500">Show in featured section</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Form Actions -->
  <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end gap-3">
    <button type="button"
            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-900
                   font-semibold rounded-lg hover:bg-gray-50 transition">
      Cancel
    </button>
    <button type="submit"
            class="px-6 py-2.5 bg-primary-600 text-white font-semibold rounded-lg
                   hover:bg-primary-700 transition">
      Create Article
    </button>
  </div>
</form>
```

### Complete Section Layout (Copy-Paste Ready)

```html
<!-- Featured Articles Section -->
<section class="py-16 md:py-24 bg-gray-50">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Section Header -->
    <div class="mb-12">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
        Featured Articles
      </h2>
      <p class="text-gray-600">
        Handpicked articles for your learning journey
      </p>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Card 1 -->
      <div class="bg-white rounded-lg overflow-hidden border border-gray-100
                  hover:shadow-lg transition">
        <img src="" alt="" class="w-full h-48 object-cover">
        <div class="p-6">
          <span class="text-xs font-bold uppercase text-primary-600">CATEGORY</span>
          <h3 class="text-lg font-bold text-gray-800 mt-3 mb-3 line-clamp-2">
            Article Title
          </h3>
          <p class="text-sm text-gray-600 line-clamp-3 mb-4">
            Article excerpt here...
          </p>
          <div class="flex items-center gap-2 text-xs text-gray-500">
            <span>10 Nov 2025</span>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="bg-white rounded-lg overflow-hidden border border-gray-100
                  hover:shadow-lg transition">
        <!-- Similar structure -->
      </div>

      <!-- Card 3 -->
      <div class="bg-white rounded-lg overflow-hidden border border-gray-100
                  hover:shadow-lg transition">
        <!-- Similar structure -->
      </div>
    </div>
  </div>
</section>
```

---

## FILE & LOCATION REFERENCE

### Core Styling Configuration Files

#### `resources/css/app.css`
Main stylesheet with Tailwind CSS configuration

**Key Sections:**
- **Lines 13-43:** Theme configuration
  - Primary color palette (primary-400 to primary-700)
  - Font configuration (Poppins)
  - Custom properties
- **Lines 45-53:** Base layer styles
- **Lines 56-109:** Rich text component styling
  - Prose typography
  - Code block styling
  - List styling

#### `vite.config.js`
Vite build configuration

**Key Settings:**
- Tailwind CSS v4 plugin enabled
- Entry points configuration
- CSS/JS bundling settings

### Key View Files & Styling Locations

#### `resources/views/home.blade.php`
Homepage with 9 distinct sections

| Section | Lines | Styles |
|---------|-------|--------|
| Hero Slider | 7-48 | `h-[500px] md:h-[600px]`, gradient overlay, `text-white` |
| Latest Articles | 51-140 | Grid 70/30 split, responsive cards, `gap-4 md:gap-6 lg:gap-8` |
| Terpopuler | 143-185 | Dark cards `bg-gray-900`, grid 4-col, `gap-8` |
| Recommendation | 188-236 | White cards, grid 4-col, `gap-8`, `bg-gray-50` section |
| Trending | 239-318 | Asymmetric layout, horizontal cards with images |
| Featured Category | 321-360 | Dynamic color backgrounds, overlay gradient |
| More Articles | 363-409 | Large horizontal cards, `h-40` images, `gap-6` |

#### `resources/views/layouts/app.blade.php`
Public layout template

| Component | Lines | Styles |
|-----------|-------|--------|
| Top Bar | 32-46 | `bg-gray-800 text-white` |
| Header | 49-55 | Navigation, logo, responsive |
| Navigation | 58-184 | Desktop nav, dropdowns, mobile menu |
| Footer | 258-275 | `bg-primary-500`, footer content |

#### `resources/views/layouts/admin.blade.php`
Admin layout template

| Component | Lines | Styling |
|-----------|-------|---------|
| Select2 Custom | 25-95 | Search input styling, option colors |
| Sidebar | 100-184 | Navigation menu, active states |
| Admin Structure | - | Grid layout, responsive sidebar |

#### `resources/views/admin/articles/create.blade.php`
Article form for creation/editing

| Component | Lines | Styles |
|-----------|-------|--------|
| Form Card | 25-193 | `bg-white rounded-lg shadow-sm` |
| Text Inputs | ~33 | `px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2` |
| Checkboxes | ~142-152 | Custom styled, labels with helper text |
| File Input | ~157 | Styled file button with primary colors |
| Form Footer | ~190 | `bg-gray-50 border-t rounded-b-lg` |

#### `resources/views/article/show.blade.php`
Single article display

| Component | Lines | Styles |
|-----------|-------|--------|
| Hero Section | 9-33 | `bg-primary-500 py-16 md:py-24 text-center` |
| Article Content | 35+ | Responsive typography, `max-w-4xl mx-auto` |

---

## DESIGN PRINCIPLES

### 1. Color Consistency
- **Primary:** Always use `primary-600` for main actions
- **Hover:** Use `primary-700` for darker hover state
- **Text:** Use semantic gray levels (500 for meta, 800 for body)
- **Dark Sections:** Use `gray-900` background with `gray-300/400` text

### 2. Typography Hierarchy
- **H1:** `text-5xl font-bold` (48px) - Main titles
- **H2:** `text-3xl md:text-4xl font-bold` (30-36px) - Section headers
- **H3:** `text-lg font-bold` (18px) - Card titles
- **Body:** `text-base font-normal` (16px) - Regular content
- **Meta:** `text-xs text-gray-500` (12px) - Dates, secondary info

### 3. Spacing Consistency
- **Cards:** `p-6` (24px) padding
- **Sections:** `py-16 md:py-24` (vertical), `px-4 sm:px-6 lg:px-8` (horizontal)
- **Gaps:** `gap-8` between cards (32px)
- **Margins:** `mb-4` between related elements, `mb-12` between major sections

### 4. Component Patterns
- **Cards:** `rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition`
- **Buttons:** `px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition`
- **Inputs:** `w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent`

### 5. Responsive First
- **Base:** Mobile styles (no prefix)
- **Tablet:** `md:` prefix (768px+)
- **Desktop:** `lg:` prefix (1024px+)
- **Pattern:** `class="mobile md:tablet lg:desktop"`

### 6. Animation Smoothness
- **Transitions:** `transition` (150ms default)
- **Hover Effects:** `hover:shadow-lg`, `hover:text-primary-600`
- **Alpine.js:** Use `duration-200` for menus, `duration-300` for modals

### 7. Accessibility
- **Contrast:** Ensure 4.5:1 ratio for text (AA standard)
- **Focus States:** Always include `focus:ring-2 focus:ring-primary-500`
- **Semantic HTML:** Use proper heading levels, form labels
- **ARIA:** Add labels for interactive elements

### 8. Consistency Across Codebase
- Reuse component patterns (same card style everywhere)
- Match spacing (gap-8 for all grids)
- Use same color palette (no random colors)
- Maintain typography hierarchy

---

## QUICK REFERENCE TABLE

### Colors
| Usage | Class | Hex |
|-------|-------|-----|
| Primary Button | `bg-primary-600` | `#d18a9b` |
| Primary Hover | `bg-primary-700` | `#b96f80` |
| Section BG | `bg-gray-50` | Light gray |
| Card BG | `bg-white` | White |
| Dark BG | `bg-gray-900` | `#222222` |
| Primary Text | `text-gray-900` | `#333333` |
| Secondary Text | `text-gray-600` | `#555555` |
| Meta Text | `text-gray-500` | Gray |
| Light Border | `border-gray-100` | Very light |
| Dark Border | `border-gray-700` | Dark gray |

### Spacing
| Purpose | Class | Value |
|---------|-------|-------|
| Standard Padding | `p-6` | 24px |
| Button Padding | `px-6 py-3` | 24px × 12px |
| Input Padding | `px-4 py-2.5` | 16px × 10px |
| Card Gap | `gap-8` | 32px |
| Section Vertical | `py-16 md:py-24` | 64px / 96px |
| Section Horizontal | `px-4 sm:px-6 lg:px-8` | 16px / 24px / 32px |

### Radius
| Purpose | Class | Value |
|---------|-------|-------|
| Cards | `rounded-lg` | 8px |
| Buttons | `rounded-lg` | 8px |
| Pills/Badges | `rounded-full` | 100% |
| Inputs | `rounded-lg` | 8px |

### Shadows
| Purpose | Class | Usage |
|---------|-------|-------|
| Card Base | `shadow-sm` | Subtle elevation |
| Card Hover | `hover:shadow-lg` | Hover elevation |
| Dropdown | `shadow-lg` | Menus, modals |

---

## CUSTOMIZATION GUIDE

### Adding a New Color

1. **Define in CSS:**
```css
@theme {
  --color-custom-600: #newcolor;
}
```

2. **Use in Tailwind:**
```html
<div class="bg-custom-600 text-custom-700">Content</div>
```

### Creating a New Component

1. **Define HTML Structure** (consistent with existing patterns)
2. **Apply Styling Classes** (use existing tokens)
3. **Test Responsive** (mobile, tablet, desktop)
4. **Document Pattern** (add to this guide)

### Updating Font Stack

1. **Modify `resources/css/app.css`:**
```css
@theme {
  --font-sans: "New Font", fallback-fonts;
}
```

2. **Import Font** (Google Fonts or local)

### Changing Primary Color

1. **Update in CSS:**
```css
@theme {
  --color-primary-600: #newcolor;
}
```

2. **Update all occurrences:**
- Buttons
- Links
- Badges
- Hover states

---

**End of Style Guide**

For implementation support, refer to specific component sections or file locations listed above.

Last Updated: November 11, 2025
