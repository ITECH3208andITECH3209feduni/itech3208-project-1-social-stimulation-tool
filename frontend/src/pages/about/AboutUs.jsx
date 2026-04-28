import { aboutUs2 } from "@/assets";
import { Box, Flex, Heading, Image, Text } from "@chakra-ui/react";

function AboutUs() {
    return (
        <Flex mt={50} px={"100px"} justify={"center"} alignItems={"center"} gap={4}>
            <Box
                flex={1}
                display="flex"
                alignItems="center"
                justifyContent="center"
                aspectRatio="16/9"
                overflow={"hidden"}
            >
                <Image
                    src={aboutUs2}
                    w={"100%"}
                    maxW="100%"
                    maxH="100%"
                    objectFit="contain"
                    rounded={"md"}
                />
            </Box>
            <Box flex={1} fontFamily={"Sora"} fontWeight={"bold"} textAlign={"left"}>
                <Heading color={"brand.500"} fontSize={44}>
                    About Us
                </Heading>
                <Text color={"navy.500"} fontSize={18}>
                    Scenari-Aid is an interactive simulation platform developed to help individuals
                    practice real-life communication and social interactions in a safe and flexible
                    environment. Originally created to support people with speech and fluency
                    challenges, the system has evolved into a comprehensive tool used for improving
                    confidence, social skills, and everyday communication. With over 100 realistic
                    video-based scenarios, users can engage in guided interactions at their own
                    pace, allowing them to build practical skills through repeated practice and
                    reflection.
                </Text>
            </Box>
        </Flex>
    );
}

export default AboutUs;
