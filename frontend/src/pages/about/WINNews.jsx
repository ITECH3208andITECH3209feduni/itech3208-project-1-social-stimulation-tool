import { aboutUs2, winNew } from "@/assets";
import { Box, Flex, Heading, Image, Text, VStack, ListItem, Link, AspectRatio, Icon, List } from "@chakra-ui/react";
import { FaCrown, FaPlayCircle } from "react-icons/fa";

function WINNews() {
  return (
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
  )
}

export default WINNews
