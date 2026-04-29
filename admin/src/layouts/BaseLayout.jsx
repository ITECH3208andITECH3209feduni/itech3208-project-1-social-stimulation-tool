import { Container } from "@chakra-ui/react";
import React from "react";
import { Outlet } from "react-router-dom";

function BaseLayout() {
    return (
        <Container>
            <Outlet />
        </Container>
    );
}

export default BaseLayout;
