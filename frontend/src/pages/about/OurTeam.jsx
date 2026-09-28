import { ourTeam } from "@/assets";
import { Box, Grid, Heading, Image, Text } from "@chakra-ui/react";

function OurTeam() {
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
                        People Behind Scenari-Aid
                    </Text>

                    <Heading
                        fontFamily="Sora"
                        fontSize={{ base: "3xl", md: "4xl" }}
                        color="gray.800"
                        mb={6}
                    >
                        Our Team
                    </Heading>

                    <Text
                        textAlign={"left"}
                        color="gray.600"
                        fontSize={{ base: "md", md: "lg" }}
                        lineHeight="1.8"
                    >
                        Scenari-Aid was developed through the collaborative efforts of educators,
                        researchers, and community contributors dedicated to creating meaningful
                        learning tools. Led by academic professionals from Federation University,
                        the project brings together expertise in technology, education, and social
                        inclusion.
                    </Text>

                    <Text
                        textAlign={"left"}
                        mt={5}
                        color="gray.600"
                        fontSize={{ base: "md", md: "lg" }}
                        lineHeight="1.8"
                    >
                        Many of the scenarios feature real people rather than actors, reflecting
                        genuine situations and enhancing the realism of the experience. The project
                        continues to grow through community involvement, with contributions in
                        ideas, content, and support helping shape its ongoing development.
                    </Text>
                </Box>

                <Box overflow="hidden" borderRadius="xl" bg="gray.50">
                    <Image
                        src={ourTeam}
                        alt="Scenari-Aid team"
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

export default OurTeam;
