import { aboutUs2 } from "@/assets";
import { Box, Heading, Image, Link, Text, VStack } from "@chakra-ui/react";

const GrantMeredithInfoUrl =
    "https://www.federation.edu.au/research/find-an-expert/grant-meredith/";

function AboutUs() {
    return (
        <Box>
            <VStack align="start" gap={6}>
                <Text
                    fontSize="sm"
                    fontWeight="600"
                    letterSpacing="wide"
                    textTransform="uppercase"
                    color="brand.500"
                >
                    About the Project
                </Text>

                <Heading
                    fontFamily="Sora"
                    fontSize={{ base: "3xl", md: "5xl" }}
                    lineHeight="1.1"
                    color="gray.800"
                >
                    About Scenari-Aid
                </Heading>

                <Text
                    textAlign={"left"}
                    fontSize={{ base: "md", md: "lg" }}
                    lineHeight="1.8"
                    color="gray.600"
                >
                    Scenari-Aid is an application designed by{" "}
                    <Link
                        color="brand.500"
                        fontWeight="600"
                        href={GrantMeredithInfoUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Grant Meredith
                    </Link>{" "}
                    who is a lecturer within the School of Science, Information Technology &
                    Engineering (SITE) at Federation University Australia. Grant leads the
                    Technologies for Empowering People for Participation in Society (TEPPS)
                    programme which conceive, designs and evaluate “assertive technologies” for
                    people at need.
                </Text>

                <Text
                    textAlign={"left"}
                    fontSize={{ base: "md", md: "lg" }}
                    lineHeight="1.8"
                    color="gray.600"
                >
                    Originally designed as a free DVD application for people who stutter and with
                    financial aid from the Helen Macpherson Smith Trust, over the past 2 years close
                    to 1,000 users have since benefited from Scenari-Aid for a range of diverse
                    reasons. These reasons have included fluency and speech therapy, stroke
                    recovery, literacy skills, social anxiety and empowering the long-term
                    unemployed.
                </Text>
            </VStack>

            {/* <Box mt={10} w="100%" overflow="hidden" borderRadius="xl">
                <Image src={aboutUs2} alt="Scenari-Aid" w="100%" maxH="500px" objectFit="cover" />
            </Box> */}
        </Box>
    );
}

export default AboutUs;
