import React, { useState } from "react";
import { Link } from "react-router-dom";
import { Box, Field, Flex, Heading, Input, Image, Button, Checkbox, Text } from "@chakra-ui/react";
import PasswordInput from "./PasswordInput";
import SocialLogin from "./SocialLogin";
import { federationLogo } from "@/assets";
import { toaster } from "@/components/ui/toaster";

const LOGIN_FIELDS_NUMBER = 2;

function AuthForm({ fields, onSubmit }) {
    const isRegister = fields.length > LOGIN_FIELDS_NUMBER;

    const [input, setInput] = useState({});

    const handleInputChange = (key, value) => {
        setInput((prev) => ({ ...prev, [key]: value }));
    };

    const passwordValue = input.password || "";
    const passwordRequirements = [
        { id: 1, label: "at least 8 characters", isValid: passwordValue.length >= 8 },
        { id: 2, label: "at least one uppercase letter", isValid: /[A-Z]/.test(passwordValue) },
        { id: 3, label: "at least one lowercase letter", isValid: /[a-z]/.test(passwordValue) },
        { id: 4, label: "at least one number", isValid: /\d/.test(passwordValue) },
        { id: 5, label: "at least one special character", isValid: /[^A-Za-z0-9]/.test(passwordValue) }
    ];

    const handleSubmit = () => {
        if (isRegister) {
            const missing = passwordRequirements.filter((req) => !req.isValid);

            if (missing.length > 0) {
                const missingLabels = missing.map((req) => req.label);
                let errorMessage = "";

                if (missingLabels.length === 1) {
                    errorMessage = `Password must contain ${missingLabels[0]}.`;
                } else {
                    const lastRequirement = missingLabels.pop();
                    errorMessage = `Password must contain ${missingLabels.join(", ")} and ${lastRequirement}.`;
                }

                toaster.create({
                    description: errorMessage,
                    type: "error",
                    duration: 4000,
                    placement: "top-end",
                });
                return;
            }
        }

        onSubmit?.(input);
    };

    return (
        <Flex
            position={"relative"}
            w={"100%"}
            h={"100%"}
            p="8px"
            justify={"center"}
            alignItems={"center"}
            bg={"white"}
        >
            <Box width="100%" p="8px" display="flex" flexDir={"column"} spaceY={"4"}>
                <Link to="/">
                    <Image
                        src={federationLogo}
                        alt="Federation University"
                        h="60px"
                        cursor={"pointer"}
                    />
                </Link>
                <Heading color={"black"} mt={"4"}>
                    {isRegister ? "Welcome to our Scenario aid system!" : "Nice to see you again!"}
                </Heading>
                {fields.map((field) => (
                    <Field.Root key={field.label}>
                        <Field.Label>
                            {field.label} <Text as="span" color="red.500">*</Text>
                        </Field.Label>
                        {field.type === "password" ? (
                            <PasswordInput
                                placeholder={field.placeholder}
                                onChange={(e) => handleInputChange(field.name, e.target.value)}
                            />
                        ) : (
                            <Input
                                color={"dark.900"}
                                background="gray.100"
                                borderColor={"gray.400"}
                                type={field.type}
                                placeholder={field.placeholder}
                                name={field.name}
                                onChange={(e) => handleInputChange(field.name, e.target.value)}
                            />
                        )}
                        
                        {/* Password Requirements Box */}
                        {isRegister && field.name === "password" && (
                            <Box mt={2} p={3} bg="gray.50" borderRadius="md" borderWidth="1px" w="100%">
                                {passwordRequirements.map((req) => (
                                    <Flex 
                                        key={req.id} 
                                        align="center" 
                                        color={req.isValid ? "green.600" : "red.500"} 
                                        fontSize="sm" 
                                        mb={1}
                                    >
                                        <Box as="span" mr={2} fontWeight="bold">
                                            {req.isValid ? "✓" : "✗"}
                                        </Box>
                                        <Text capitalized={false}>
                                            {req.label.charAt(0).toUpperCase() + req.label.slice(1)}
                                        </Text>
                                    </Flex>
                                ))}
                            </Box>
                        )}
                    </Field.Root>
                ))}

                {/* Agree to terms and conditions fields */}
                {isRegister && (
                    <Checkbox.Root
                        variant={"solid"}
                        color={"brand.500"}
                        onCheckedChange={(details) => handleInputChange("acceptedTerms", details.checked)}
                    >
                        <Checkbox.HiddenInput color="brand.500" />
                        <Checkbox.Control />
                        <Checkbox.Label color={"brand.500"}>
                            I agree to receive terms and conditions from Federation University <Text as="span" color="red.500">*</Text>
                        </Checkbox.Label>
                    </Checkbox.Root>
                )}

                <Button w={"100%"} bg={"skyblue.500"} onClick={handleSubmit}>
                    {isRegister ? "Sign up" : "Sign in"}
                </Button>
                <SocialLogin isRegister={isRegister} />
            </Box>
        </Flex>
    );
}

export default AuthForm;