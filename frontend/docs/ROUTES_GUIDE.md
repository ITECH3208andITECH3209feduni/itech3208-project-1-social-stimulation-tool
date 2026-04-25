# 🛣️ Routes Guide for Team Members

This document provides a comprehensive guide for understanding and working with the Scenariaid frontend application routes and page structure.

## 🌐 Application Routes

### Primary Routes
- **`/`** - Home page (`src/app/page.tsx`)
- **`/about`** - About us page (`src/app/about/page.tsx`)
- **`/tutorial`** - Tutorial center (`src/app/tutorial/page.tsx`)
- **`/contact`** - Contact page (`src/app/contact/page.tsx`)
- **`/terms`** - Terms & conditions (`src/app/terms/page.tsx`)
- **`/account`** - Account section (`src/app/account/page.tsx`)

### Account Sub-routes
- **`/account/settings`** - Account settings (`src/app/account/settings/page.tsx`)
- **`/account/billing`** - Billing & subscription (`src/app/account/billing/page.tsx`)
- **`/account/security`** - Security settings (`src/app/account/security/page.tsx`)

## 📝 Development Guidelines for Team Members

### 🏗️ File Structure Standards

#### When Adding New Pages
1. **Create Route Directory**: 
   ```bash
   mkdir src/app/new-page-name
   ```

2. **Create Page Component**:
   ```typescript
   // src/app/new-page-name/page.tsx
   import { Metadata } from 'next';
   import { Container, Header } from '../../components/layout';
   import { H1, P, Card } from '../../components/ui';

   export const metadata: Metadata = {
       title: 'Page Title - Scenariaid',
       description: 'Page description for SEO',
   };

   export default function NewPage() {
       return (
           <div className="min-h-screen bg-background">
               <Header title="Page Title" />
               <main className="py-20">
                   <Container>
                       {/* Page content here */}
                   </Container>
               </main>
           </div>
       );
   }
   ```

3. **Update Navigation**: Add link to Header component navigation

#### When Adding Account Sub-pages
1. **Create Subdirectory**:
   ```bash
   mkdir src/app/account/new-subpage
   ```

2. **Follow Same Pattern**: Use consistent structure and imports

### 🎨 Component Usage Standards

#### Import Patterns
```typescript
// Always use relative imports from app directory
import { Container, Header } from '../../components/layout';
import { H1, H2, H3, P, Card } from '../../components/ui';
```

#### Component Structure
```typescript
// Follow this consistent structure for all pages
export default function PageName() {
    return (
        <div className="min-h-screen bg-background">
            <Header title="Page Title" />
            <main className="py-20">
                <Container>
                    {/* Page content */}
                </Container>
            </main>
        </div>
    );
}
```

### 🎨 Design System Usage

#### Color Classes
```typescript
// Use semantic color classes
className="text-primary-red"    // #B81F24
className="text-primary-blue"   // #0032FF
className="text-primary-light"  // #E3E6DF
className="bg-primary-red"     // Red background
className="bg-primary-blue"    // Blue background
className="bg-primary-light"   // Light background
```

#### Typography Components
```typescript
// Use appropriate typography components
<H1>Main Title</H1>
<H2>Section Title</H2>
<H3>Subsection Title</H3>
<P>Paragraph text</P>
```

### 🔧 Development Workflow

#### 1. Setup Development Environment
```bash
# Install dependencies
npm install

# Start development server
npm run dev
```

#### 2. Code Standards
- **4-space indentation** (enforced by ESLint)
- **Single quotes** for strings
- **Semicolons** at end of statements
- **TypeScript interfaces** for all props
- **Forward refs** for components

#### 3. Testing Routes
```bash
# Access routes during development
http://localhost:3000/about
http://localhost:3000/tutorial
http://localhost:3000/contact
http://localhost:3000/terms
http://localhost:3000/account
http://localhost:3000/account/settings
http://localhost:3000/account/billing
http://localhost:3000/account/security
```

### 📱 Page Content Guidelines

#### Home Page (`/`)
- **Purpose**: Landing page and application overview
- **Components**: Hero section, feature cards, CTA buttons
- **Key Elements**: Navigation, main heading, feature highlights

#### About Page (`/about`)
- **Purpose**: Company information and team introduction
- **Components**: Mission statement, values, team structure
- **Key Elements**: Company story, team roles, contact CTA

