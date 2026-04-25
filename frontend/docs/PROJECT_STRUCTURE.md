# 📁 Frontend Project Structure - Scenariaid

This document provides a detailed overview of the frontend project structure built with Next.js, TypeScript, and Tailwind CSS.

## 🌳 Project Overview

```
frontend/
├── .gitignore                    # Git ignore file
├── README.md                     # Main project documentation
├── docs/                        # Documentation folder
│   ├── README.md               # Documentation index and guide
│   ├── PROJECT_STRUCTURE.md    # Detailed project structure
│   ├── CODING_STANDARDS.md    # Coding conventions and standards
│   ├── AGENTS.md               # AI agent rules
│   └── CLAUDE.md              # Claude AI configuration
├── eslint.config.mjs             # ESLint configuration
├── next-env.d.ts                 # Next.js type definitions
├── next.config.ts                # Next.js configuration
├── package.json                  # Dependencies and scripts
├── package-lock.json             # Dependencies lock file
├── postcss.config.mjs            # PostCSS configuration
├── tsconfig.json                 # TypeScript configuration
├── node_modules/                 # Installed dependencies
├── public/                       # Static assets
│   ├── favicon.ico
│   ├── next.svg
│   └── vercel.svg
├── src/                          # Main source code
│   ├── app/                      # App Router (Next.js 13+)
│   │   ├── favicon.ico
│   │   ├── globals.css          # Global styles and CSS variables
│   │   ├── layout.tsx           # Root layout with font configuration
│   │   ├── page.tsx             # Home page
│   │   ├── about/               # About page with company info
│   │   │   └── page.tsx         # About us page
│   │   ├── tutorial/            # Tutorial section with guides
│   │   │   └── page.tsx         # Tutorial center page
│   │   ├── contact/             # Contact information and form
│   │   │   └── page.tsx         # Contact us page
│   │   ├── terms/               # Terms and conditions
│   │   │   └── page.tsx         # Legal terms page
│   │   └── account/             # Account management section
│   │       ├── page.tsx         # Main account page
│   │       ├── settings/        # Account settings
│   │       │   └── page.tsx     # Settings and preferences
│   │       ├── billing/          # Billing and subscription
│   │       │   └── page.tsx     # Payment methods and history
│   │       └── security/         # Security settings
│   │           └── page.tsx     # Authentication and 2FA
│   ├── components/               # Reusable React components
│   │   ├── ui/                 # Base UI components library
│   │   │   ├── button.tsx      # Button component with variants
│   │   │   ├── card.tsx        # Card components
│   │   │   ├── input.tsx       # Input field component
│   │   │   ├── typography.tsx  # Typography components
│   │   │   │   ├── button.tsx      # Button component with variants
│   │   │   ├── card.tsx        # Card components
│   │   │   ├── input.tsx       # Input field component
│   │   │   ├── typography.tsx  # Typography components (H1, H2, H3, P, Lead, Large, Small, Muted)
│   │   │   ├── badge.tsx       # Badge component
│   │   │   └── index.ts        # Barrel exports
│   │   └── layout/             # Layout-specific components
│   │       ├── container.tsx    # Responsive container
│   │       └── header.tsx       # Header component
│   ├── lib/                      # Utilities and helper functions
│   │   ├── constants.ts          # Design tokens and constants
│   │   └── utils.ts            # Utility functions
│   └── pages/                    # Pages directory (legacy/backup)
│       └── (empty)              # Currently empty - using app router
├── .next/                        # Next.js build output
└── .windsurf/                    # Windsurf workspace settings
```

## 📂 Detailed Directory Breakdown

### � `src/` Directory Overview

#### `src/app/` - App Router (Next.js 13+)

- **Purpose**: Main application routing and page components
- **Structure**: File-based routing system
- **Files**:
    - `layout.tsx`: Root layout with font configuration and metadata
    - `page.tsx`: Home page with component showcase
    - `globals.css`: Global styles and CSS variables
    - `favicon.ico`: Site favicon

#### `src/components/` - Reusable React Components

- **Purpose**: Modular, reusable UI components for the application
- **Structure**: Separated into logical subdirectories

##### `src/components/ui/` - Base UI Components Library

