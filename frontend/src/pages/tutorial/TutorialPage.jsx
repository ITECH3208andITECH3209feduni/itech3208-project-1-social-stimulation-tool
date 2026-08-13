import { Container, GridItem, Heading, VStack, Grid, Center } from "@chakra-ui/react";
import TutorialGrid from "@/components/common/grids/TutorialGrid";
import LevelFilter from "@/components/common/filters/LevelFilter";
import CategoryFilter from "@/components/common/filters/CategoryFilter";
import useAuthStore from "@/hooks/stores/useAuthStore";
import RequiredAuth from "@/components/auth/RequiredAuth";

function TutorialPage() {
    const { accessToken } = useAuthStore();

    return (
        <Container mt={"100px"} spaceY={"50px"}>
            {accessToken ? (
                <>
                    <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={48}>
                        Welcome to our tutorial
                    </Heading>
                    
                    <TutorialGrid />
                    
                </>
            ) : (
                <RequiredAuth />
            )}
        </Container>
    );
}

export default TutorialPage;
