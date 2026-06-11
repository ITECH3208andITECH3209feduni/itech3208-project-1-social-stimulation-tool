import { Button, Field, Heading, Input, Box, Flex, Image, HStack, VStack } from "@chakra-ui/react";
import React from "react";
import { federationLogo } from "@/assets";
import { useState } from "react";
import useLogin from "@/hooks/common/useLogin";
import { toaster } from "@/components/ui/toaster";
import { useNavigate } from "react-router-dom";

function LoginPage() {
    const navigate = useNavigate();
    const { loading, login } = useLogin();
    const [inputs, setInputs] = useState({
        username: "",
        password: "",
    });

    const handleInputsChange = (key, value) => setInputs((prev) => ({ ...prev, [key]: value }));

    const handleSubmit = async () => {
        const payload = {
            username: inputs.username,
            password: inputs.password,
        };
        await login(payload, {
            onSuccess: (_, msg) => {
                toaster.create({
                    description: msg,
                    type: "success",
                });
                setTimeout(() => {
                    navigate("/admin/dashboard");
                }, 300);
            },
            onError: (msg) => {
                toaster.create({
                    description: msg,
                    type: "error",
                });
            },
        });
    };

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
                <VStack>
                    <Image src={federationLogo} h={"50px"} />
                    {/* Title */}
                    <HStack justifyContent={"center"}>
                        <Heading color="dark.500" fontSize={25}>
                            Scenario
                        </Heading>
                        <Heading color="brand.500" fontSize={25}>
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
                        value={inputs.username}
                        name="username"
                        onChange={(e) => handleInputsChange("username", e.target.value)}
                    />
                </Field.Root>

                {/* Password */}
                <Field.Root>
                    <Input
                        placeholder="Password"
                        p={"4"}
                        type="password"
                        bg="dark.800"
                        border="none"
                        _focus={{ bg: "whiteAlpha.50", boxShadow: "md" }}
                        color={"gray.500"}
                        value={inputs.password}
                        name="password"
                        onChange={(e) => handleInputsChange("password", e.target.value)}
                    />
                </Field.Root>

                <Button
                    w="100%"
                    borderRadius="full"
                    bg="brand.500"
                    color="white"
                    loading={loading}
                    loadingText="Logging in..."
                    onClick={handleSubmit}
                >
                    Log in
                </Button>
            </Box>
        </Flex>
    );
}

export default LoginPage;
