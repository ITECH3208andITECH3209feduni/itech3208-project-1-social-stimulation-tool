import { Button, Field, Heading, Input, Box, Flex, Text, Image, HStack, VStack, Center } from "@chakra-ui/react";
import React from "react";
import { federationLogo } from "@/assets";

function LoginPage() {
    return (
        <Flex
        h={"100vh"}
            bgGradient="linear(to-b, dark.700, white)"
            justifyContent={"center"}
            alignItems={"center"}
        >
            <Box 
                w="360px"
                p="4"
                bg="dark.700"
                boxShadow="lg"
                textAlign="center"
                spaceY={4}
                rounded={"lg"}
            >
                {/* Icon */}
                <VStack >
                        <Image src={federationLogo} h={"50px"} />
                        {/* Title */}
                        <HStack justifyContent={"center"}>
                            <Heading color="dark.500" fontSize={25} >
                                Scenario
                            </Heading>
                            <Heading color="brand.500" fontSize={25} >
                                Aid
                            </Heading>
                        </HStack>
                </VStack>

                {/* Username */}
                <Field.Root>
                    <Input
                        placeholder="Username"
                        p={"4"}
                        bg="dark.800"
                        border="none"
                        _focus={{ bg: "whiteAlpha.50", boxShadow: "md" }}
                        color={"gray.500"}
                    />
                </Field.Root>

                {/* Password */}
                <Field.Root >
                    <Input
                        placeholder="Password"
                        p={"4"}
                        type="password"
                        bg="dark.800"
                        border="none"
                        _focus={{ bg: "whiteAlpha.50", boxShadow: "md" }}
                        color={"gray.500"}
                    />
                </Field.Root>

                <Button
                    w="100%"
                    borderRadius="full"
                    bg="brand.500"
                    color="white"
                    _hover={{ bg: "gray.800" }}
                >
                    Log in
                </Button>
            </Box>
        </Flex>
    );
}

export default LoginPage;
