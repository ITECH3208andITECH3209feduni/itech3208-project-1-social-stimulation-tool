import { Box, Button, Heading, Stack, Text } from "@chakra-ui/react";
import { Link } from "react-router-dom";

function NoFeedback() {
    return (
        <Box
            width="100%"
            py={{ base: 12, md: 16 }}
            px={6}
            borderRadius="2xl"
            background="gray.50"
            textAlign="center"
        >
            <Stack align="center" spacing={4} maxW="600px" mx="auto">
                <Box
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    width="64px"
                    height="64px"
                    borderRadius="full"
                    background="brand.50"
                    fontSize="28px"
                >
                    💬
                </Box>

                <Heading
                    fontFamily="Sora"
                    fontSize={{ base: "22px", md: "26px" }}
                    color="brand.500"
                >
                    No feedback yet
                </Heading>

                <Text
                    fontFamily="Sora"
                    color="gray.600"
                    fontSize={{ base: "sm", md: "md" }}
                    lineHeight="1.7"
                >
                    Be one of the first users to share your experience with Scenari-Aid and help us
                    improve the community.
                </Text>

                <Button
                    as={Link}
                    to="/account/send-feedback"
                    colorScheme="brand"
                    borderRadius="full"
                    px={7}
                    mt={2}
                    fontFamily="Sora"
                >
                    Share Your Feedback
                </Button>
            </Stack>
        </Box>
    );
}

export default NoFeedback;
