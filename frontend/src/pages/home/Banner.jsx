import { landingPage, star2, testImg } from "@/assets";
import { Box, Button, Flex, Heading, HStack, Image, Text } from "@chakra-ui/react";
import GeneralInfor from "./GeneralInfor";

function Banner() {
    return (
        <Box width="100%" mt={50}>
            <Flex gap={30} align={"center"}>
                {/* Section1: Scenari-Aid Simulated Scenario System */}
                <Flex justify={"center"} flex={1} direction={"column"} gap={30}>
                    <Heading
                        position={"relative"}
                        color="brand.500"
                        fontFamily="Sora"
                        fontSize={64}
                        textAlign={"left"}
                    >
                        <HStack>
                            <Text>Scenari-Aid</Text>
                            <Image src={star2} />
                        </HStack>
                        <HStack>
                            <Image src={star2} />
                            <Text>Simulated</Text>
                        </HStack>

                        <Text>Scenario System</Text>
                    </Heading>
                    <Text textAlign={"left"} fontFamily="Sora">
                        Welcome to Scenari-Aid — Your Practice Space for Real-Life Skills Sharpen
                        your social, interview, and communication skills through realistic,
                        interactive scenarios. With 100+ recorded video situations, you can
                        practice, build confidence, and improve at your own pace. Signup now to get
                        started.
                    </Text>
                </Flex>

                {/* Section3: Landing Image */}
                <Flex flex={1}>
                    <Image src={testImg} fit={"cover"} w="full" h="full" rounded={"md"}/>
                </Flex>
            </Flex>
        </Box>
    );
}

export default Banner;
