import { Card, Image, Text, HStack, Box, Button, Flex, Separator } from "@chakra-ui/react";
import { BsFileEarmarkBarGraph, BsPlayFill } from "react-icons/bs";
import { MdOutlineTimer } from "react-icons/md";
import { business } from "@/assets";

function TutorialCard() {
    return (
        <Card.Root overflow="hidden" borderColor="gray.500">
            <Box position="relative" w={"100%"} h={"600px"}>
                <Image
                    src={business}
                    w={"100%"}
                    h={"100%"}
                    objectFit={"cover"}
                />

                <Button
                    position="absolute"
                    top="50%"
                    left="50%"
                    transform="translate(-50%, -50%)"
                    w={"80px"}
                    h={"80px"}
                    rounded={"full"}
                    bg={"white"}
                    color={"navy.300"}
                    fontSize={"36px"}
                    display={"flex"}
                    alignItems={"center"}
                    justifyContent={"center"}
                    _hover={{
                        transform: "translate(-50%, -50%) scale(1.05)",
                    }}
                >
                    <BsPlayFill />
                </Button>
            </Box>

            <Card.Body>
                <Flex
                    w={"100%"}
                    justify={"space-between"}
                    alignItems={"center"}
                    gap={4}
                >
                    <Box>
                        <Card.Title
                            fontFamily={"Sora"}
                            fontSize={"24px"}
                            fontWeight={"bold"}
                        >
                            Prepare your interview with some basic steps
                        </Card.Title>

                        <HStack
                            mt={"12px"}
                            color={"gray.500"}
                            alignItems={"center"}
                            gap={2}
                        >
                            <HStack>
                                <BsFileEarmarkBarGraph />
                                <Text
                                    fontFamily={"Sora"}
                                    fontWeight={"medium"}
                                    fontSize={16}
                                >
                                    Business
                                </Text>
                            </HStack>

                            <Text color={"gray.400"}>|</Text>

                            <HStack>
                                <MdOutlineTimer />
                                <Text
                                    fontFamily={"Sora"}
                                    fontWeight={"medium"}
                                    fontSize={16}
                                >
                                    10 hours
                                </Text>
                            </HStack>
                        </HStack>
                    </Box>
                </Flex>
            </Card.Body>
        </Card.Root>
    );
}

export default TutorialCard;