- **Purpose**: Core UI building blocks with consistent design
- **Components**:
    - `button.tsx`: Button component with multiple variants (default, red, blue, light, outline, ghost, link)
    - `card.tsx`: Card system with Header, Title, Description, Content, Footer
    - `input.tsx`: Form input with accessibility features
    - `typography.tsx`: Text components (H1-H4, P, Lead, Large, Small, Muted)
    - `badge.tsx`: Badge component with color variants
    - `index.ts`: Barrel exports for easy importing

##### `src/components/layout/` - Layout-Specific Components

- **Purpose**: Application layout and structural components
- **Components**:
    - `container.tsx`: Responsive container with size variants (sm, md, lg, xl, full)
    - `header.tsx`: Sticky navigation header with backdrop blur

#### `src/lib/` - Utilities and Helper Functions

- **Purpose**: Shared utilities, constants, and helper functions
- **Files**:
    - `constants.ts`: Design tokens (COLORS, FONT_WEIGHTS, BREAKPOINTS, SPACING)
    - `utils.ts`: Utility functions (cn, formatCurrency, formatDate, debounce, generateId)

#### `src/pages/` - Pages Directory (Legacy/Backup)

- **Purpose**: Legacy pages directory - currently empty
- **Status**: Using App Router instead of Pages Router
- **Future**: Can be used for migration or additional page structures

### �📄 Root Level Files

#### `package.json`

- **Core Dependencies**:
    - `next`: React framework
    - `react` & `react-dom`: React library
    - `@chakra-ui/react`: UI library (legacy)
    - `@emotion/react` & `@emotion/styled`: Styling
    - `framer-motion`: Animations
    - `tailwindcss`: CSS framework
    - `class-variance-authority`: Component variants
    - `clsx`: Utility for className
    - `tailwind-merge`: Merge Tailwind classes
    - `@radix-ui/react-slot`: Primitive components

#### `tsconfig.json`

- TypeScript configuration with path mapping
- Modern ES features support
- Strict type checking

#### `next.config.ts`

- Next.js configuration
- Turbopack support

### 🎨 `src/app/` - App Router

#### `layout.tsx`

- **Font Configuration**: Sora font with weights 100-800
- **Metadata**: SEO metadata for application
- **Root Structure**: HTML structure with font classes

```typescript
const sora = Sora({
    variable: "--font-sora",
    subsets: ["latin"],
    weight: ["100", "200", "300", "400", "500", "600", "700", "800"],
});
```

#### `globals.css`

- **CSS Variables**: Design system colors
- **Theme Configuration**: Light/dark mode support
- **Utility Classes**: Custom color classes
- **Tailwind Integration**: Theme configuration

```css
:root {
    --primary-red: #b81f24;
    --primary-blue: #0032ff;
    --primary-light: #e3e6df;
}
```

#### `page.tsx`

- **Home Page**: Component library demo
- **Component Showcase**: Displays all components
- **Layout Examples**: Header, container, cards

## 🧩 Component Library Structure

### `src/components/ui/` - Base UI Components

#### `button.tsx`

- **Variants**: default, red, blue, light, outline, ghost, link
- **Sizes**: default, sm, lg, icon
- **Features**: Forward ref, accessibility support

#### `card.tsx`

- **Components**: Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter
- **Features**: Consistent spacing, semantic HTML

#### `input.tsx`

- **Features**: Form integration, accessibility, styling
- **Types**: Extends HTML input attributes

#### `typography.tsx`

- **Components**: H1, H2, H3, H4, P, Lead, Large, Small, Muted
- **Features**: Consistent font sizes, weights, spacing

#### `badge.tsx`

- **Variants**: default, red, blue, light, outline
- **Features**: Pill shape, color theming

#### `index.ts`

- **Barrel Export**: Re-export all UI components
- **Easy Import**: Single import statement

### `src/components/layout/` - Layout Components

#### `container.tsx`

- **Responsive**: max-width containers with padding
- **Sizes**: sm, md, lg, xl, full
- **Features**: Consistent layout spacing

#### `header.tsx`

- **Sticky Navigation**: Fixed header with backdrop blur
- **Responsive**: Mobile-friendly navigation
- **Features**: Logo, navigation items, CTA buttons

## 🛠️ `src/lib/` - Utilities & Constants

#### `constants.ts`

