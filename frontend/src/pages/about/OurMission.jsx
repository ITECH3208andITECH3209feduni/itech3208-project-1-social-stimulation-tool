import { ourMission } from "@/assets";
import { Box, Flex, Heading, Image, Text } from "@chakra-ui/react";

function OurMission() {
    return (
        <Flex
            mt={50}
            px={"100px"}
            justify={"center"}
            alignItems={"center"}
            direction={"row-reverse"}
            gap={4}
        >
            <Box
                flex={1}
                display="flex"
                alignItems="center"
                justifyContent="center"
                aspectRatio="13/9"
                overflow={"hidden"}
            >
                <Image
                    src={ourMission}
                    w={"100%"}
                    maxW="100%"
                    maxH="100%"
                    objectFit="contain"
                    rounded={"md"}
                />
            </Box>
            <Box flex={1} fontFamily={"Sora"} fontWeight={"bold"} textAlign={"left"}>
                <Heading color={"brand.500"} fontSize={44}>
                    Our Mission
                </Heading>
                <Text color={"navy.500"} fontSize={18}>
                    Our mission is to empower individuals by providing accessible and realistic
                    simulated scenarios that enhance communication, confidence, and decision-making
                    in real-world situations. We aim to support users from diverse
                    backgrounds—including those facing social anxiety, language barriers, or
                    employment challenges—by offering a platform where they can practice without
                    pressure. Through continuous learning and repetition, Scenari-Aid helps users
                    develop the skills needed to navigate interviews, social conversations, and
                    everyday interactions with greater ease and confidence.
                </Text>
            </Box>
        </Flex>
    );
}

export default OurMission;
