import { Text, Box, Flex, Heading, VStack, Tag, List } from "@chakra-ui/react";

import React from "react";

function SectionCard({ heading, title, items, color }) {
    return (
        <Box
            bg="white"
            border="1px solid"
            borderColor="gray.200"
            borderRadius="12px"
            spaceY={"4"}
            // padding={"8"}
        >
            <Flex gap={"3"} alignItems={"center"}>
                <Box display={"flex"} alignItems={"center"} justifyContent={"center"}>
                    <Tag.Root
                        bg={"skyblue.200"}
                        color={"navy.500"}
                        fontWeight={"bold"}
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

            <VStack align="start" gap="2">
                {items.map((item, index) => (
                    <Flex key={index} gap="2" align="start">
                        <Box w="6px" h="6px" bg="gray.500" borderRadius="full" mt="6px" />
                        <Text fontSize="14px" color="gray.700" lineHeight="1.7" textAlign={"left"}>
                            {item}
                        </Text>
                    </Flex>
                ))}
            </VStack>
        </Box>
    );
}

export default SectionCard;
