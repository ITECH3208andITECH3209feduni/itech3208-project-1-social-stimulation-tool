import { Container, GridItem, Heading, VStack, Grid } from "@chakra-ui/react";
import TutorialGrid from "@/components/common/grids/TutorialGrid";
import LevelFilter from "@/components/common/filters/LevelFilter";
import CategoryFilter from "@/components/common/filters/CategoryFilter";
import TutorialPagination from "@/components/common/paginations/TutorialPagination";

function TutorialPage() {
    return (
        <Container mt={"100px"} spaceY={"50px"}>
            <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={48}>
                Welcome to our tutorial
            </Heading>
            <VStack align={"start"} gap={8}>
                {/* Category Filter */}
                <CategoryFilter />

                {/* Level Filter */}
                <LevelFilter />
            </VStack>
            <TutorialGrid />
            <TutorialPagination />
        </Container>
    );
}

export default TutorialPage;
