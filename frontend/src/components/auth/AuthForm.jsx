import { Box, Field, Flex, Heading, Input, Image, Button } from "@chakra-ui/react";
import React from "react";
import { loginFields, registerFields } from "./authConfig";
import PasswordInput from "./PasswordInput";
import { Link } from "react-router-dom";
import { federationLogo } from "@/assets";
import SocialLogin from "./SocialLogin";

function AuthForm({ isRegister = false }) {
    const fields = isRegister ? registerFields : loginFields;
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
                            <PasswordInput placeholder={field.placeholder} />
                        ) : (
                            <Input
                                background="gray.100"
                                borderColor={"gray.400"}
                                type={field.type}
                                placeholder={field.placeholder}
                            />
                        )}
                    </Field.Root>
                ))}
                <Button w={"100%"} bg={"skyblue.500"}>
                    {isRegister ? "Sign up" : "Sign in"}
                </Button>
                <SocialLogin isRegister={isRegister} />
            </Box>
        </Flex>
    );
}

export default AuthForm;
