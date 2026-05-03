import { Box, HStack, Icon, Text } from "@chakra-ui/react";
import React from "react";

function CardPerformance({ title, records, icon }) {
    return (
        <Box
            flex={1}
            bg="dark.800"
            borderRadius="xl"
            p={5}
            minH="90px"
            boxShadow="sm"
            _hover={{
                boxShadow: "md",
                borderColor: "blue.200",
                transform: "translateY(-2px)",
            }}
            _active={{
                transform: "translateY(0px)",
                boxShadow: "sm",
            }}
            transition="all 0.2s ease"
        >
            <Text fontSize="sm" color="brand.500" fontWeight="medium" mb={2}>
                {title}
            </Text>
            <HStack gap={2} align="center">
                <Icon fontSize="22px" color="brand.500">
                    {icon}
                </Icon>
                <Text fontSize="2xl" fontWeight="bold" color="brand.500">
                    {records.toLocaleString()}
                </Text>
            </HStack>
        </Box>
    );
}

export default CardPerformance;
