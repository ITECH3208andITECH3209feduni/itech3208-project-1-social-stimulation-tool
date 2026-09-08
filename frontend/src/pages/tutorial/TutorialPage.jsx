import { Container, Heading } from "@chakra-ui/react";
import TutorialGrid from "@/components/common/grids/TutorialGrid";

function TutorialPage() {
    return (
        <Container mt={"100px"} spaceY={"50px"}>
            <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={48}>
                Welcome to our tutorial
            </Heading>
            <TutorialGrid />
        </Container>
    );
}

export default TutorialPage;
