import { landingPage, star2, testImg } from "@/assets";
import { Box, Button, Flex, Heading, Image, Stack, Text } from "@chakra-ui/react";
import { Link } from "react-router-dom";

function Banner() {
    return (
        <Box width="100%" mt={{ base: 10, md: 16, lg: 20 }}>
            <Flex
                direction={{ base: "column", lg: "row" }}
                align="center"
                gap={{ base: 8, lg: 10, xl: 12 }}
            >
                {/* Content */}
                <Stack flex={1.1} spacing={6} maxW="680px">
                    <Text
                        textAlign={"justify"}
                        fontFamily="Sora"
                        fontSize="sm"
                        fontWeight="600"
                        letterSpacing="0.15em"
                        color="brand.500"
                    >
                        PRACTICE • CONNECT • GROW
                    </Text>
                    <Heading
                        textAlign={"justify"}
                        fontFamily="Sora"
                        color="brand.500"
                        fontSize={{ base: "42px", md: "52px", lg: "58px", xl: "64px" }}
                        lineHeight="1.08"
                        letterSpacing="-0.02em"
                    >
                        Build confidence <br /> for real-life <br /> conversations.
                    </Heading>
                    <Text
                        textAlign={"justify"}
                        fontFamily="Sora"
                        fontSize={{ base: "md", md: "lg" }}
                        lineHeight="1.8"
                        color="gray.600"
                        maxW="600px"
                    >
                        Improve your social, interview and speech skills by practicing real life
                        interactions with the free Scenari-Aid Simulated Scenario System.
                    </Text>
                    {/* CTA */}
                    <Flex gap={4} pt={2} flexWrap="wrap">
                        <Button
                            as={Link}
                            to="/account/register"
                            size="lg"
                            colorScheme="brand"
                            borderRadius="full"
                            px={8}
                            fontFamily="Sora"
                        >
                            Get Started
                        </Button>
                        <Button
                            as={Link}
                            to="/scenarios"
                            size="lg"
                            variant="outline"
                            borderRadius="full"
                            px={8}
                            fontFamily="Sora"
                        >
                            Explore Scenarios
                        </Button>
                    </Flex>
                </Stack>
                {/* Hero Image */}
                <Box
                    flex={0.9}
                    width="100%"
                    position="relative"
                    display="flex"
                    justifyContent="center"
                    alignItems="center"
                    minH={{ base: "400px", md: "500px", lg: "560px" }}
                >
                    {/* Decorative background shape */}
                    <Box
                        position="absolute"
                        width={{ base: "80%", md: "75%" }}
                        height={{ base: "80%", md: "85%" }}
                        bg="brand.50"
                        borderRadius="3xl"
                        transform="rotate(4deg)"
                    />
                    {/* Main image */}
                    <Image
                        src={testImg}
                        position="relative"
                        width={{ base: "90%", md: "82%", lg: "88%" }}
                        maxW="520px"
                        objectFit="cover"
                        borderRadius="3xl"
                        boxShadow="lg"
                    />
                </Box>
            </Flex>
        </Box>
    );
}

export default Banner;
