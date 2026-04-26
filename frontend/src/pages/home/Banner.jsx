import { landingPage, star2 } from "@/assets";
import { Box, Button, Flex, Heading, HStack, Image, Text } from "@chakra-ui/react";
import GeneralInfor from "./GeneralInfor";

function Banner() {
    return (
        <Box background="gray.300" width="100%">
            <Flex gap={30}>
                {/* Section1: Scenari-Aid Simulated Scenario System */}
                <Flex justify={"center"} p="50px" flex={1} direction={"column"} gap={30}>
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
                    <Button
                        width={210}
                        height={45}
                        rounded={"full"}
                        size="xl"
                        background="brand.500"
                        fontFamily="Sora"
                    >
                        Signup
                    </Button>

                    {/* Section2: General Information (e.g. active users, videos, happy customers)*/}
                    <GeneralInfor />
                </Flex>

                {/* Section3: Landing Image */}
                <Flex flex={1} >
                    <Image src={landingPage} fit={"cover"} w="full" h="100%" />
                </Flex>
            </Flex>
        </Box>
    );
}

export default Banner;
