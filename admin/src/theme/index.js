import { createSystem, defineConfig, defaultConfig } from "@chakra-ui/react";
import { colors } from "./foundations/color";

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
                    value: colors.whiteAlpha[500],
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

        styles: {
            global: {
                body: {
                    fontFamily: "body",
                    color: "text",
                    bg: "bg",
                },
            },
        },

        colorMode: {
            defaultColorMode: "dark",
            disableTransition: true,
        },
    },

    globalCss: {
        "html": {
            // font-family: "Sora", sans-serif;
            fontFamily: "'Sora', system-ui, sans-serif",
        },
        "body" : {
            color: colors.whiteAlpha[500]
        }
    },
});

const system = createSystem(defaultConfig, config);

export default system;
