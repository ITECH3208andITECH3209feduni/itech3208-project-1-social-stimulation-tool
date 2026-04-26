import { createSystem, defineConfig, defaultConfig } from "@chakra-ui/react";
import { colors } from "./foundations/colors";

const config = defineConfig({
    theme: {
        tokens: {
            colors,
            fonts: {
                body: { value: "'Sora', system-ui, sans-serif" },
                heading: { value: "'Sora', system-ui, sans-serif" },
            },
        },
        semanticTokens: {
            colors: {
                bg: {
                    value: colors.dark[900],
                },
                surface: {
                    value: colors.gray[100],
                },
                border: {
                    value: colors.brand[200],
                },
                text: {
                    value: colors, // main text or text color
                },
                textMuted: {
                    value: colors.gray[500],
                },
                primary: {
                    value: colors.brand[500], // red
                },
                secondary: {
                    value: colors.navy[500], //navy
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
