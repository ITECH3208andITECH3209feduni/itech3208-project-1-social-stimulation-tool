import * as React from "react";
import { Container } from "./container";
import { Button, Logo } from "../ui";
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
          "sticky top-0 z-50 w-full border-b bg-white pt-[45px]",
          className
        )}
        {...props}
      >
 <div className="flex h-20 items-center justify-between px-[100px] max-w-[1240px] mx-auto w-full">
            <Logo/>
            {showNavigation && (
              <nav className="flex items-center space-x-4">
                <Button variant="ghost">Home</Button>
                <Button variant="ghost">About</Button>
                <Button variant="ghost">Tutorial</Button>
                <Button variant="ghost">Contact</Button>
                <Button variant="ghost">Terms & Conditions</Button>
                <Button variant="ghost">Account</Button>
                <div className="w-9 h-9 rounded-full bg-primary-red flex items-center justify-center cursor-pointer">
              <span className="text-white text-xs font-semibold">AP</span>
                </div>
              </nav>
            )}
          </div>
      </header>
    );
  }
);

Header.displayName = "Header";

export { Header };