#### Tutorial Page (`/tutorial`)
- **Purpose**: Learning resources and getting started guide
- **Components**: Step-by-step guides, code examples, best practices
- **Key Elements**: Tutorial navigation, setup instructions, advanced topics

#### Contact Page (`/contact`)
- **Purpose**: User communication and support
- **Components**: Contact form, contact information, response times
- **Key Elements**: Form validation, contact details, support channels

#### Terms Page (`/terms`)
- **Purpose**: Legal information and user agreements
- **Components**: Terms sections, legal clauses, contact information
- **Key Elements**: Table of contents, legal sections, last updated date

#### Account Section (`/account`)
- **Purpose**: User account management and personalization
- **Components**: Profile management, settings, subscription info
- **Key Elements**: User dashboard, account navigation, settings forms

##### Account Settings (`/account/settings`)
- **Purpose**: User preferences and configuration
- **Components**: Language settings, timezone, notifications, theme
- **Key Elements**: Form controls, preference toggles, save functionality

##### Account Billing (`/account/billing`)
- **Purpose**: Subscription management and payment processing
- **Components**: Current plan, billing history, payment methods
- **Key Elements**: Plan details, invoice list, upgrade options

##### Account Security (`/account/security`)
- **Purpose**: Authentication and security settings
- **Components**: Password management, 2FA setup, session management
- **Key Elements**: Security forms, active sessions, device management

### 🔄 Navigation Structure

#### Header Component Navigation
```typescript
// Header navigation includes all main routes
const navigationItems = [
    { name: 'Home', href: '/' },
    { name: 'About', href: '/about' },
    { name: 'Tutorial', href: '/tutorial' },
    { name: 'Contact', href: '/contact' },
    { name: 'Terms', href: '/terms' },
    { name: 'Account', href: '/account' },
];
```

### 📝 Commenting Standards

#### File Header Comments
```typescript
/**
 * Page Component - [Page Name]
 * 
 * Purpose: [Brief description of page purpose]
 * 
 * @author [Author name]
 * @since [Date or version]
 * @last-modified [Date of last modification]
 */
```

#### Function Comments
```typescript
export default function PageName() {
    return (
        <div className="min-h-screen bg-background">
            {/* Main container - root layout element */}
            <Header title="Page Title" />
            
            {/* Main content area - semantic HTML5 main tag */}
            <main className="py-20">
                <Container>
                    {/* Hero section - main page introduction */}
                    <div className="text-center mb-16">
                        {/* Page heading with semantic H1 */}
                        <H1 className="mb-4 text-primary-red">Page Title</H1>
                        
                        {/* Page description paragraph */}
                        <P className="text-xl text-muted-foreground max-w-3xl mx-auto">
                            Page description text with clear, concise content.
                        </P>
                    </div>

                    {/* Content sections - use semantic HTML5 elements */}
                    <section className="mb-12">
                        {/* Section heading with semantic H2 */}
                        <H2 className="mb-4">Section Title</H2>
                        
                        {/* Section content in cards for consistent styling */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Card Title</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <P>Card content with clear information.</P>
                            </CardContent>
                        </Card>
                    </section>
                </Container>
            </main>
        </div>
    );
}
```

### 🎯 Best Practices

#### Accessibility
- **Semantic HTML5**: Use proper elements (`<main>`, `<section>`, `<header>`)
- **ARIA Labels**: Include proper ARIA attributes
- **Keyboard Navigation**: Ensure all interactive elements are keyboard accessible
- **Color Contrast**: Use approved color combinations for accessibility

#### Performance
- **Image Optimization**: Use Next.js Image component
- **Code Splitting**: Implement dynamic imports for large components
- **Bundle Analysis**: Monitor bundle size and optimize as needed

#### SEO
- **Metadata**: Always include title and description for each page
- **Semantic Headings**: Use proper heading hierarchy (H1 → H2 → H3)
- **URL Structure**: Clean, readable URLs for all routes

### 🚀 Deployment Considerations

#### Route Structure
- All routes are server-side rendered by default
- Static generation available for optimal performance
- Client-side navigation works seamlessly with Next.js App Router

#### Build Process
```bash
# Build for production
npm run build

# Check build output
npm run start
```

---

**Last Updated**: 25 April 2026  
**Version**: 1.0.0  
**Framework**: Next.js 16.2.4  
**Maintainer**: Development Team