```typescript
export const COLORS = {
    PRIMARY_RED: "#B81F24",
    PRIMARY_BLUE: "#0032FF",
    PRIMARY_LIGHT: "#E3E6DF",
} as const;

export const FONT_WEIGHTS = {
    THIN: "100",
    EXTRA_LIGHT: "200",
    LIGHT: "300",
    NORMAL: "400",
    MEDIUM: "500",
    SEMI_BOLD: "600",
    BOLD: "700",
    EXTRA_BOLD: "800",
} as const;
```

#### `utils.ts`

- **cn()**: Merge and optimize class names
- **formatCurrency()**: Currency formatting
- **formatDate()**: Date formatting
- **debounce()**: Performance utility
- **generateId()**: Generate unique IDs

## 🎨 Design System

### Colors

- **Primary Red**: `#B81F24` - Used for actions, alerts
- **Primary Blue**: `#0032FF` - Used for links, secondary actions
- **Primary Light**: `#E3E6DF` - Used for backgrounds, subtle elements

### Typography

- **Font Family**: Sora
- **Weights**: 100-800 (Thin to Extra Bold)
- **Responsive**: Auto-scale on mobile

### Spacing

- **Scale**: Consistent spacing system
- **Responsive**: Mobile-first approach

## 🔄 Component Usage Examples

### Import Patterns

```typescript
// Import from UI library
import { Button, Card, H1, P } from "../components/ui";

// Import layout components
import { Container, Header } from "../components/layout";

// Import utilities
import { cn } from "../lib/utils";
import { COLORS } from "../lib/constants";
```

### Component Examples

```typescript
// Button with custom variants
<Button variant="red" size="lg">Click Me</Button>

// Card structure
<Card>
  <CardHeader>
    <CardTitle>Title</CardTitle>
    <CardDescription>Description</CardDescription>
  </CardHeader>
  <CardContent>
    <P>Content here</P>
  </CardContent>
</Card>

// Typography
<H1 className="text-primary-red">Main Title</H1>
<P className="text-primary-blue">Blue text</P>
```

## 🚀 Development Workflow

### 1. Component Development

- Create components in `src/components/ui/`
- Use TypeScript interfaces
- Export from `index.ts`
- Add variants with `class-variance-authority`

### 2. Styling

- Use Tailwind classes
- Custom colors through CSS variables
- Responsive design with mobile-first

### 3. Testing

- Test components in `src/app/page.tsx`
- Verify responsive behavior
- Check accessibility

### 4. Documentation

- Update README.md with new components
- Add examples in documentation
- Maintain component prop types

## 📦 Build & Deployment

### Development

```bash
npm run dev    # Development server
npm run lint   # ESLint checking
```

### Production

```bash
npm run build  # Build for production
npm run start  # Start production server
```

### Build Output

- `.next/` directory contains build artifacts
- Static assets optimized
- Automatic code splitting

## 🔧 Configuration Files

### `eslint.config.mjs`

- TypeScript support
- React hooks rules
- Accessibility checking

### `postcss.config.mjs`

- Tailwind CSS processing
- Autoprefixer integration

### `tailwind.config.js`

- Custom theme configuration
- Plugin setup
- Content paths

## 🎯 Best Practices

### Component Design

- **Consistent Props**: Use consistent patterns
- **Forward Ref**: Support ref forwarding
- **Accessibility**: ARIA labels and semantic HTML
- **TypeScript**: Strict typing for props

### File Organization

- **Barrel Exports**: Use index.ts files
- **Relative Imports**: Avoid absolute paths when possible
- **Feature-based**: Group related components

### Performance

- **Code Splitting**: Dynamic imports for large components
- **Optimization**: Image and font optimization
- **Bundle Size**: Monitor bundle size

## 🔄 Future Enhancements

### Planned Features

- **Form Components**: Advanced form validation
- **Data Display**: Tables, charts, lists
- **Navigation**: Sidebar, breadcrumbs, pagination
- **Feedback**: Toast notifications, modals, tooltips

### Architecture Improvements

- **State Management**: Context API or Zustand
- **API Integration**: React Query or SWR
- **Testing**: Jest and React Testing Library
- **Storybook**: Component documentation

---

**Last Updated**: 25 April 2026  
**Version**: 1.0.0  
**Framework**: Next.js 16.2.4
