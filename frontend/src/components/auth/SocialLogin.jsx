import { Box, Button, Separator, Text } from "@chakra-ui/react";
import React from "react";
import { FcGoogle } from "react-icons/fc";
import { Link } from "react-router-dom";

function SocialLogin({ isRegister = false }) {
    return (
        <Box>
            <Separator />

            <Button w={"full"} color={"white"} bg={"dark.900"}>
                <FcGoogle />
                {isRegister ? "Or sign up with Google" : "Or sign in with Google"}
            </Button>

            <Text textAlign={"center"} mt={"2"}>
                {isRegister ? "Already had an account?" : "Don't have an account?"}{" "}
                <Link to={isRegister ? "/account/login" : "/account/register"}>
                    <Text as="span" color="navy.500">
                        {isRegister ? "Sign in now" : "Sign up now"}
                    </Text>
                </Link>
            </Text>
        </Box>
    );
}

export default SocialLogin;
