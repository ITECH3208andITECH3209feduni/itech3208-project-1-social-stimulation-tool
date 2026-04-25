import { defineConfig, globalIgnores } from "eslint/config";
import nextVitals from "eslint-config-next/core-web-vitals";

const eslintConfig = defineConfig([
    ...nextVitals,
    {
        rules: {
            // Code Style & Formatting
            "prefer-const": "error",
            "no-var": "error",
            "no-console": "warn", // Allow console logs but warn
            "no-debugger": "error",
            "no-alert": "warn",
            "no-unused-vars": "off", // Using TypeScript version

            // Import/Export Rules
            "sort-imports": [
                "error",
                {
                    groups: [
                        ["builtin", "external"],
                        ["internal", "parent", "sibling", "index"],
                    ],
                    "newlines-between": "always",
                    alphabetize: {
                        order: "asc",
                        caseInsensitive: true,
                    },
                },
            ],

            // Spacing & Formatting
            indent: ["error", 4, { SwitchCase: 1 }],
            quotes: ["error", "single", { avoidEscape: true }],
            semi: ["error", "always"],
            "comma-dangle": ["error", "always-multiline"],
            "eol-last": ["error", "always"],
            "no-trailing-spaces": "error",

            // Object & Array Rules
            "object-curly-spacing": ["error", "always"],
            "array-bracket-spacing": ["error", "never"],
            "computed-property-spacing": ["error", "never"],
            "key-spacing": ["error", { beforeColon: false, afterColon: true }],

            // Function Rules
            "func-call-spacing": ["error", "never"],
            "space-before-function-paren": [
                "error",
                {
                    anonymous: "always",
                    named: "never",
                    asyncArrow: "always",
                },
            ],

            // Naming Conventions
            camelcase: ["error", { properties: "never" }],

            // Best Practices
            eqeqeq: ["error", "always"],
            curly: ["error", "all"],
            "brace-style": ["error", "1tbs"],
            "no-eval": "error",
            "no-implied-eval": "error",
            "no-new-func": "error",
            "no-script-url": "error",

            // TypeScript & React Rules
            "@typescript-eslint/no-unused-vars": [
                "error",
                { argsIgnorePattern: "^_" },
            ],
            "@typescript-eslint/no-explicit-any": "warn",
            "@typescript-eslint/prefer-const": "error",
            "@typescript-eslint/no-non-null-assertion": "warn",

            // React Rules
            "react/prop-types": "off", // Using TypeScript for prop validation
            "react/react-in-jsx-scope": "off", // Not needed in React 17+
            "react/jsx-uses-react": "off", // Not needed in React 17+
            "react/jsx-key": "error",
            "react/jsx-no-duplicate-props": "error",
            "react/jsx-no-undef": "error",
            "react/jsx-uses-vars": "error",
            "react-hooks/rules-of-hooks": "error",
            "react-hooks/exhaustive-deps": "warn",

            // Accessibility
            "jsx-a11y/alt-text": "error",
            "jsx-a11y/anchor-has-content": "error",
            "jsx-a11y/anchor-is-valid": "error",
            "jsx-a11y/aria-props": "error",
            "jsx-a11y/aria-proptypes": "error",
            "jsx-a11y/aria-unsupported-elements": "error",
            "jsx-a11y/click-events-have-key-events": "warn",
            "jsx-a11y/heading-has-content": "error",
            "jsx-a11y/html-has-lang": "error",
            "jsx-a11y/img-redundant-alt": "error",
            "jsx-a11y/interactive-supports-focus": "error",
            "jsx-a11y/label-has-associated-control": "error",
            "jsx-a11y/media-has-caption": "warn",
            "jsx-a11y/mouse-events-have-key-events": "warn",
            "jsx-a11y/no-access-key": "error",
            "jsx-a11y/no-autofocus": "warn",
            "jsx-a11y/no-distracting-elements": "error",
            "jsx-a11y/no-interactive-element-to-noninteractive-role": "error",
            "jsx-a11y/no-noninteractive-element-interactions": "error",
            "jsx-a11y/no-noninteractive-element-to-interactive-role": "error",
            "jsx-a11y/no-redundant-roles": "error",
            "jsx-a11y/no-static-element-interactions": "error",
            "jsx-a11y/role-has-required-aria-props": "error",
            "jsx-a11y/role-supports-aria-props": "error",
            "jsx-a11y/scope": "error",
            "jsx-a11y/tabindex-no-positive": "error",
        },
    },
    // Override default ignores of eslint-config-next.
    globalIgnores([
        // Default ignores of eslint-config-next:
        ".next/**",
        "out/**",
        "build/**",
        "next-env.d.ts",
    ]),
]);

export default eslintConfig;
