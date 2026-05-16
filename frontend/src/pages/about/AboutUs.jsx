import { aboutUs2, winNew } from "@/assets";
import { Box, Flex, Heading, Image, Text, VStack, ListItem, Link, AspectRatio, Icon, List } from "@chakra-ui/react";
import { FaCrown, FaPlayCircle } from "react-icons/fa";

function AboutUs() {
    return (
        <Box mt={50} px={{ base: 4, md: "100px" }} maxW="7xl" mx="auto">
            {/* Crowdfunding Success Highlight */}
            <Flex
                direction="column"
                alignItems="center"
                justifyContent="center"
                textAlign="center"
                rounded="xl"
                mb={12}
                py={8}
                px={6}
                bg="green.50"
                color="green.800"
                border="1px solid"
                borderColor="green.200"
                boxShadow="md"
            >
                <Icon as={FaCrown} boxSize="40px" color="yellow.500" />
                <Heading mt={4} mb={2} fontSize="2xl" fontFamily="Sora" color={"brand.500"}>
                    Crowdfunding Success!
                </Heading>
                <Text maxWidth="2xl" fontSize="lg">
                    Thank you to our amazing community! With your incredible support, we've successfully reached our crowdfunding goals. Your contributions make it possible for us to keep Scenari-Aid a free and growing resource for everyone.
                </Text>
            </Flex>

            <Flex direction={{ base: "column", lg: "row" }} gap={10} alignItems="flex-start">
                <Box flex={1} fontFamily={"Sora"} textAlign={"left"}>
                    <Heading color={"brand.500"} fontSize={{ base: 36, md: 44 }} mb={6}>
                        About Scenari-Aid
                    </Heading>

                    <VStack spacing={5} align="start" color={"navy.500"} fontSize={18}>
                        <Text>
                            Scenari-Aid is an application designed by <Link color="brand.500" fontWeight="bold" href="/contact">Grant Meredith</Link> who is a lecturer within the School of Science, Information Technology & Engineering (SITE) at Federation University Australia. Grant leads the Technologies for Empowering People for Participation in Society (TEPPS) programme which conceive, designs and evaluate “assertive technologies” for people at need.
                        </Text>

                        <Text>
                            Originally designed as a free DVD application for people who stutter and with financial aid from the Helen Macpherson Smith Trust, over the past 2 years close to 1,000 users have since benefited from the Scenari-Aid for a range of diverse reasons. These reasons have included fluency and speech therapy for people, stroke recovery, literacy skills for people from non-English speaking backgrounds, addressing social anxiety issues and the empowering the long term unemployed.
                        </Text>
                    </VStack>
                </Box>

                <Box
                    flex={1}
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    w="100%"
                    rounded="xl"
                    overflow={"hidden"}
                    boxShadow="2xl"
                >
                    <Image
                        src={aboutUs2}
                        w={"100%"}
                        objectFit="cover"
                        alt="About Scenari-Aid"
                    />
                </Box>
            </Flex>

            <Box mt={16} fontFamily={"Sora"} color={"navy.500"} fontSize={18}>
                <VStack spacing={6} align="start">
                    <Box w="100%" bg="gray.50" p={8} rounded="xl" borderLeft="4px solid" borderColor="brand.500">
                        <Flex align="center" gap={4} mb={4}>
                            <Icon as={FaPlayCircle} boxSize={8} color="red.500" />
                            <Heading size="md" color="brand.500">WIN News Feature</Heading>
                        </Flex>
                        <Text>
                            This Video is a news report of the Scenari-Aid DVD by Australia's National WIN News.
                        </Text>
                        <AspectRatio maxW="600px" w="100%" ratio={16 / 9} mt={4} rounded="md" overflow="hidden" bg="black">
                            <Box as="video" controls src={winNew} w="100%" h="100%" objectFit="contain" />
                        </AspectRatio>
                    </Box>
                    <Text>
                        Scenari-Aid is now a free website containing over 100 staged streaming video scenarios for people to work through in their own time, pace and purpose of need. We wholeheartedly thank the Telematics Trust for financially backing this phase of the project. The 100 initial scenarios were shot within Ballarat, Victoria, Australia. Everybody you see within the scenarios are volunteers and nobody is an actor. Most of the people you see are authentic within their scenario.
                    </Text>

                    <Text>
                        These scenarios offer the user a perfect platform to work through in regards to their purpose and need. Each scenario is comprised of a number of steps in which the user can pause at any time and choose to respond in a time and manner that best fits their situation, style & needs.
                    </Text>

                    <Text bg="blue.50" p={6} rounded="lg" w="100%" border="1px solid" borderColor="blue.100">
                        You will need to register to be able to access the scenarios. Registration is available through{" "}
                        <Link color="brand.500" fontWeight="bold" href="/account/register">this link</Link>. If you have any questions or suggestions about Scenari-Aid please do not hesitate to <Link color="brand.500" fontWeight="bold" href="/contact">contact us</Link>.
                    </Text>
                </VStack>
            </Box>

            <Box mt={16} fontFamily={"Sora"}>
                <Heading color={"brand.500"} fontSize={32} mb={6}>
                    Contribute
                </Heading>
                <Text color={"navy.500"} fontSize={18} mb={4}>
                    Scenari-Aid is an organic community driven project and we welcome personal contributions to the application in the form of ideas, video scenarios and donations. If you are interested in contributing to Scenari-Aid in any form please contact <Link color="brand.500" fontWeight="bold" href="/contact">Grant Meredith</Link> via{" "}
                    <Link color="brand.500" fontWeight="bold" href="/contact">this link</Link>.
                </Text>
            </Box>
        </Box>
    );
}

export default AboutUs;
