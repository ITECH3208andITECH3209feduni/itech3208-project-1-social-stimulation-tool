import { Box, Button, Heading, Text } from "@chakra-ui/react";

function Contribution() {
    return (
        <Box
            py={{ base: 8, md: 12 }}
            px={{ base: 4, md: 8 }}
            borderTop="1px solid"
            borderBottom="1px solid"
            borderColor="gray.200"
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
                    Get Involved
                </Text>

                <Heading
                    fontFamily="Sora"
                    fontSize={{ base: "3xl", md: "4xl" }}
                    color="gray.800"
                    mb={5}
                >
                    Contribute
                </Heading>

                <Text color="gray.600" fontSize={{ base: "md", md: "lg" }} lineHeight="1.8" mb={7}>
                    Scenari-Aid is an organic community driven project and we welcome personal
                    contributions to the application in the form of ideas, video scenarios and
                    donations.
                </Text>

                <Button
                    as="a"
                    href="/contact"
                    bg="brand.500"
                    color="white"
                    size="lg"
                    _hover={{
                        opacity: 0.9,
                    }}
                >
                    Contact Grant Meredith
                </Button>
            </Box>
        </Box>
    );
}

export default Contribution;
