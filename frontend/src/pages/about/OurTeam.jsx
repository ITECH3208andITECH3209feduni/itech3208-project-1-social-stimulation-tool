import { ourTeam } from "@/assets";
import { Box, Flex, Heading, Image, Text } from "@chakra-ui/react";

function OurTeam() {
    return (
        <Flex mt={50} px={"100px"} justify={"center"} alignItems={"center"} gap={8}>
            <Box
                flex={1}
                display="flex"
                alignItems="center"
                justifyContent="center"
                aspectRatio="13/9"
                overflow={"hidden"}
            >
                <Image
                    src={ourTeam}
                    w={"100%"}
                    maxW="100%"
                    maxH="100%"
                    objectFit="contain"
                    rounded={"md"}
                />
            </Box>
            <Box flex={1} fontFamily={"Sora"} fontWeight={"bold"} textAlign={"left"}>
                <Heading color={"brand.500"} fontSize={44}>
                    Our Team
                </Heading>
                <Text color={"navy.500"} fontSize={18}>
                    Scenari-Aid was developed through the collaborative efforts of educators,
                    researchers, and community contributors dedicated to creating meaningful
                    learning tools. Led by academic professionals from Federation University, the
                    project brings together expertise in technology, education, and social inclusion
                    to design authentic and impactful scenarios. Many of the scenarios feature real
                    people rather than actors, reflecting genuine situations and enhancing the
                    realism of the experience. The project continues to grow through community
                    involvement, with contributions in ideas, content, and support helping shape its
                    ongoing development.
                </Text>
            </Box>
        </Flex>
    );
}

export default OurTeam;
