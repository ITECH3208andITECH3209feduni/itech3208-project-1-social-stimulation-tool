import { Toaster } from "@/components/ui/toaster";
import { Flex } from "@chakra-ui/react";
import React from "react";
import { Outlet } from "react-router-dom";

function LoginLayout() {
    return (
        <Flex justify={"center"} align={"center"}>
            <Outlet />
            <Toaster />
        </Flex>
    );
}

export default LoginLayout;
