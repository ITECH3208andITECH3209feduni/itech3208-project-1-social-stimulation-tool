import { Box, Button, Container, Grid, GridItem, Heading } from "@chakra-ui/react";
import Banner from "./Banner";
import VideoCarousel from "@/components/common/carousels/VideoCarousel";
import UsersFeedbackGrid from "@/components/common/grids/UsersFeedbackGrid";

function HomePage() {
    return (
        <div style={{ background: "white" }}>
            <Banner />
            <Container mt={"100px"} spaceY={10} background={"white"}>
                <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={48}>
                    NEW SCENARIOS
                </Heading>
                <VideoCarousel />
                <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={48}>
                    OUR USERS FEEDBACK
                </Heading>
                <Box spaceY={5}>
                    <UsersFeedbackGrid />
                    <Button size={"sm"} color={"white"} bg={"brand.500"} fontFamily={"Sora"}>
                        View All
                    </Button>
                </Box>
            </Container>
        </div>
    );
}

export default HomePage;
