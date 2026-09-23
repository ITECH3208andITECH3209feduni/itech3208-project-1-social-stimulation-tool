import { Box, Heading, Text } from "@chakra-ui/react";

function Crowdfunding() {
    return (
        <Box py={10} px={{ base: 6, md: 10 }} bg="gray.800" color="white" borderRadius="xl">
            <Heading fontFamily="Sora" fontSize={{ base: "2xl", md: "3xl" }} mb={4}>
                Crowdfunding Success
            </Heading>

            <Text fontSize={{ base: "md", md: "lg" }} lineHeight="1.8" color="whiteAlpha.800">
                Thank you to our amazing community. With your support, we successfully reached our
                crowdfunding goals. Your contributions make it possible for us to keep Scenari-Aid a
                free and growing resource for everyone.
            </Text>
        </Box>
    );
}

export default Crowdfunding;
