import { createSystem, defineConfig, defaultConfig } from "@chakra-ui/react";
import { colors } from "./foundations/colors";

const config = defineConfig({
    theme: {
        tokens: {
            colors,
            fonts: {
                body: { value: "'Open Sans', system-ui, sans-serif" },
                rubik: { value: "'Rubik Storm', system-ui, sans-serif" },
                roboto: { value: "Roboto, system-ui, sans-serif" },
            },
        },
        semanticTokens: {
            colors: {
                bg: {
                    value: colors.dark[900],
                },
                surface: {
                    value: colors.brand[100],
                },
                border: {
                    value: colors.brand[200],
                },
                brand: {
                    value: colors.brand[500],
                },
            },
        },
        colorMode: {
            defaultColorMode: "dark",
            disableTransition: true,
        },
    },
});

const system = createSystem(defaultConfig, config);

export default system;