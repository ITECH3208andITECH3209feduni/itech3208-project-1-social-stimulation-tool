import { winNew } from "@/assets";
import { AspectRatio, Box, Grid, Heading, Link, Text } from "@chakra-ui/react";

function WINNews() {
    return (
        <Box>
            <Box mb={8}>
                <Text
                    fontSize="sm"
                    fontWeight="600"
                    letterSpacing="wide"
                    textTransform="uppercase"
                    color="brand.500"
                    mb={3}
                >
                    Media Coverage
                </Text>

                <Heading
                    fontFamily="Sora"
                    fontSize={{ base: "3xl", md: "4xl" }}
                    color="gray.800"
                    mb={5}
                >
                    WIN News Feature
                </Heading>

                <Text
                    maxW="800px"
                    color="gray.600"
                    fontSize={{ base: "md", md: "lg" }}
                    lineHeight="1.8"
                >
                    A WIN News report featuring the Scenari-Aid DVD and the development of the
                    project.
                </Text>
            </Box>

            <Grid
                templateColumns={{
                    base: "1fr",
                    lg: "1fr 1fr",
                }}
                gap={{ base: 8, lg: 12 }}
                alignItems="center"
            >
                <AspectRatio
                    ratio={16 / 9}
                    w="100%"
                    maxW="900px"
                    borderRadius="xl"
                    overflow="hidden"
                    bg="black"
                >
                    <Box as="video" controls src={winNew} w="100%" h="100%" objectFit="contain" />
                </AspectRatio>

                <Box textAlign={"left"} mt={8}>
                    <Text color="gray.600" fontSize={{ base: "md", md: "lg" }} lineHeight="1.8">
                        Scenari-Aid is now a free website containing over 100 staged streaming video
                        scenarios for people to work through in their own time, pace and purpose of
                        need. We wholeheartedly thank the Telematics Trust for financially backing
                        this phase of the project.
                    </Text>

                    <Text
                        mt={5}
                        color="gray.600"
                        fontSize={{ base: "md", md: "lg" }}
                        lineHeight="1.8"
                    >
                        The 100 initial scenarios were shot within Ballarat, Victoria, Australia.
                        Everybody you see within the scenarios are volunteers and nobody is an
                        actor.
                    </Text>
                </Box>
            </Grid>

            <Box
                mt={8}
                p={6}
                borderLeft="3px solid"
                borderLeftColor="brand.500"
                borderRight="1px solid"
                borderRightColor="gray.500"
                borderTop="1px solid"
                borderTopColor="gray.500"
                borderBottom="1px solid"
                borderBottomColor="gray.500"
                bg="gray.50"
                borderRadius="md"
            >
                <Text color="gray.800" lineHeight="1.7">
                    You will need to register to access the scenarios.{" "}
                    <Link color="brand.500" fontWeight="600" href="/account/register">
                        Register here
                    </Link>
                    . If you have any questions or suggestions, please{" "}
                    <Link color="brand.500" fontWeight="600" href="/contact">
                        contact us
                    </Link>
                    .
                </Text>
            </Box>
        </Box>
    );
}

export default WINNews;
