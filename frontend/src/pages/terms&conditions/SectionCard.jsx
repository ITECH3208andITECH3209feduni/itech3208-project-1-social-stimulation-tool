import { Text, Box, Flex, Heading, VStack, Tag, List } from "@chakra-ui/react";

import React from "react";
import HighlightText from "./HighlightText";

function SectionCard({ heading, title, terms, subTerms, highlights, color }) {
    return (
        <Box bg="white" border="1px solid" borderColor="white">
            <Flex gap={"3"} alignItems={"center"}>
                <Box display={"flex"} alignItems={"center"} justifyContent={"center"}>
                    <Tag.Root
                        unstyled
                        bg={"white"}
                        color={"brand.500"}
                        fontWeight={"bold"}
                        borderWidth="0"
                        borderColor="white"
                        borderRadius={"full"}
                    >
                        <Tag.Label>{heading}</Tag.Label>
                    </Tag.Root>
                </Box>
                <Heading
                    margin={"0"}
                    display={"flex"}
                    alignItems={"center"}
                    fontWeight="bold"
                    color="black"
                    fontFamily="Sora"
                >
                    {title}
                </Heading>
            </Flex>

            <List.Root textAlign={"left"} mx={4}>
                {terms &&
                    terms.map((term, index) => {
                        const highlight = highlights.find((hl) => term.includes(hl.text));
                        return (
                            <List.Item key={index}>
                                {highlight ? (
                                    <HighlightText text={term} highlights={highlights} />
                                ) : (
                                    <Text fontSize="14px" color="gray.700" lineHeight="1.7">
                                        {term}
                                    </Text>
                                )}
                            </List.Item>
                        );
                    })}

                {subTerms && (
                    <List.Root textAlign={"left"} mx={4}>
                        {subTerms.map((subTerm, index) => {
                            const highlight = highlights.find((hl) => subTerm.includes(hl.text));
                            return (
                                <List.Item key={index}>
                                    {highlight ? (
                                        <HighlightText text={subTerm} highlights={highlights} />
                                    ) : (
                                        <Text fontSize="14px" color="gray.700" lineHeight="1.7">
                                            {subTerm}
                                        </Text>
                                    )}
                                </List.Item>
                            );
                        })}
                    </List.Root>
                )}
            </List.Root>
        </Box>
    );
}

export default SectionCard;
