import * as React from "react";
import { cn } from "../../lib/utils";

export interface TypographyProps {
  className?: string;
  children: React.ReactNode;
}

export const H1 = React.forwardRef<HTMLHeadingElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <h1
      ref={ref}
      className={cn(
        "scroll-m-20 text-4xl font-extrabold tracking-tight lg:text-5xl",
        className
      )}
      {...props}
    >
      {children}
    </h1>
  )
);
H1.displayName = "H1";

export const H2 = React.forwardRef<HTMLHeadingElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <h2
      ref={ref}
      className={cn(
        "scroll-m-20 border-b pb-2 text-3xl font-semibold tracking-tight first:mt-0",
        className
      )}
      {...props}
    >
      {children}
    </h2>
  )
);
H2.displayName = "H2";

export const H3 = React.forwardRef<HTMLHeadingElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <h3
      ref={ref}
      className={cn(
        "scroll-m-20 text-2xl font-semibold tracking-tight",
        className
      )}
      {...props}
    >
      {children}
    </h3>
  )
);
H3.displayName = "H3";

export const H4 = React.forwardRef<HTMLHeadingElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <h4
      ref={ref}
      className={cn(
        "scroll-m-20 text-xl font-semibold tracking-tight",
        className
      )}
      {...props}
    >
      {children}
    </h4>
  )
);
H4.displayName = "H4";

export const P = React.forwardRef<HTMLParagraphElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <p
      ref={ref}
      className={cn("leading-7 not-first:mt-6", className)}
      {...props}
    >
      {children}
    </p>
  )
);
P.displayName = "P";

export const Lead = React.forwardRef<HTMLParagraphElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <p
      ref={ref}
      className={cn("text-xl text-muted-foreground", className)}
      {...props}
    >
      {children}
    </p>
  )
);
Lead.displayName = "Lead";

export const Large = React.forwardRef<HTMLDivElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <div
      ref={ref}
      className={cn("text-lg font-semibold", className)}
      {...props}
    >
      {children}
    </div>
  )
);
Large.displayName = "Large";

export const Small = React.forwardRef<HTMLDivElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <div
      ref={ref}
      className={cn("text-sm font-medium leading-none", className)}
      {...props}
    >
      {children}
    </div>
  )
);
Small.displayName = "Small";

export const Muted = React.forwardRef<HTMLParagraphElement, TypographyProps>(
  ({ className, children, ...props }, ref) => (
    <p
      ref={ref}
      className={cn("text-sm text-muted-foreground", className)}
      {...props}
    >
      {children}
    </p>
  )
);
Muted.displayName = "Muted";
