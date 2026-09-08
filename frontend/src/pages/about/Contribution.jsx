import { Box, Heading, Text, Link, } from "@chakra-ui/react";

function Contribution() {
    return (
        <Box mt={16} fontFamily={"Sora"}>
            <Heading color={"brand.500"} fontSize={32} mb={6}>
                Contribute
            </Heading>
            <Text color={"navy.500"} fontSize={18} mb={4}>
                Scenari-Aid is an organic community driven project and we welcome personal contributions to the application in the form of ideas, video scenarios and donations. If you are interested in contributing to Scenari-Aid in any form please contact <Link color="brand.500" fontWeight="bold" href="/contact">Grant Meredith</Link> via{" "}
                <Link color="brand.500" fontWeight="bold" href="/contact">this link</Link>.
            </Text>
        </Box>
    );
}

export default Contribution;