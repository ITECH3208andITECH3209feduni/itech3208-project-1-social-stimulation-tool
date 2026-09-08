import { Flex, Heading, Text, Icon } from "@chakra-ui/react";
import { FaCrown } from "react-icons/fa";

function Crowdfunding() {
  return (
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
  )
}

export default Crowdfunding
