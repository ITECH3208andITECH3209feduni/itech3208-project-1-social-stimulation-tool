# 📋 Coding Standards & Conventions

This document outlines the coding standards and conventions for the Scenariaid frontend project to ensure consistency across the team.

## 🎯 ESLint Configuration

Our ESLint configuration enforces the following rules automatically. All team members should follow these standards.

### 🔧 TypeScript & React Rules

#### TypeScript

- **No unused variables**: Error (except variables starting with `_`)
- **Avoid `any` type**: Warning - prefer specific types
- **Prefer const**: Error - use `const` instead of `let` when possible
- **No non-null assertions**: Warning - avoid `!` operator

#### React

- **No prop-types**: Disabled - using TypeScript for validation
- **React imports**: Disabled for React 17+ (not needed)
- **JSX key**: Required for lists
- **Hooks rules**: Enforce proper hook usage
- **Exhaustive deps**: Warning for useEffect dependencies

### 📝 Code Style & Formatting

#### Basic Rules

- **Prefer const**: Use `const` over `let` when possible
- **No var**: Never use `var`
- **Console logs**: Allowed but with warning
- **No debugger**: Error - debugger statements not allowed
- **No alert**: Warning - avoid alert() in production

#### Import/Export Rules

- **Sorted imports**: Auto-sorted by groups:
    1. Built-in and external libraries
    2. Internal modules (parent, sibling, index)
- **Alphabetical order**: Case-insensitive within groups
- **Newlines between groups**: Required

#### Spacing & Formatting

- **Indentation**: 4 spaces (1 space for switch cases)
- **Quotes**: Single quotes, avoid escape when possible
- **Semicolons**: Always required
- **Comma dangle**: Required for multiline
- **End of line**: Must end with newline
- **No trailing spaces**: Error

#### Objects & Arrays

- **Object spacing**: `{ key: value }` (spaces around braces)
- **Array spacing**: `[1, 2, 3]` (no spaces around brackets)
- **Computed properties**: `obj[key]` (no spaces around brackets)
- **Key spacing**: `key: value` (space after colon, not before)

#### Functions

- **Function calls**: `func()` (no spaces before parentheses)
- **Function spacing**:
    - Anonymous functions: `function () {}` (space before parentheses)
    - Named functions: `function name() {}` (no space before parentheses)
    - Arrow functions: `() => {}` (space before parentheses)

## 🏷️ Naming Conventions

### General Rules

- **camelCase**: For variables and functions
- **PascalCase**: For components and classes
- **UPPER_CASE**: For constants
- **kebab-case**: For file names and CSS classes

### Examples

```typescript
// Variables and functions
const userName = "john";
const getUserData = () => {};

// Components and classes
const ButtonComponent = () => {};
class UserService {}

// Constants
const API_BASE_URL = "https://api.example.com";

// File names
button - component.tsx;
user - service.ts;
```

## 📁 File Organization

### Directory Structure

```
src/
├── app/           # App Router pages
├── components/     # Reusable components
│   ├── ui/        # Base UI components
│   └── layout/    # Layout components
└── lib/           # Utilities and constants
```

### File Naming

- **Components**: PascalCase (e.g., `Button.tsx`, `UserProfile.tsx`)
- **Utilities**: camelCase (e.g., `formatDate.ts`, `apiClient.ts`)
- **Types**: camelCase with `.types.ts` suffix (e.g., `user.types.ts`)
- **Constants**: camelCase (e.g., `colors.ts`, `endpoints.ts`)

## 🧩 Component Standards

### Component Structure

```typescript
import React from 'react';
import { cn } from '../../lib/utils';

interface ComponentProps {
  // Props interface here
}

const Component = React.forwardRef<HTMLDivElement, ComponentProps>(
  ({ className, ...props }, ref) => {
    return (
      <div
        ref={ref}
        className={cn('default-classes', className)}
        {...props}
      >
        {/* Component content */}
      </div>
    );
  }
);

Component.displayName = 'Component';

export { Component };
```

### Component Rules

- **Forward ref**: Required for all components
- **Display name**: Must set `displayName`
- **Props interface**: Always use TypeScript interfaces
- **Default props**: Use default parameters instead
- **ClassName prop**: Always support with `cn()` utility

## 🎨 Styling Conventions

### Tailwind CSS

- **Utility classes**: Use Tailwind utilities
- **Custom colors**: Use CSS variables from design system
- **Responsive**: Mobile-first approach
- **Consistent spacing**: Use defined spacing scale

### CSS Variables

```css
/* Use these instead of hardcoded values */
text-primary-red    /* #B81F24 */
text-primary-blue    /* #0032FF */
text-primary-light   /* #E3E6DF */
```

## 🔍 Accessibility Standards

### Required Rules

- **Alt text**: All images must have alt attributes
- **Semantic HTML**: Use proper HTML5 elements
- **ARIA labels**: Use ARIA attributes when needed
- **Keyboard navigation**: Ensure keyboard accessibility
- **Focus management**: Proper focus handling

### Examples

```typescript
// Good
<button
  onClick={handleClick}
  onKeyDown={handleKeyDown}
  aria-label="Close dialog"
>
  Close
</button>

// Bad
<div onClick={handleClick}>Close</div>
```

## 📝 Comment Standards

### When to Comment

- **Complex logic**: Explain non-obvious code
- **Business rules**: Document business requirements
- **TODO/FIXME**: Mark incomplete code
- **Public APIs**: Document function purpose and parameters

### Comment Style

```typescript
/**
 * Calculates the total price including tax
 * @param basePrice - The base price before tax
 * @param taxRate - Tax rate as decimal (0.1 for 10%)
 * @returns Total price including tax
 */
const calculateTotalPrice = (basePrice: number, taxRate: number): number => {
    return basePrice * (1 + taxRate);
};
```

## 🚀 Git Conventions

### Commit Messages

- **Format**: `type(scope): description`
- **Types**: feat, fix, docs, style, refactor, test, chore
- **Examples**:
    - `feat(button): add red variant`
    - `fix(typography): resolve font loading issue`
    - `docs(readme): update installation instructions`

### Branch Naming

- **Feature**: `feature/description`
- **Bugfix**: `bugfix/description`
- **Hotfix**: `hotfix/description`

## ⚡ Performance Guidelines

### Code Optimization

- **Avoid unnecessary re-renders**: Use React.memo when appropriate
- **Optimize imports**: Import only what's needed
- **Lazy loading**: Use dynamic imports for large components
- **Image optimization**: Use Next.js Image component

### Bundle Size

- **Tree shaking**: Ensure unused code is eliminated
- **Code splitting**: Split code by routes
- **Asset optimization**: Optimize images and fonts

## 🧪 Testing Standards

### Unit Tests

- **Coverage**: Aim for 80%+ coverage
- **Naming**: `*.test.ts` or `*.spec.ts` files
- **Structure**: Arrange-Act-Assert pattern
- **Mocks**: Use jest.mock for dependencies

### Integration Tests

- **User flows**: Test complete user journeys
- **API integration**: Test API calls
- **Component integration**: Test component interactions

## 🔧 Development Workflow

### Before Committing

1. **Run linter**: `npm run lint`
2. **Run tests**: `npm test`
3. **Build check**: `npm run build`
4. **Code review**: Peer review required

### Code Review Checklist

- [ ] Follows naming conventions
- [ ] Proper TypeScript types
- [ ] Accessibility compliant
- [ ] Performance considered
- [ ] Tests included
- [ ] Documentation updated

---

**Last Updated**: 25 April 2026  
**Version**: 1.0.0  
**Framework**: Next.js 16.2.4
