# Scenariaid Frontend

A modern, scalable, and maintainable frontend application built with Next.js, TypeScript, and Tailwind CSS. This project features a comprehensive component library with reusable UI components and a consistent design system.

## 🚀 Features

- **Modern Tech Stack**: Next.js 16, TypeScript, Tailwind CSS
- **Component Library**: Reusable UI components with consistent design
- **Custom Design System**: 
  - Sora font family for modern typography
  - Custom color palette: Red (#B81F24), Blue (#0032FF), Light (#E3E6DF)
- **Scalable Architecture**: Well-organized project structure for easy maintenance
- **Developer Experience**: TypeScript, ESLint, and modern development tools

## 🎨 Design System

### Colors
- **Primary Red**: `#B81F24`
- **Primary Blue**: `#0032FF` 
- **Primary Light**: `#E3E6DF`

### Typography
- **Font Family**: Sora (weights 100-800)
- **Responsive**: Optimized for all screen sizes

### Components
- **Buttons**: Multiple variants (default, red, blue, light, outline)
- **Cards**: Flexible card components with headers, content, and footers
- **Typography**: Consistent heading and text components
- **Forms**: Input fields and form elements
- **Layout**: Container and header components

## 📁 Project Structure

```
src/
├── app/                    # Next.js app router
│   ├── globals.css        # Global styles and CSS variables
│   ├── layout.tsx         # Root layout with font configuration
│   └── page.tsx           # Home page
├── components/            # Reusable components
│   ├── ui/               # Base UI components
│   │   ├── button.tsx    # Button component with variants
│   │   ├── card.tsx      # Card components
│   │   ├── input.tsx     # Input field component
│   │   ├── typography.tsx # Typography components
│   │   ├── badge.tsx     # Badge component
│   │   └── index.ts      # Component exports
│   └── layout/           # Layout components
│       ├── container.tsx # Responsive container
│       └── header.tsx    # Header component
└── lib/                  # Utilities and constants
    ├── constants.ts      # Design tokens and constants
    └── utils.ts          # Utility functions
```

## 🛠️ Getting Started

### Prerequisites
- Node.js 18+ 
- npm or yarn

### Installation

1. Clone the repository
2. Install dependencies:
```bash
npm install
```

3. Run the development server:
```bash
npm run dev
```

4. Open [http://localhost:3000](http://localhost:3000) in your browser

## 📦 Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run start` - Start production server
- `npm run lint` - Run ESLint

## 🧩 Using Components

### Import Components

```typescript
// Import individual components
import { Button, Card, H1, P } from '../components/ui';

// Import layout components
import { Container, Header } from '../components/layout';
```

### Button Examples

```typescript
<Button variant="default">Default Button</Button>
<Button variant="red">Red Button</Button>
<Button variant="blue">Blue Button</Button>
<Button variant="light">Light Button</Button>
<Button variant="outline">Outline Button</Button>
```

### Card Examples

```typescript
<Card>
  <CardHeader>
    <CardTitle>Card Title</CardTitle>
    <CardDescription>Card description</CardDescription>
  </CardHeader>
  <CardContent>
    <P>Card content goes here</P>
  </CardContent>
</Card>
```

### Typography

```typescript
<H1>Main Heading</H1>
<H2>Subheading</H2>
<P>Paragraph text</P>
<P className="text-primary-red">Red text</P>
<P className="text-primary-blue">Blue text</P>
```

## 🎯 Customization

### Adding New Colors

Update `src/app/globals.css` and `src/lib/constants.ts`:

```css
:root {
  --new-color: #your-color;
}
```

```typescript
export const COLORS = {
  // ... existing colors
  NEW_COLOR: '#your-color',
} as const;
```

### Creating New Components

1. Create component file in `src/components/ui/`
2. Follow the existing patterns with TypeScript interfaces
3. Export from `src/components/ui/index.ts`

## 🚀 Deployment

### Vercel (Recommended)

[Deploy on Vercel](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme)

### Other Platforms

```bash
npm run build
npm run start
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🔗 Dependencies

- [Next.js](https://nextjs.org/) - React framework
- [TypeScript](https://www.typescriptlang.org/) - Type safety
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS
- [class-variance-authority](https://github.com/joe-bell/cva) - Component variants
- [clsx](https://github.com/lukeed/clsx) - Conditional className utility
- [tailwind-merge](https://github.com/dcastilloc/tailwind-merge) - Merge Tailwind classes
- [@radix-ui/react-slot](https://www.radix-ui.com/) - Primitive components
