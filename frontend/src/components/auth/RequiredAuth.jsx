import { requiredAuth } from "@/assets";
import { Button, ButtonGroup, Center, EmptyState, VStack, Image } from "@chakra-ui/react";
import { useNavigate } from "react-router-dom";

function RequiredAuth() {
    const navigate = useNavigate();

    const handleGotoLogin = () => navigate("/account/login");

    return (
        <EmptyState.Root size={"lg"}>
            <EmptyState.Content>
                <VStack textAlign={"center"}>
                    <Image src={requiredAuth} w={150} h={150} objectFit={"cover"} />
                    <EmptyState.Title> Sign in to access Scenari-Aid</EmptyState.Title>
                    <EmptyState.Description>
                        Log in to unlock personalized scenarios, track your progress, and access all
                        available features.
                    </EmptyState.Description>
                </VStack>
                <ButtonGroup>
                    <Button color={"white"} bg={"brand.500"} onClick={handleGotoLogin}>
                        Login
                    </Button>
                </ButtonGroup>
            </EmptyState.Content>
        </EmptyState.Root>
    );
}

export default RequiredAuth;
