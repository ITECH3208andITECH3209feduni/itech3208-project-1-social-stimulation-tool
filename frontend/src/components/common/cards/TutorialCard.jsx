import { Card, Image, Text, HStack, Box, Button, Flex, Separator } from "@chakra-ui/react";
import { BsFileEarmarkBarGraph, BsGraphUp } from "react-icons/bs";
import { MdOutlineTimer } from "react-icons/md";
import { business } from "@/assets";

function TutorialCard() {
    return (
        <Card.Root overflow="hidden" borderColor="gray.500">
            <Image src={business} w={"100%"} h={"auto"} objectFit={"cover"} />
            <Box>
                <Card.Body>
                    <HStack color={"gray.500"} alignItems={"center"}>
                        <BsFileEarmarkBarGraph />
                        <Text fontFamily={"Sora"} fontWeight={"medium"} fontSize={16}>
                            Business
                        </Text>
                    </HStack>
                    <Card.Title mt={"10px"}>
                        Prepare your interview with some basic steps
                    </Card.Title>
                    <Card.Description>
                        Learn the fundamentals of writing your résume with Canva tool to build a
                        good sense of job interviews
                    </Card.Description>
                    <Separator
                        mt={4}
                        borderColor="gray.500"
                        orientation={"horizontal"}
                        variant="solid"
                        size={"md"}
                        w={"100%"}
                    />
                </Card.Body>
                <Card.Footer>
                    <Flex w={"100%"} justify={"space-between"} alignItems={"center"}>
                        <HStack
                            color={"gray.500"}
                            fontFamily={"Sora"}
                            fontWeight={"medium"}
                            fontSize={16}
                        >
                            <MdOutlineTimer />
                            10 hours
                        </HStack>
                        <Button
                            color={"white"}
                            bg={"navy.300"}
                            fontFamily={"Sora"}
                            fontWeight={"medium"}
                            rounded={"full"}
                        >
                            View <BsGraphUp />
                        </Button>
                    </Flex>
                </Card.Footer>
            </Box>
        </Card.Root>
    );
}

export default TutorialCard;
