import { ourMission } from "@/assets";
import { Box, Grid, Heading, Image, Text } from "@chakra-ui/react";

function OurMission() {
    return (
        <Box>
            <Grid
                templateColumns={{
                    base: "1fr",
                    md: "1fr",
                    lg: "1fr 1fr",
                }}
                gap={{ base: 8, md: 10, lg: 14 }}
                alignItems="center"
            >
                <Box>
                    <Text
                        fontSize="sm"
                        fontWeight="600"
                        letterSpacing="wide"
                        textTransform="uppercase"
                        color="brand.500"
                        mb={3}
                    >
                        Why We Exist
                    </Text>

                    <Heading
                        fontFamily="Sora"
                        fontSize={{ base: "3xl", md: "4xl" }}
                        color="gray.800"
                        mb={6}
                    >
                        Our Mission
                    </Heading>

                    <Text
                        textAlign={"left"}
                        color="gray.600"
                        fontSize={{ base: "md", md: "lg" }}
                        lineHeight="1.8"
                    >
                        Our mission is to empower individuals by providing accessible and realistic
                        simulated scenarios that enhance communication, confidence, and
                        decision-making in real-world situations. We aim to support users from
                        diverse backgrounds—including those facing social anxiety, language
                        barriers, or employment challenges—by offering a platform where they can
                        practice without pressure.
                    </Text>

                    <Text
                        textAlign={"left"}
                        mt={5}
                        color="gray.600"
                        fontSize={{ base: "md", md: "lg" }}
                        lineHeight="1.8"
                    >
                        Through continuous learning and repetition, Scenari-Aid helps users develop
                        the skills needed to navigate interviews, social conversations, and everyday
                        interactions with greater ease and confidence.
                    </Text>
                </Box>

                <Box overflow="hidden" borderRadius="xl" bg="gray.50">
                    <Image
                        src={ourMission}
                        alt="Scenari-Aid mission"
                        w="100%"
                        h="100%"
                        maxH="500px"
                        objectFit="cover"
                    />
                </Box>
            </Grid>
        </Box>
    );
}

export default OurMission;
