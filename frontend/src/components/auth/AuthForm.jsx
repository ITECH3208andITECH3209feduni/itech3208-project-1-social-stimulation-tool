import React, { useState } from "react";
import { Link } from "react-router-dom";
import { Box, Field, Flex, Heading, Input, Image, Button, Checkbox } from "@chakra-ui/react";
import PasswordInput from "./PasswordInput";
import SocialLogin from "./SocialLogin";
import { federationLogo } from "@/assets";

const LOGIN_FIELDS_NUMBER = 2;

function AuthForm({ fields, onSubmit }) {
    const isRegister = fields.length > LOGIN_FIELDS_NUMBER;

    const [input, setInput] = useState({});

    const handleInputChange = (key, value) => {
        setInput((prev) => ({ ...prev, [key]: value }));
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
                        <Field.Label>{field.label}</Field.Label>
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
                    </Field.Root>
                ))}

                {/* Agree to terms and conitions fields */}
                {isRegister && (
                    <Checkbox.Root
                        variant={"solid"}
                        color={"brand.500"}
                        onCheckedChange={(details) => handleInputChange("acceptedTerms", details.checked)}
                    >
                        <Checkbox.HiddenInput color="brand.500" />
                        <Checkbox.Control />
                        <Checkbox.Label color={"brand.500"}>
                            I agree to recieve terms and conditions from Federation University
                        </Checkbox.Label>
                    </Checkbox.Root>
                )}

                <Button w={"100%"} bg={"skyblue.500"} onClick={() => onSubmit?.(input)}>
                    {isRegister ? "Sign up" : "Sign in"}
                </Button>
                <SocialLogin isRegister={isRegister} />
            </Box>
        </Flex>
    );
}

export default AuthForm;
