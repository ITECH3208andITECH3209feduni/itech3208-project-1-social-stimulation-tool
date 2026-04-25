import * as React from "react";
import { Container } from "./container";
import { Button } from "../ui/button";
import { H1 } from "../ui/typography";
import { cn } from "../../lib/utils";

interface HeaderProps {
  className?: string;
  title?: string;
  showNavigation?: boolean;
}

const Header = React.forwardRef<HTMLElement, HeaderProps>(
  ({ className, title, showNavigation = true, ...props }, ref) => {
    return (
      <header
        ref={ref}
        className={cn(
          "sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-backdrop-filter:bg-background/60",
          className
        )}
        {...props}
      >
        <Container>
          <div className="flex h-16 items-center justify-between">
            <div className="flex items-center space-x-4">
              <H1 className="text-xl">{title || "Scenariaid"}</H1>
            </div>
            
            {showNavigation && (
              <nav className="flex items-center space-x-4">
                <Button variant="ghost">Home</Button>
                <Button variant="ghost">About</Button>
                <Button variant="ghost">Services</Button>
                <Button variant="red">Get Started</Button>
              </nav>
            )}
          </div>
        </Container>
      </header>
    );
  }
);

Header.displayName = "Header";

export { Header };
