import { aboutUs2, winNew } from "@/assets";
import { Box, Flex, Heading, Image, Text, VStack, ListItem, Link, AspectRatio, Icon, List } from "@chakra-ui/react";
import { FaCrown, FaPlayCircle } from "react-icons/fa";

function AboutUs() {
    return (
        <Box mt={50} maxW="7xl" mx="auto">
            {/* MARK: About Section */}
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
           
        </Box>
    );
}

export default AboutUs;
