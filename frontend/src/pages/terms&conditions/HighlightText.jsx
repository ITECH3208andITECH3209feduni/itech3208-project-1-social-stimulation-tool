import React from "react";
import { Text, Link } from "@chakra-ui/react";

function HighlightText({
    text,
    highlights = [],
    color = "brand.500",
    fontWeight = "bold",
    fontSize = "14px",
    lineHeight = "1.7",
    ...props
}) {
    if (!highlights.length) {
        return (
            <Text fontSize={fontSize} color="gray.700" lineHeight={lineHeight} {...props}>
                {text}
            </Text>
        );
    }

    // Escape special regex characters
    const escapeRegex = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

    // Longest text first to avoid shorter highlights matching first
    const sortedHighlights = [...highlights].sort((a, b) => b.text.length - a.text.length);

    const regex = new RegExp(
        `(${sortedHighlights.map((hl) => escapeRegex(hl.text)).join("|")})`,
        "gi",
    );

    const parts = text.split(regex);

    const getHighlight = (value) =>
        sortedHighlights.find((hl) => hl.text.toLowerCase() === value.toLowerCase());

    return (
        <Text fontSize={fontSize} color="gray.700" lineHeight={lineHeight} {...props}>
            {parts.map((part, index) => {
                const highlight = getHighlight(part);

                if (!highlight) {
                    return <React.Fragment key={index}>{part}</React.Fragment>;
                }

                const highlightColor = highlight.color || color;
                const highlightWeight = highlight.fontWeight || fontWeight;

                if (highlight.url) {
                    return (
                        <Link
                            key={index}
                            href={highlight.url}
                            color={highlightColor}
                            fontWeight={highlightWeight}
                            textDecoration="none"
                            _hover={{
                                textDecoration: "underline",
                            }}
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            {part}
                        </Link>
                    );
                }

                return (
                    <Text
                        key={index}
                        as="span"
                        color={highlightColor}
                        fontWeight={highlightWeight}
                        onClick={highlight.action}
                        _hover={{
                            textDecoration: "underline",
                            cursor: "pointer"
                        }}
                    >
                        {part}
                    </Text>
                );
            })}
        </Text>
    );
}

export default HighlightText;
